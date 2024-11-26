<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return view('reservation.index',[
            'title'=>'reservation'
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
        ]);

        // Simpan data ke tabel reservations
        Reservation::create([
            'nama' => $request->input('name'),
            'nomor_telepon' => $request->input('phone'),
            'tanggal_reservasi' => $request->input('reservation_date'),
            'jam_reservasi' => $request->input('reservation_time'),
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('reservation.index')->with('success', 'Reservasi berhasil ditambahkan');
    }

    public function list()
    {
        $reservations = Reservation::all(); // Ambil semua data reservasi
        return view('dashboard.reservations.index', [
            'title' => 'Data Reservasi',
            'reservations' => $reservations
        ]);
    }
}
