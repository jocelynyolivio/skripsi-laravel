<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Pricelist;
use App\Models\Procedure;
use App\Models\Receivable;
use App\Models\Transaction;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\MedicalRecord;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function create(Request $request, $medicalRecordId = null)
    {
        $medicalRecord = null;
        $proceduresWithPrices = [];
        $totalAmount = 0;
        $users = $medicalRecord ? null : User::all();

        $cashAccounts = \App\Models\ChartOfAccount::where('type', 'asset')
            ->where(function ($query) {
                $query->where('name', 'LIKE', '%Kas%')
                    ->orWhere('name', 'LIKE', '%Bank%')
                    ->orWhere('name', 'LIKE', '%Petty Cash%');
            })
            ->get();


        if (!$medicalRecordId) {
            return redirect()->route('dashboard.transactions.selectMedicalRecord');
        }

        if ($medicalRecordId) {
            $medicalRecord = MedicalRecord::with([
                'procedures'
            ])->findOrFail(($medicalRecordId));

            if ($medicalRecord->procedures->isEmpty()) {
                return redirect()->back()->with('error', 'Transaction Failed. The doctor has not added any procedures to this medical record.');
            }

            $medicalRecord = MedicalRecord::with(['procedures'])
                ->findOrFail($medicalRecordId);

            $vouchers = Patient::where('id', $medicalRecord->patient->id)
                ->where('birthday_voucher_used', 0)
                ->select('birthday_voucher_code')
                ->get();

            $procedureCounts = [];

            foreach ($medicalRecord->procedures as $procedure) {
                $procedureCounts[$procedure->id] = ($procedureCounts[$procedure->id] ?? 0) + 1;
            }

            foreach ($procedureCounts as $procedureId => $quantity) {
                $procedure = Procedure::find($procedureId);
                $basePrice = Pricelist::where('procedure_id', $procedureId)
                    ->orderBy('effective_date', 'desc')
                    ->value('price') ?? 0;

                $promoPrice = Pricelist::where('procedure_id', $procedureId)
                    ->where('is_promo', 1)
                    ->orderBy('effective_date', 'desc')
                    ->value('price') ?? null;

                $proceduresWithPrices[] = [
                    'procedure' => $procedure,
                    'basePrice' => $basePrice,
                    'promoPrice' => $promoPrice,
                    'quantity' => $quantity,
                ];

                $totalAmount += $basePrice * $quantity;
            }
        } else {
            $proceduresWithPrices = Procedure::all()->map(function ($procedure) {
                return [
                    'procedure' => $procedure,
                    'basePrice' => $basePrice ?? 0,
                    'promoPrice' => $promoPrice ?? null,
                ];
            });
        }
        return view('dashboard.transactions.create', [
            'title' => 'Create Transaction',
            'medicalRecord' => $medicalRecord,
            'proceduresWithPrices' => $proceduresWithPrices,
            'totalAmount' => $totalAmount,
            'users' => $users,
            'cashAccounts' => $cashAccounts,
            'vouchers' => $vouchers,
        ]);
    }

    public function createWithoutMedicalRecord()
    {
        $vouchers = Patient::where('birthday_voucher_used', 0)
            ->whereNotNull('birthday_voucher_code')
            ->select('birthday_voucher_code')
            ->get();


        $proceduresWithPrices = Procedure::all()->map(function ($procedure) {
            return [
                'procedure' => $procedure,

                'basePrice' => $procedure->pricelists()->latest('effective_date')->value('price') ?? 0,

                'promoPrice' => $procedure->pricelists()->where('is_promo', 1)->latest('effective_date')->value('price') ?? null,

            ];
        });

        $cashAccounts = \App\Models\ChartOfAccount::where('type', 'asset')
            ->where(function ($query) {
                $query->where('name', 'LIKE', '%Kas%')
                    ->orWhere('name', 'LIKE', '%Bank%')
                    ->orWhere('name', 'LIKE', '%Petty Cash%');
            })
            ->get();


        $patients = Patient::all(); // Ambil daftar pengguna

        return view('dashboard.transactions.create_without_medical_record', [
            'title' => 'Create Transaction Without Medical Record',
            'proceduresWithPrices' => $proceduresWithPrices,
            'patients' => $patients,
            'cashAccounts' => $cashAccounts,
            'vouchers' => $vouchers
        ]);
    }
    public function store(Request $request)
    {

        try {
            $updated_by = auth()->id();
            // Validasi input
            $validated = $request->validate([
                'medical_record_id' => 'nullable|exists:medical_records,id',
                'user_id' => 'required|exists:users,id',
                'admin_id' => 'required|exists:users,id',
                'amount' => 'required|array',
                'amount.*' => 'numeric|min:0',
                'discount_final' => 'required|array',
                'discount_final.*' => 'numeric|min:0',
                'payments' => 'nullable|array',
                'payments.*.coa_id' => 'required|exists:chart_of_accounts,id', // coa
                'payments.*.method' => 'required',
                'payments.*.amount' => 'required|numeric|min:0',
                'payments.*.notes' => 'nullable|string',
                'voucher' => 'nullable|string'
            ]);

            // dd($validated);

            // LANGKAH 1: Inisialisasi semua variabel total yang kita butuhkan
            $grossTotalAmount = 0; // Total sebelum diskon (untuk sisi Kredit Pendapatan)
            $totalDiscountAmount = 0; // Total semua diskon (untuk sisi Debit Diskon)
            $netTotalAmount = 0; // Total setelah diskon (untuk menentukan piutang)
            $itemsData = [];
            $doctorId = null;
            $revenuePercentage = 0;
            $totalRevenueAmount = 0;

            $medicalRecord = MedicalRecord::with('procedures')->find($validated['medical_record_id']);
            $totalAmount = 0;
            $itemsData = [];

            $doctorId = null;
            $revenuePercentage = 0;
            $totalRevenueAmount = 0;

            // dd('initialize');

            // Hitung Total Amount dari Procedures
            if ($medicalRecord) {
                $procedureCounts = [];
                foreach ($medicalRecord->procedures as $procedure) {
                    $procedureCounts[$procedure->id] = ($procedureCounts[$procedure->id] ?? 0) + 1;
                }

                $doctorId = $medicalRecord->doctor_id;
                $doctorRole = User::where('id', $doctorId)->value('role_id');
                $revenuePercentage = ($doctorRole == 2) ? 35 : 30;

                foreach ($procedureCounts as $procedureId => $quantity) {
                    $unitPrice = $validated['amount'][$procedureId] ?? 0;
                    $discount = $validated['discount_final'][$procedureId] ?? 0;
                    $totalPrice = $unitPrice * $quantity;
                    $finalPrice = max($totalPrice - $discount, 0);
                    $revenueAmount = $finalPrice * ($revenuePercentage / 100);

                    // Akumulasi total-total yang dibutuhkan
                    $grossTotalAmount += $totalPrice;
                    $totalDiscountAmount += $discount;
                    $netTotalAmount += $finalPrice;

                    $totalRevenueAmount += $revenueAmount;

                    $itemsData[] = [
                        'procedure_id' => $procedureId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'discount' => $discount,
                        'final_price' => $finalPrice,
                    ];

                    $totalAmount += $finalPrice;
                }
            }

            // dd($netTotalAmount);

            // Buat transaksi baru
            $transaction = Transaction::create([
                'medical_record_id' => $validated['medical_record_id'],
                'user_id' => $validated['user_id'],
                'admin_id' => $validated['admin_id'],
                'total_amount' => $netTotalAmount,
                'status' => 'belum lunas',
                'doctor_id' => $doctorId,
                'revenue_percentage' => $revenuePercentage,
                'revenue_amount' => $totalRevenueAmount,
                'updated_by' => $updated_by,
                'birthday_voucher' => $validated['voucher']
            ]);

            // dd('had');

            // Simpan Item Transaksi
            foreach ($itemsData as $data) {
                $transaction->items()->create($data);
            }

            // dd('saved');

            // Simpan Payments
            $totalPayments = 0;
            if (!empty($validated['payments'])) {
                foreach ($validated['payments'] as $paymentData) {
                    $transaction->payments()->create([
                        'payment_date' => now(),
                        'amount' => $paymentData['amount'],
                        'payment_method' => $paymentData['method'],
                        'notes' => $paymentData['notes'] ?? null,
                        'coa_id' => $paymentData['coa_id']
                    ]);
                    $totalPayments += $paymentData['amount'];
                }
            }

            $remainingAmount = $totalAmount - $totalPayments;

            // Update Status Transaksi
            $transaction->status = ($remainingAmount > 0) ? 'belum lunas' : 'lunas';
            $transaction->save();

            $medicalRecord = MedicalRecord::with('patient')->find($validated['medical_record_id']);

            if ($medicalRecord && $medicalRecord->patient) {
                Patient::where('id', $medicalRecord->patient->id)
                    ->update(['birthday_voucher_used' => 1]);
            }

            // dd('msk');


            // Simpan Receivable
            Receivable::create([
                'transaction_id' => $transaction->id,
                'coa_id' => 11, // Piutang Usaha
                'amount' => $totalAmount,
                'paid_amount' => $totalPayments,
                'remaining_amount' => $remainingAmount,
                'due_date' => now()->addDays(30),
                'status' => ($remainingAmount > 0) ? 'belum lunas' : 'lunas'
            ]);

            $coa_pendapatan_id = ChartOfAccount::where('name', 'Pendapatan Penjualan')->value('id');
            $coa_diskon_id = ChartOfAccount::where('name', 'Diskon Penjualan')->value('id');
            $coa_piutang_id = ChartOfAccount::where('name', 'Piutang Usaha')->value('id');

            // Journal Entry
            $journalEntry = JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => now(),
                'description' => 'Penjualan pada ' . now()->format('d-m-Y'),
            ]);

            // dd('masuk jurnal entry');

            // dd($grossTotalAmount);

            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id' => $coa_pendapatan_id,
                'debit' => 0,
                'credit' => $grossTotalAmount, // CONTOH: 350.000
            ]);

            // dd('journal detail created');

            // dd($coa_pendapatan_id);

            if ($totalDiscountAmount > 0) {
                JournalDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'coa_id' => $coa_diskon_id,
                    'debit' => $totalDiscountAmount, // CONTOH: 30.000
                    'credit' => 0
                ]);
            }

            if ($remainingAmount > 0) {
                JournalDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'coa_id' => $coa_piutang_id,
                    'debit' => $remainingAmount, // CONTOH: 20.000
                    'credit' => 0
                ]);
            }

            if (!empty($validated['payments'])) {
                foreach ($validated['payments'] as $paymentData) {
                    if ($paymentData['amount'] > 0) {
                        JournalDetail::create([
                            'journal_entry_id' => $journalEntry->id,
                            'coa_id' => $paymentData['coa_id'], // coa_id dari Kas/Bank yang dipilih
                            'debit' => $paymentData['amount'], // CONTOH: 300.000
                            'credit' => 0
                        ]);
                    }
                }
            }
            DB::commit(); // Semua proses berhasil, simpan perubahan ke database
            return redirect()->route('dashboard.transactions.index')->with('success', 'Transaction created successfully!');
        } catch (\Exception $e) {
            DB::rollBack(); // Terjadi error, batalkan semua proses dalam transaksi
            // dd($e); // Hapus atau ganti dengan logging di production
            return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function storeWithoutMedicalRecord(Request $request)
    {
        try {
            $updated_by = auth()->id();
            // dd($request->voucher);

            // Validasi Input
            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'admin_id' => 'required|exists:users,id',
                'items' => 'nullable|array',
                'items.*.id' => 'nullable|exists:procedures,id',
                'items.*.quantity' => 'nullable|integer|min:1',
                'items.*.unit_price' => 'nullable|numeric|min:0',
                'items.*.discount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'payments' => 'nullable|array',
                'payments.*.coa_id' => 'required|exists:chart_of_accounts,id', // Validasi coa_id
                'payments.*.method' => 'required',
                'payments.*.amount' => 'required|numeric|min:0',
                'payments.*.notes' => 'nullable|string',
                'voucher' => 'nullable|string'
            ]);

            // dd($validated);

            // Hitung Total Harga Transaksi
            $totalAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $itemData) {
                $procedure = Procedure::findOrFail($itemData['id']);
                $quantity = $itemData['quantity'];
                $unitPrice = $itemData['unit_price'];
                $discount = $itemData['discount'] ?? 0;

                $totalPrice = $unitPrice * $quantity;
                $finalPrice = max($totalPrice - $discount, 0);

                $itemsData[] = [
                    'procedure_id' => $procedure->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'discount' => $discount,
                    'final_price' => $finalPrice,
                ];

                $totalAmount += $finalPrice;
            }

            // Buat Transaksi Baru
            $transaction = Transaction::create([
                'medical_record_id' => null,
                'patient_id' => $validated['patient_id'],
                'admin_id' => $validated['admin_id'],
                'total_amount' => $totalAmount,
                'status' => 'belum lunas', // Default status
                'updated_by' => $updated_by,
                'birthday_voucher' => $validated['voucher']
            ]);

            // Simpan Item Transaksi
            foreach ($itemsData as $data) {
                $transaction->items()->create($data);
            }

            // Simpan Payments
            $totalPayments = 0;
            if (!empty($validated['payments'])) {
                foreach ($validated['payments'] as $paymentData) {
                    $transaction->payments()->create([
                        'payment_date' => now(),
                        'amount' => $paymentData['amount'],
                        'payment_method' => $paymentData['method'],
                        'notes' => $paymentData['notes'] ?? null,
                        'coa_id' => $paymentData['coa_id'],
                    ]);
                    $totalPayments += $paymentData['amount'];
                }
            }


            // **Hitung Total Payment dan Remaining Amount**
            $totalPayments = array_sum(array_column($validated['payments'], 'amount'));
            $remainingAmount = $totalAmount - $totalPayments;

            // **Update Status Transaksi Secara Dinamis**
            $transaction->status = ($remainingAmount > 0) ? 'belum lunas' : 'lunas';
            $transaction->save();

            Patient::where('id', $validated['patient_id'])
                ->update(['birthday_voucher_used' => 1]);

            Receivable::create([
                'transaction_id' => $transaction->id,
                'coa_id' => 11, // COA untuk Accounts Receivable
                'amount' => $totalAmount,
                'paid_amount' => $totalPayments,
                'remaining_amount' => $totalAmount - $totalPayments,
                'due_date' => now()->addDays(30), // Default Jatuh Tempo 30 hari
                'status' => ($totalAmount - $totalPayments > 0) ? 'belum lunas' : 'lunas'
            ]);

            // **Journal Entries untuk Penjualan**
            $journalEntry = JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => now(),
                'description' => 'Penjualan pada ' . now()->format('d-m-Y'),
            ]);

            $idPiutangUsaha = ChartOfAccount::where('name', 'Piutang Usaha')->value('id');

            // **Journal untuk Penjualan & Piutang Usaha**
            // Debit: Piutang Usaha (Sisa Tagihan / Remaining Amount)
            if ($remainingAmount > 0) {
                JournalDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'coa_id' => $idPiutangUsaha, // Piutang Usaha
                    'debit' => $remainingAmount,
                    'credit' => 0
                ]);
            }

            // dd($paymentData['coa_id'][0]);
            // Debit: Kas atau Bank (Total Pembayaran yang Diterima)
            if ($totalPayments > 0) {
                JournalDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'coa_id' => $paymentData['coa_id'][0], // Langsung gunakan coa_id yang dipilih
                    'debit' => $totalPayments,
                    'credit' => 0
                ]);
            }

            $idPendapatanPenjualan = ChartOfAccount::where('name', 'Pendapatan Penjualan')->value('id');

            // Kredit: Pendapatan Penjualan (Total Transaksi)
            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id' => $idPendapatanPenjualan, // Pendapatan Penjualan
                'debit' => 0,
                'credit' => $totalAmount
            ]);


            return redirect()->route('dashboard.transactions.index')->with('success', 'Transaction without medical record created successfully!');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Gagal karena ' . $e->getMessage());
        }
    }


    public function index()
    {
        // Ambil semua transaksi dengan informasi terkait
        $transactions = Transaction::with(['patient', 'admin', 'medicalRecord.patient'])->get();
        $coa = ChartOfAccount::all();

        // Hitung total transaksi lunas dalam 30 hari terakhir
        $paidTransactions = Transaction::where('status', 'lunas')
            ->where('updated_at', '>=', now()->subDays(30))
            ->sum('total_amount');

        // Hitung total transaksi yang belum lunas
        $unpaidTransactions = $transactions->sum('remaining_amount');

        return view('dashboard.transactions.index', [
            'title' => 'Transactions',
            'transactions' => $transactions,
            'cashAccounts' => $coa,
            'paidTransactions' => $paidTransactions,
            'unpaidTransactions' => $unpaidTransactions,
        ]);
    }

    public function payRemaining(Request $request, $transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        $receivable = $transaction->receivable;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $receivable->remaining_amount,
            'notes' => 'nullable|string',
            'coa_id' => 'required|exists:chart_of_accounts,id',
            'payments' => 'required|array',
            'payments.0.method' => 'required|string' // This matches your select name="payments[0][method]"
        ]);

        // Tambahkan payment baru
        $payment = $transaction->payments()->create([
            'payment_date' => now(),
            'amount' => $validated['amount'],
            'notes' => $validated['notes'] ?? null,
            'payment_method' => $validated['payments'][0]['method'], // Access the nested array
            'coa_id' => $validated['coa_id']
        ]);

        // Create journal entry
        $journalEntry = JournalEntry::create([
            'transaction_id' => $transaction->id,
            'entry_date' => now(),
            'description' => 'Pembayaran via ' . $validated['payments'][0]['method'] . ' pada ' . now()->format('d-m-Y'),
        ]);

        // Debit: Kas/Bank
        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id' => $validated['coa_id'],
            'debit' => $validated['amount'],
            'credit' => 0
        ]);

        // Kredit: Piutang Usaha
        $idPiutangUsaha = ChartOfAccount::where('name', 'Piutang Usaha')->value('id');
        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id' => $idPiutangUsaha,
            'debit' => 0,
            'credit' => $validated['amount']
        ]);

        // Update Receivables
        $receivable->paid_amount += $validated['amount'];
        $receivable->remaining_amount = $receivable->amount - $receivable->paid_amount;
        $receivable->status = $receivable->remaining_amount > 0 ? 'belum lunas' : 'lunas';
        $receivable->save();

        // Update Transaction status
        $transaction->status = $receivable->status;
        $transaction->save();

        return redirect()->back()->with('success', 'Payment added successfully!');
    }

    public function showStruk($id)
    {
        // Ambil transaksi dengan item transaksi terkait
        $transaction = Transaction::with([
            'items.procedure', // Ambil prosedur dari transaction_items
            'medicalRecord.patient',
            'medicalRecord.doctor',
            'patient' // Tambahkan relasi ke user sebagai pasien jika tidak ada rekam medis
        ])->findOrFail($id);

        return view('dashboard.transactions.struk', compact('transaction'));
    }



    //     public function create($medicalRecordId)
    // {
    //      // Ambil data rekam medis dengan reservasi, dokter, dan prosedur
    //      $medicalRecord = MedicalRecord::with([
    //         'reservation.patient', 
    //         'reservation.doctor', 
    //         'procedures.basePrice', 
    //         'procedures.promoPrice'
    //     ])->findOrFail($medicalRecordId);

    //     $proceduresWithPrices = [];
    //     $totalAmount = 0;

    //     foreach ($medicalRecord->procedures as $procedure) {
    //         $basePrice = $procedure->basePrice->price ?? 0;
    //         $promoPrice = $procedure->promoPrice->price ?? null;

    //         // Simpan data harga prosedur
    //         $proceduresWithPrices[] = [
    //             'procedure' => $procedure,
    //             'basePrice' => $basePrice,
    //             'promoPrice' => $promoPrice,
    //         ];

    //         // Defaultnya gunakan harga dasar
    //         $totalAmount += $basePrice;
    //     }

    //     // Kirim data ke view
    //     return view('dashboard.transactions.create', [
    //         'title' => 'Create Transaction',
    //         'medicalRecord' => $medicalRecord,
    //         'proceduresWithPrices' => $proceduresWithPrices,
    //         'totalAmount' => $totalAmount,
    //     ]);
    // }

    // public function store(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'medical_record_id' => 'required|exists:medical_records,id',
    //         'amount' => 'required|array',
    //         'payment_type' => 'required|in:cash,credit,dp',
    //         'payment_status' => 'required|in:lunas,cicilan,dp',
    //     ]);

    //     // Cek apakah transaksi untuk rekam medis ini sudah ada
    //     $existingTransaction = Transaction::where('medical_record_id', $request->medical_record_id)->first();

    //     if ($existingTransaction) {
    //         return redirect()->back()->with('error', 'Transaction for this medical record already exists.');
    //     }

    //             // Ambil Medical Record untuk mendapatkan data reservasi, pasien, dan dokter

    //     $medicalRecord = MedicalRecord::with('reservation')->findOrFail($request->medical_record_id);


    //     // Ambil data admin yang membuat transaksi
    //     $admin = Auth::user();

    //     // Hitung total amount
    //     $totalAmount = array_sum($request->amount);

    //     // Buat transaksi baru
    //     Transaction::create([
    //         'medical_record_id' => $request->medical_record_id,
    //         'admin_id' => $admin->id,
    //         'amount' => $totalAmount,
    //         'payment_type' => $request->payment_type,
    //         'payment_status' => $request->payment_status,
    //     ]);

    //     $medicalRecord = MedicalRecord::findOrFail($request->medical_record_id);
    //     foreach ($medicalRecord->procedures as $index => $procedure) {
    //         $procedure->pivot->price = $request->amount[$index]; // Simpan harga pilihan
    //         $procedure->pivot->save();
    //     }

    //     return redirect()->route('dashboard.transactions.index')->with('success', 'Transaction created successfully!');
    // }    

    public function show(Transaction $transaction)
    {
        $transaction->load(['items.procedure', 'patient', 'doctor', 'admin']);
        return view('dashboard.transactions.show', compact('transaction'));
    }
}
