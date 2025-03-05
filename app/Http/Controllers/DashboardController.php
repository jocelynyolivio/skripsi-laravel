<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Payment;
use App\Models\Receivable;
use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Hitung jumlah pasien hari ini
        $jumlahPasienHariIni = MedicalRecord::whereDate('tanggal_reservasi', $today)->count();

        // Hitung jumlah reservasi yang belum diproses
        $reservasiBelumDiproses = MedicalRecord::whereNull('status_konfirmasi')->count();

        // Hitung total pendapatan hari ini
        $pendapatanHariIni = Transaction::whereDate('created_at', Carbon::today())->sum('total_amount');

        // puiutang
        // $transaksiBelumLunas = Transaction::where('status','belum lunas')->value('total_amount');
        // $transaksiBelumLunas = Payment::groupBy('transaction_id')->sum('amount');

        // $piutang = Transaction::where('status', 'belum lunas')
        //     ->with('payments')
        //     ->get();

        // // Total Semua Piutang (Belum Lunas)
        // $transaksiBelumLunas = $piutang->sum('remaining_amount');

        $transaksiBelumLunas = Receivable::sum('remaining_amount');
        // dd($transaksiBelumLunas);


        $user = Auth::user();
        return view('dashboard.index', [
            'title' => 'Dashboard',
            'user' => $user,
            'role' => $user->role->role_name,
            'jumlahPasienHariIni' => $jumlahPasienHariIni,
            'reservasiBelumDiproses' => $reservasiBelumDiproses,
            'pendapatanHariIni' => $pendapatanHariIni,
            'transaksiBelumLunas' => $transaksiBelumLunas,
        ]);
    }
}
