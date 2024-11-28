<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Schedules;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        $doctors = User::where('role_id', 2)->get(); // Ambil data dokter dengan role_id = 2

        $schedules = Schedules::where('is_available', true)->get();
        $user = Auth::user();

        return view('reservation.index', [
            'title' => 'reservation',
            'doctors' => $doctors,
            'schedules' => $schedules,
            'user' => $user,
        ]);
    }

    public function edit($id)
    {
        // Ambil data reservasi berdasarkan ID
        $reservation = Reservation::findOrFail($id);
    
        // Kirim data ke view
        return view('dashboard.reservations.edit', [
            'title' => 'Edit Reservation',
            'reservation' => $reservation, // Data yang akan digunakan di form
        ]);
    }

public function update(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'reservation_date' => 'required|date',
        'reservation_time' => 'required|date_format:H:i',
    ]);

    // Update data reservasi
    $reservation = Reservation::findOrFail($id);
    $reservation->update([
        'name' => $request->name,
        'phone' => $request->phone,
        'reservation_date' => $request->reservation_date,
        'reservation_time' => $request->reservation_time,
    ]);

    // Redirect dengan pesan sukses
    return redirect()->route('dashboard.reservations.index')->with('success', 'Reservation updated successfully!');
}


    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'schedule_id' => 'required|exists:schedules,id',
        ]);
        $schedule = Schedules::findOrFail($request->schedule_id);

        // Simpan data ke tabel reservations
        Reservation::create([
            'nama' => $request->input('name'),
            'nomor_telepon' => $request->input('phone'),
            'tanggal_reservasi' => $schedule->date,
            'jam_reservasi' => $schedule->time_start, // Bisa gunakan waktu mulai atau waktu lainnya
            'doctor_id' => $schedule->doctor_id,
        ]);
        $schedule->update(['is_available' => false]);


        // Redirect dengan pesan sukses
        return redirect()->route('dashboard.reservation.index')->with('success', 'Reservasi berhasil ditambahkan');
    }

    public function destroy($id)
{
    // Hapus data reservasi
    $reservation = Reservation::findOrFail($id);
    $reservation->delete();

    // Redirect dengan pesan sukses
    return redirect()->route('dashboard.reservations.index')->with('success', 'Reservation deleted successfully!');
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
