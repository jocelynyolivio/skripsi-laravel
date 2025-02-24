<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Pricelist;
use App\Models\Procedure;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;

class TransactionController extends Controller
{
    public function create(Request $request, $medicalRecordId = null)
    {
        $medicalRecord = null;
        $proceduresWithPrices = [];
        $totalAmount = 0;
        $users = $medicalRecord ? null : User::all();

        if (!$medicalRecordId) {
            return redirect()->route('dashboard.transactions.selectMedicalRecord');
        }

        if ($medicalRecordId) {
            $medicalRecord = MedicalRecord::with([
                'reservation.patient',
                'reservation.doctor',
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
            'users' => $users
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

        $patients = Patient::all(); // Ambil daftar pengguna

        return view('dashboard.transactions.create_without_medical_record', [
            'title' => 'Create Transaction Without Medical Record',
            'proceduresWithPrices' => $proceduresWithPrices,
            'patients' => $patients,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // Validasi input
        $validated = $request->validate([
            'medical_record_id' => 'nullable|exists:medical_records,id',
            'user_id' => 'required|exists:users,id',
            'admin_id' => 'required|exists:users,id',
            'amount' => 'required|array',
            'amount.*' => 'numeric|min:0',
            'discount' => 'required|array',
            'discount.*' => 'numeric|min:0',
            'payment_method' => 'required|in:cash,card',
            'payments' => 'nullable|array',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.payment_method' => 'required|in:cash,card,bank_transfer,other',
            'payments.*.notes' => 'nullable|string'
        ]);

        // dd($validated);

        $medicalRecord = MedicalRecord::with('procedures')->find($validated['medical_record_id']);
        $totalAmount = 0;

        // Buat transaksi baru
        $transaction = Transaction::create([
            'medical_record_id' => $validated['medical_record_id'],
            'user_id' => $validated['user_id'],
            'admin_id' => $validated['admin_id'],
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
        ]);

        if ($medicalRecord) {
            $procedureCounts = [];

            foreach ($medicalRecord->procedures as $procedure) {
                $procedureCounts[$procedure->id] = ($procedureCounts[$procedure->id] ?? 0) + 1;
            }

            foreach ($procedureCounts as $procedureId => $quantity) {
                // $unitPrice = Pricelist::where('procedure_id', $procedureId)
                //     ->orderBy('effective_date', 'desc')
                //     ->value('price') ?? 0;
                $unitPrice = $validated['amount'][$procedureId] ?? 0;

                $discount = $validated['discount'][$procedureId] ?? 0;
                $totalPrice = $unitPrice * $quantity;
                $finalPrice = max($totalPrice - $discount, 0);

                $doctorId = Reservation::join('medical_records', 'reservations.id', '=', 'medical_records.reservation_id')->where('medical_records.id', $medicalRecord->id)->value('reservations.doctor_id');

                $doctorRole = User::where('id', $doctorId)->value('role_id');
                $revenuePercentage = ($doctorRole == 2) ? 35 : 30;

                $revenueAmount = $finalPrice * ($revenuePercentage / 100);

                $transaction->items()->updateOrCreate(
                    ['transaction_id' => $transaction->id, 'procedure_id' => $procedureId],
                    [
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'discount' => $discount,
                        'final_price' => $finalPrice,
                        'doctor_id' => $doctorId, // Simpan dokter langsung
                        'revenue_percentage' => $revenuePercentage,
                        'revenue_amount' => $revenueAmount,
                    ]
                );

                $totalAmount += $finalPrice;



                // DoctorRevenueShare::create([
                //     'transaction_id' => $transaction->id,
                //     'doctor_id' => $doctorId,
                //     'procedure_id' => $procedureId,
                //     'payment_net' => $finalPrice,
                //     'revenue_percentage' => $revenuePercentage,
                //     'revenue_amount' => $revenueShare,
                //     'month_year' => now()->format('Y-m'),
                // ]);
            }
        }

        // Update total transaksi setelah diskon diterapkan
        $transaction->update(['total_amount' => $totalAmount]);

        $total_payments = 0;
        if ($request->has('payments')) {
            foreach ($request->payments as $payment) {
                $transaction->payments()->create([
                    'payment_date' => now(),
                    'amount' => $payment['amount'],
                    'payment_method' => $payment['payment_method'],
                    'notes' => $payment['notes']
                ]);
                $total_payments += $payment['amount'];
            }

            // dd($total_payments);
        }


        return redirect()->route('dashboard.transactions.index')->with('success', 'Transaction created successfully!');
    }



    public function storeWithoutMedicalRecord(Request $request)
    {

        // dd($request->all());
        // Validasi input untuk transaksi tanpa rekam medis
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'admin_id' => 'required|exists:users,id',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:procedures,id',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card',
            'payments' => 'nullable|array',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.payment_method' => 'required|in:cash,card,bank_transfer,other',
            'payments.*.notes' => 'nullable|string'
        ]);
        // dd($validated);

        // Hitung total harga transaksi dari input yang dipilih
        $totalAmount = 0;
        $itemsData = [];

        // foreach ($validated['items'] as $index => $itemData) {
        //     if (!isset($itemData['id'])) {
        //         continue;
        //     }
        //     $procedure = Procedure::findOrFail($itemData['id']);
        //     $quantity = $itemData['quantity'] ?? 1;
        //     $unitPrice = Pricelist::where('procedure_id', $procedure->id)
        //         ->orderBy('effective_date', 'desc')
        //         ->value('price') ?? 0;
        //     $totalPrice = $unitPrice * $quantity;

        //     $itemsData[] = [
        //         'procedure_id' => $procedure->id,
        //         'quantity' => $quantity,
        //         'unit_price' => $unitPrice,
        //         'total_price' => $totalPrice,
        //     ];

        //     $totalAmount += $totalPrice;
        // }
        foreach ($validated['items'] as $itemData) {
            $procedure = Procedure::findOrFail($itemData['id']);
            $quantity = $itemData['quantity'];
            $unitPrice = $itemData['unit_price'];
            $discount = $itemData['discount'] ?? 0;

            $totalPrice = $unitPrice * $quantity;
            $finalPrice = max($totalPrice - $discount, 0); // Pastikan tidak negatif

            // dd($validated);

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

        // // Jika tidak ada item valid, hentikan proses
        // if (empty($itemsData)) {
        //     return redirect()->back()->with('error', 'No valid items selected for the transaction.');
        // }

        // // Buat transaksi baru tanpa rekam medis
        // $transaction = Transaction::create([
        //     'medical_record_id' => null, // Tidak ada rekam medis
        //     'patient_id' => $validated['patient_id'],
        //     'admin_id' => $validated['admin_id'],
        //     'total_amount' => $totalAmount,
        //     'payment_method' => $validated['payment_method'],
        // ]);

        // // Simpan item transaksi
        // foreach ($itemsData as $data) {
        //     $transaction->items()->create($data);
        // }

        // Buat transaksi baru
        $transaction = Transaction::create([
            'medical_record_id' => null,
            'patient_id' => $validated['patient_id'],
            'admin_id' => $validated['admin_id'],
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
        ]);

        // Simpan item transaksi
        // dd($itemsData);
        foreach ($itemsData as $data) {
            // dd($data);
            $transaction->items()->create($data);
        }

        // Simpan data payments
if (!empty($validated['payments'])) {
    foreach ($validated['payments'] as $paymentData) {
        $transaction->payments()->create([
            'payment_date' => now(), // Atau sesuaikan dengan data yang dimiliki
            'amount' => $paymentData['amount'],
            'payment_method' => $paymentData['payment_method'],
            'notes' => $paymentData['notes'] ?? null
        ]);
    }
}



        return redirect()->route('dashboard.transactions.index')->with('success', 'Transaction without medical record created successfully!');
    }


    public function index()
    {
        // Ambil semua transaksi dengan informasi terkait
        $transactions = Transaction::with(['patient', 'admin', 'medicalRecord.reservation.patient'])->get();

        return view('dashboard.transactions.index', [
            'title' => 'Transactions',
            'transactions' => $transactions,
        ]);
    }


    public function showStruk($id)
    {
        // Ambil transaksi dengan item transaksi terkait
        $transaction = Transaction::with([
            'items.procedure', // Ambil prosedur dari transaction_items
            'medicalRecord.reservation.patient',
            'medicalRecord.reservation.doctor',
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

}
