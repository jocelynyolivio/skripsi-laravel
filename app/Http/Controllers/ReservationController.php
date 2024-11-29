<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Schedules;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    // public function index()
    // {
    //     $doctors = User::where('role_id', 2)->get(); // Ambil data dokter dengan role_id = 2

    //     $schedules = Schedules::where('is_available', true)->get();
    //     $user = Auth::user();

    //     return view('reservation.index', [
    //         'title' => 'reservation',
    //         'doctors' => $doctors,
    //         'schedules' => $schedules,
    //         'user' => $user,
    //     ]);
    // }

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


public function index()
{
    // Ambil pasien yang sedang login
    $patient = Auth::guard('patient')->user();

    // Ambil jadwal yang masih tersedia
    $schedules = Schedules::where('is_available', true)
        ->with('doctor') // Ambil data dokter terkait
        ->get();

    return view('reservation.index', [
        'title' => 'Available Schedules',
        'schedules' => $schedules,
        'patient' => $patient,
    ]);
}

public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'schedule_id' => 'required|exists:schedules,id',
    ]);

    // Ambil pasien yang login
    $patient = Auth::guard('patient')->user();

    if (!$patient) {
        return redirect()->back()->with('error', 'You must be logged in as a patient to make a reservation.');
    }

    // Ambil jadwal yang dipilih
    $schedule = Schedules::findOrFail($request->schedule_id);

    // Simpan data ke tabel reservations
    Reservation::create([
        'schedule_id' => $schedule->id,
        'patient_id' => $patient->id,
        'doctor_id' => $schedule->doctor_id,
        'tanggal_reservasi' => $schedule->date,
        'jam_reservasi' => $schedule->time_start,
    ]);

    // Tandai jadwal sebagai tidak tersedia
    $schedule->update(['is_available' => false]);

    return redirect()->route('reservation.index')->with('success', 'Reservation created successfully!');
}



    public function destroy($id)
{
    // Ambil data reservasi berdasarkan ID
    $reservation = Reservation::findOrFail($id);

    // Cari jadwal terkait dari reservasi
    $schedule = $reservation->schedule;

    // Hapus data reservasi
    $reservation->delete();

    // Update status is_available pada jadwal menjadi true
    $schedule->update(['is_available' => true]);
    return redirect()->route('dashboard.reservations.index')->with('success', 'Reservation deleted successfully!');
}


    public function list()
    {
        $reservations = Reservation::all(); // Ambil semua data reservasi
        $reservations = Reservation::with('patient')->get();
        return view('dashboard.reservations.index', [
            'title' => 'Data Reservasi',
            'reservations' => $reservations
        ]);
    }

    public function createForAdmin()
{
    // Ambil jadwal yang tersedia
    $schedules = Schedules::where('is_available', true)->get();

    // Ambil data pasien dari tabel patients
    $patients = Patient::all();

    // Kirim data ke view
    return view('dashboard.reservations.create', [
        'title' => 'Add Reservation',
        'schedules' => $schedules,
        'patients' => $patients,
    ]);
}

public function storeForAdmin(Request $request)
{
    // Validasi input
    $request->validate([
        'schedule_id' => 'required|exists:schedules,id',
        'patient_id' => 'required|exists:patients,id',
    ]);

    // Ambil jadwal yang dipilih
    $schedule = Schedules::findOrFail($request->schedule_id);

    // Simpan reservasi baru
    Reservation::create([
        'schedule_id' => $request->schedule_id,
        'patient_id' => $request->patient_id,
        'doctor_id' => $schedule->doctor_id,
        'tanggal_reservasi' => $schedule->date,
        'jam_reservasi' => $schedule->time_start,
    ]);

    // Tandai jadwal sebagai tidak tersedia
    $schedule->update(['is_available' => false]);

    return redirect()->route('dashboard.reservations.index')->with('success', 'Reservation added successfully!');
}

}
