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
            'cashAccounts' => $cashAccounts
        ]);
    }

    public function createWithoutMedicalRecord()
    {
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
            'cashAccounts' => $cashAccounts
        ]);
    }
    public function store(Request $request)
    {
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
            'coa_id' => 'required|exists:chart_of_accounts,id', // coa
            'payments' => 'nullable|array',
            'payments.*.method' => 'required',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.notes' => 'nullable|string'
        ]);

        $medicalRecord = MedicalRecord::with('procedures')->find($validated['medical_record_id']);
        $totalAmount = 0;
        $itemsData = [];

        $doctorId = null;
        $revenuePercentage = 0;
        $totalRevenueAmount = 0;

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

        // Buat transaksi baru
        $transaction = Transaction::create([
            'medical_record_id' => $validated['medical_record_id'],
            'user_id' => $validated['user_id'],
            'admin_id' => $validated['admin_id'],
            'total_amount' => $totalAmount,
            'status' => 'belum lunas',
            'doctor_id' => $doctorId,
            'revenue_percentage' => $revenuePercentage,
            'revenue_amount' => $totalRevenueAmount,
            'updated_by' => $updated_by,
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
                    'notes' => $paymentData['notes'] ?? null
                ]);
                $totalPayments += $paymentData['amount'];
            }
        }

        $remainingAmount = $totalAmount - $totalPayments;

        // Update Status Transaksi
        $transaction->status = ($remainingAmount > 0) ? 'belum lunas' : 'lunas';
        $transaction->save();

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

        // Journal Entry
        $journalEntry = JournalEntry::create([
            'transaction_id' => $transaction->id,
            'entry_date' => now(),
            'description' => 'Penjualan pada ' . now()->format('d-m-Y'),
        ]);

        $idPiutangUsaha = ChartOfAccount::where('name', 'Piutang Usaha')->value('id');

        if ($remainingAmount > 0) {
            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id' => $idPiutangUsaha,
                'debit' => $remainingAmount,
                'credit' => 0
            ]);
        }

        if ($totalPayments > 0) {
            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id' => $validated['coa_id'],
                'debit' => $totalPayments,
                'credit' => 0
            ]);
        }

        $idPendapatanPenjualan = ChartOfAccount::where('name', 'Pendapatan Penjualan')->value('id');

        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id' => $idPendapatanPenjualan,
            'debit' => 0,
            'credit' => $totalAmount
        ]);

        return redirect()->route('dashboard.transactions.index')->with('success', 'Transaction created successfully!');
    }

    // public function store(Request $request)
    // {
    //     // Validasi input
    //     $validated = $request->validate([
    //         'medical_record_id' => 'nullable|exists:medical_records,id',
    //         'user_id' => 'required|exists:users,id',
    //         'admin_id' => 'required|exists:users,id',
    //         'amount' => 'required|array',
    //         'amount.*' => 'numeric|min:0',
    //         // 'discount' => 'required|array',
    //         // 'discount.*' => 'numeric|min:0',
    //         'discount_final' => 'required|array',
    //         'discount_final.*' => 'numeric|min:0',
    //         'coa_id' => 'required|exists:chart_of_accounts,id', // coa
    //         'payments' => 'nullable|array',
    //         'payments.*.method' => 'required',
    //         'payments.*.amount' => 'required|numeric|min:0',
    //         'payments.*.notes' => 'nullable|string'
    //     ]);

    //     $medicalRecord = MedicalRecord::with('procedures')->find($validated['medical_record_id']);
    //     $totalAmount = 0;
    //     $itemsData = [];

    //     // Hitung Total Amount dari Procedures
    //     if ($medicalRecord) {
    //         $procedureCounts = [];
    //         foreach ($medicalRecord->procedures as $procedure) {
    //             $procedureCounts[$procedure->id] = ($procedureCounts[$procedure->id] ?? 0) + 1;
    //         }

    //         foreach ($procedureCounts as $procedureId => $quantity) {
    //             $unitPrice = $validated['amount'][$procedureId] ?? 0;
    //             $discount = $validated['discount_final'][$procedureId] ?? 0;
    //             $totalPrice = $unitPrice * $quantity;
    //             $finalPrice = max($totalPrice - $discount, 0);
    //             $doctorId = MedicalRecord::where('id', $validated['medical_record_id'])->value('doctor_id');

    //             $doctorRole = User::where('id', $doctorId)->value('role_id');
    //             $revenuePercentage = ($doctorRole == 2) ? 35 : 30;
    //             $revenueAmount = $finalPrice * ($revenuePercentage / 100);

    //             $itemsData[] = [
    //                 'procedure_id' => $procedureId,
    //                 'quantity' => $quantity,
    //                 'unit_price' => $unitPrice,
    //                 'total_price' => $totalPrice,
    //                 'discount' => $discount,
    //                 'final_price' => $finalPrice,
    //                 'doctor_id' => $doctorId,
    //                 'revenue_percentage' => $revenuePercentage,
    //                 'revenue_amount' => $revenueAmount,
    //             ];

    //             $totalAmount += $finalPrice;
    //         }
    //     }

    //     // Buat transaksi baru
    //     $transaction = Transaction::create([
    //         'medical_record_id' => $validated['medical_record_id'],
    //         'user_id' => $validated['user_id'],
    //         'admin_id' => $validated['admin_id'],
    //         'total_amount' => $totalAmount,
    //         'status' => 'belum lunas', // Default status
    //     ]);

    //     // Simpan Item Transaksi
    //     foreach ($itemsData as $data) {
    //         $transaction->items()->create($data);
    //     }

    //     // Simpan Payments
    //     $totalPayments = 0;
    //     if (!empty($validated['payments'])) {
    //         foreach ($validated['payments'] as $paymentData) {
    //             $transaction->payments()->create([
    //                 'payment_date' => now(),
    //                 'amount' => $paymentData['amount'],
    //                 'payment_method' => $paymentData['method'],
    //                 'notes' => $paymentData['notes'] ?? null
    //             ]);
    //             $totalPayments += $paymentData['amount'];
    //         }
    //     }

    //     // **Hitung Total Payment dan Remaining Amount**
    //     $totalPayments = array_sum(array_column($validated['payments'], 'amount'));
    //     $remainingAmount = $totalAmount - $totalPayments;

    //     // **Update Status Transaksi Secara Dinamis**
    //     $transaction->status = ($remainingAmount > 0) ? 'belum lunas' : 'lunas';
    //     $transaction->save();

    //     // Simpan Receivable
    //     Receivable::create([
    //         'transaction_id' => $transaction->id,
    //         'coa_id' => 11, // COA untuk Accounts Receivable
    //         'amount' => $totalAmount,
    //         'paid_amount' => $totalPayments,
    //         'remaining_amount' => $remainingAmount,
    //         'due_date' => now()->addDays(30), // Default Jatuh Tempo 30 hari
    //         'status' => ($remainingAmount > 0) ? 'belum lunas' : 'lunas'
    //     ]);

    //     // **Journal Entries untuk Penjualan**
    //     $journalEntry = JournalEntry::create([
    //         'transaction_id' => $transaction->id,
    //         'entry_date' => now(),
    //         'description' => 'Penjualan pada ' . now()->format('d-m-Y'),
    //     ]);

    //     $idPiutangUsaha = ChartOfAccount::where('name', 'Piutang Usaha')->value('id');

    //     // **Journal untuk Penjualan & Piutang Usaha**
    //     // Debit: Piutang Usaha (Sisa Tagihan / Remaining Amount)
    //     if ($remainingAmount > 0) {
    //         JournalDetail::create([
    //             'journal_entry_id' => $journalEntry->id,
    //             'coa_id' => $idPiutangUsaha, // Piutang Usaha
    //             'debit' => $remainingAmount,
    //             'credit' => 0
    //         ]);
    //     }

    //     // Debit: Kas atau Bank (Total Pembayaran yang Diterima)
    //     if ($totalPayments > 0) {
    //         JournalDetail::create([
    //             'journal_entry_id' => $journalEntry->id,
    //             'coa_id' => $validated['coa_id'], // Langsung gunakan coa_id yang dipilih
    //             'debit' => $totalPayments,
    //             'credit' => 0
    //         ]);
    //     }

    //     $idPendapatanPenjualan = ChartOfAccount::where('name', 'Pendapatan Penjualan')->value('id');

    //     // Kredit: Pendapatan Penjualan (Total Transaksi)
    //     JournalDetail::create([
    //         'journal_entry_id' => $journalEntry->id,
    //         'coa_id' => $idPendapatanPenjualan, // Pendapatan Penjualan
    //         'debit' => 0,
    //         'credit' => $totalAmount
    //     ]);

    //     return redirect()->route('dashboard.transactions.index')->with('success', 'Transaction created successfully!');
    // }



    public function storeWithoutMedicalRecord(Request $request)
    {
        $updated_by = auth()->id();
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
            'coa_id' => 'required|exists:chart_of_accounts,id', // Validasi coa_id
            'payments' => 'nullable|array',
            'payments.*.method' => 'required',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.notes' => 'nullable|string',
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
            'updated_by' => $updated_by
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
                    'notes' => $paymentData['notes'] ?? null
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

        // Debit: Kas atau Bank (Total Pembayaran yang Diterima)
        if ($totalPayments > 0) {
            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id' => $validated['coa_id'], // Langsung gunakan coa_id yang dipilih
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
    }

    // public function storeWithoutMedicalRecord(Request $request)
    // {

    //     // dd($request->all());
    //     // Validasi input untuk transaksi tanpa rekam medis
    //     $validated = $request->validate([
    //         'patient_id' => 'required|exists:patients,id',
    //         'admin_id' => 'required|exists:users,id',
    //         'items' => 'nullable|array',
    //         'items.*.id' => 'nullable|exists:procedures,id',
    //         'items.*.quantity' => 'nullable|integer|min:1',
    //         'items.*.unit_price' => 'nullable|numeric|min:0',
    //         'items.*.discount' => 'nullable|numeric|min:0',
    //         'total_amount' => 'required|numeric|min:0',
    //         'payment_method' => 'required|in:cash,card',
    //         'payments' => 'nullable|array',
    //         'payments.*.amount' => 'required|numeric|min:0',
    //         'payments.*.payment_method' => 'required|in:cash,card,bank_transfer,other',
    //         'payments.*.notes' => 'nullable|string'
    //     ]);
    //     // dd($validated['payments']);
    //     // dd($validated['total_amount']);
    //     // $totalPayment = array_sum(array_column($validated['payments'], 'amount'));
    //     // dd($totalAmount);

    //     // Hitung total harga transaksi dari input yang dipilih
    //     $totalAmount = 0;
    //     $itemsData = [];

    //     // foreach ($validated['items'] as $index => $itemData) {
    //     //     if (!isset($itemData['id'])) {
    //     //         continue;
    //     //     }
    //     //     $procedure = Procedure::findOrFail($itemData['id']);
    //     //     $quantity = $itemData['quantity'] ?? 1;
    //     //     $unitPrice = Pricelist::where('procedure_id', $procedure->id)
    //     //         ->orderBy('effective_date', 'desc')
    //     //         ->value('price') ?? 0;
    //     //     $totalPrice = $unitPrice * $quantity;

    //     //     $itemsData[] = [
    //     //         'procedure_id' => $procedure->id,
    //     //         'quantity' => $quantity,
    //     //         'unit_price' => $unitPrice,
    //     //         'total_price' => $totalPrice,
    //     //     ];

    //     //     $totalAmount += $totalPrice;
    //     // }
    //     foreach ($validated['items'] as $itemData) {
    //         $procedure = Procedure::findOrFail($itemData['id']);
    //         $quantity = $itemData['quantity'];
    //         $unitPrice = $itemData['unit_price'];
    //         $discount = $itemData['discount'] ?? 0;

    //         $totalPrice = $unitPrice * $quantity;
    //         $finalPrice = max($totalPrice - $discount, 0); // Pastikan tidak negatif

    //         // dd($validated);

    //         $itemsData[] = [
    //             'procedure_id' => $procedure->id,
    //             'quantity' => $quantity,
    //             'unit_price' => $unitPrice,
    //             'total_price' => $totalPrice,
    //             'discount' => $discount,
    //             'final_price' => $finalPrice,
    //         ];



    //         $totalAmount += $finalPrice;
    //     }

    //     // // Jika tidak ada item valid, hentikan proses
    //     // if (empty($itemsData)) {
    //     //     return redirect()->back()->with('error', 'No valid items selected for the transaction.');
    //     // }

    //     // // Buat transaksi baru tanpa rekam medis
    //     // $transaction = Transaction::create([
    //     //     'medical_record_id' => null, // Tidak ada rekam medis
    //     //     'patient_id' => $validated['patient_id'],
    //     //     'admin_id' => $validated['admin_id'],
    //     //     'total_amount' => $totalAmount,
    //     //     'payment_method' => $validated['payment_method'],
    //     // ]);

    //     // // Simpan item transaksi
    //     // foreach ($itemsData as $data) {
    //     //     $transaction->items()->create($data);
    //     // }

    //     $totalPayments = array_sum(array_column($validated['payments'], 'amount'));
    //     $remainingAmount = max($totalAmount - $totalPayments, 0);
    //     // Buat transaksi baru
    //     $transaction = Transaction::create([
    //         'medical_record_id' => null,
    //         'patient_id' => $validated['patient_id'],
    //         'admin_id' => $validated['admin_id'],
    //         'total_amount' => $totalAmount,
    //         'status' => ($remainingAmount > 0) ? 'belum lunas' : 'lunas',
    //     ]);

    //     // Simpan item transaksi
    //     // dd($itemsData);
    //     foreach ($itemsData as $data) {
    //         // dd($data);
    //         $transaction->items()->create($data);
    //     }

    //     // Simpan data payments
    //     if (!empty($validated['payments'])) {
    //         foreach ($validated['payments'] as $paymentData) {
    //             $transaction->payments()->create([
    //                 'payment_date' => now(), // Atau sesuaikan dengan data yang dimiliki
    //                 'amount' => $paymentData['amount'],
    //                 'payment_method' => $paymentData['payment_method'],
    //                 'notes' => $paymentData['notes'] ?? null
    //             ]);
    //         }
    //     }

    //     // if($validated['total_amount'] > $totalPayment){
    //     //     dd('tagihan lebih besar');

    //     // }

    //     // **Tambahkan Receivable untuk Piutang**
    //     Receivable::create([
    //         'transaction_id' => $transaction->id,
    //         'coa_id' => 2, // COA untuk Accounts Receivable
    //         'amount' => $totalAmount,
    //         'paid_amount' => $totalPayments,
    //         'remaining_amount' => $totalAmount - $totalPayments,
    //         'due_date' => now()->addDays(30), // Default Jatuh Tempo 30 hari
    //         'status' => ($totalAmount - $totalPayments > 0) ? 'belum lunas' : 'lunas'
    //     ]);

    //     $journalEntry = JournalEntry::create([
    //         'transaction_id' => $transaction->id,
    //         'entry_date' => now(),
    //         'description' => 'Penjualan pada ' . now()->format('d-m-Y'),
    //     ]);

    //     // Debit: Piutang Usaha
    //     JournalDetail::create([
    //         'journal_entry_id' => $journalEntry->id,
    //         'coa_id' =>  ChartOfAccount::where('code', '1200')->value('id'), // Piutang Usaha
    //         'debit' => $totalAmount,
    //         'credit' => 0
    //     ]);

    //     // Kredit: Pendapatan Penjualan
    //     JournalDetail::create([
    //         'journal_entry_id' => $journalEntry->id,
    //         'coa_id' => ChartOfAccount::where('code', '4100')->value('id'), // Pendapatan Penjualan
    //         'debit' => 0,
    //         'credit' => $totalAmount
    //     ]);



    //     return redirect()->route('dashboard.transactions.index')->with('success', 'Transaction without medical record created successfully!');
    // }


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
        $transaction->load(['patient', 'admin', 'editor', 'items.procedure', 'payments', 'medicalRecord']);
    
        return view('dashboard.transactions.show', compact('transaction'));
    }
    
}
