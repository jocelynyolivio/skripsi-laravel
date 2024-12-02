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

    public function edit($id)
{
    // Ambil data reservasi berdasarkan ID
    $reservation = Reservation::findOrFail($id);

    // Ambil jadwal yang masih tersedia
    $schedules = Schedules::where('is_available', true)->get();

    // Kirim data ke view
    return view('dashboard.reservations.edit', [
        'title' => 'Edit Reservation',
        'reservation' => $reservation, // Data reservasi yang akan diedit
        'schedules' => $schedules, // Jadwal yang masih available
    ]);
}

public function update(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'schedule_id' => 'required|exists:schedules,id',
    ]);

    // Cari reservasi berdasarkan ID
    $reservation = Reservation::findOrFail($id);

    // Ambil schedule lama (yang sebelumnya digunakan di reservasi)
    $oldSchedule = $reservation->schedule;

    // Ubah status jadwal lama menjadi 'available'
    $oldSchedule->update(['is_available' => true]);

    // Cari jadwal baru yang dipilih
    $newSchedule = Schedules::findOrFail($request->schedule_id);

    // Update status jadwal baru menjadi 'reserved'
    $newSchedule->update(['is_available' => false]);

    // Update data reservasi dengan schedule baru
    $reservation->update([
        'schedule_id' => $newSchedule->id,
        'doctor_id' => $newSchedule->doctor_id,
        'tanggal_reservasi' => $newSchedule->date,
        'jam_mulai' => $newSchedule->time_start,
        'jam_selesai' => $newSchedule->time_end,
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
        'jam_mulai' => $schedule->time_start,
        'jam_selesai' => $schedule->time_end,
    ]);

    // Tandai jadwal sebagai tidak tersedia
    $schedule->update(['is_available' => false]);

    // $schedule->delete();

    return redirect()->route('reservation.index')->with('success', 'Reservation created successfully!');
}



    public function destroy($id)
{
    // Ambil data reservasi berdasarkan ID
    $reservation = Reservation::findOrFail($id);

    // Cari jadwal terkait dari reservasi
    $schedule = $reservation->schedule;

    $schedule->update(['is_available' => false]);

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
        'jam_mulai' => $schedule->time_start,
        'jam_selesai' => $schedule->time_end,
    ]);

    // Tandai jadwal sebagai tidak tersedia
    $schedule->update(['is_available' => false]);
    // $schedule->delete();

    return redirect()->route('dashboard.reservations.index')->with('success', 'Reservation added successfully!');
}

public function sendWhatsApp($id)
{
    // Ambil data reservasi berdasarkan ID
    $reservation = Reservation::with('patient')->findOrFail($id);

    // Nomor telepon pasien
    $phoneNumber = $reservation->patient->nomor_telepon;

    // Pesan template
    $message = "Halo {$reservation->patient->name}, untuk konfirmasi kehadiran di {$reservation->tanggal_reservasi} dan {$reservation->jam_mulai} ya. Terima kasih!";

     // Update status konfirmasi
     $reservation->status_konfirmasi = 'Sudah Dikonfirmasi';
     $reservation->save();

    // Redirect ke wa.me dengan pesan template
    return redirect("https://wa.me/62{$phoneNumber}?text=" . urlencode($message));
}

}
