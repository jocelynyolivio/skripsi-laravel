<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Hitung jumlah pasien hari ini
        $jumlahPasienHariIni = Reservation::whereDate('tanggal_reservasi', $today)->count();

        // Hitung jumlah reservasi yang belum diproses
        $reservasiBelumDiproses = Reservation::whereNull('status_konfirmasi')->count();

        // Hitung total pendapatan hari ini
        $pendapatanHariIni = Transaction::whereDate('created_at', Carbon::today())->sum('amount');
        
        $user = Auth::user();
        return view('dashboard.index', [
            'title' => 'Dashboard',
            'user' => $user,
            'role' => $user->role->role_name,
            'jumlahPasienHariIni' => $jumlahPasienHariIni,
            'reservasiBelumDiproses' => $reservasiBelumDiproses,
            'pendapatanHariIni' => $pendapatanHariIni,
        ]);
    }

    
}

