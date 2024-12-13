<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function create($medicalRecordId)
{
    // Ambil data rekam medis beserta prosedurnya
    $medicalRecord = MedicalRecord::with(['patient', 'doctor', 'reservation', 'procedures.priceLists'])->findOrFail($medicalRecordId);

    $proceduresWithPrices = [];
    $totalAmount = 0;

    foreach ($medicalRecord->procedures as $procedure) {
        $basePrice = $procedure->basePrice->price ?? 0;
        $promoPrice = $procedure->promoPrice->price ?? null;

        // Simpan data harga prosedur
        $proceduresWithPrices[] = [
            'procedure' => $procedure,
            'basePrice' => $basePrice,
            'promoPrice' => $promoPrice,
        ];

        // Defaultnya gunakan harga dasar
        $totalAmount += $basePrice;
    }

    // Kirim data ke view
    return view('dashboard.transactions.create', [
        'title' => 'Create Transaction',
        'medicalRecord' => $medicalRecord,
        'proceduresWithPrices' => $proceduresWithPrices,
        'totalAmount' => $totalAmount,
    ]);
}


public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'medical_record_id' => 'required|exists:medical_records,id',
        'reservation_id' => 'required|exists:reservations,id',
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:users,id',
        'amount' => 'required|array',
        'payment_type' => 'required|in:cash,credit,dp',
        'payment_status' => 'required|in:lunas,cicilan,dp',
    ]);

    // Cek apakah transaksi untuk rekam medis ini sudah ada
    $existingTransaction = Transaction::where('medical_record_id', $request->medical_record_id)->first();

    if ($existingTransaction) {
        return redirect()->back()->with('error', 'Transaction for this medical record already exists.');
    }

    // Ambil data admin yang membuat transaksi
    $admin = Auth::user();
    
    // Hitung total amount
    $totalAmount = array_sum($request->amount);

    // Buat transaksi baru
    Transaction::create([
        'medical_record_id' => $request->medical_record_id,
        'reservation_id' => $request->reservation_id,
        'patient_id' => $request->patient_id,
        'doctor_id' => $request->doctor_id,
        'admin_id' => $admin->id,
        'amount' => $totalAmount,
        'payment_type' => $request->payment_type,
        'payment_status' => $request->payment_status,
    ]);

    return redirect()->route('dashboard.transactions.index')->with('success', 'Transaction created successfully!');
}




    public function index()
    {
        $transactions = Transaction::with(['patient', 'doctor', 'admin', 'reservation'])->get();
        return view('dashboard.transactions.index', [
            'title' => 'Transactions',
            'transactions' => $transactions,
        ]);
    }

    public function showStruk($id)
{
    $transaction = Transaction::with([
        'medicalRecord.procedures.basePrice',
        'medicalRecord.patient',
        'medicalRecord.doctor'
    ])->findOrFail($id);    
    
    return view('dashboard.transactions.struk', compact('transaction')); // Tampilkan view struk
}



}

