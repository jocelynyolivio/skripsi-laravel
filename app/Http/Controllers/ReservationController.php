<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Schedules;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Mail\ReservationInvitation;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{

    public function list()
    {
        $reservations = Reservation::all(); // Ambil semua data reservasi
        $reservations = Reservation::with('patient')->get();
        return view('dashboard.reservations.index', [
            'title' => 'Data Reservasi',
            'reservations' => $reservations
        ]);
    }

    public function destroy($id)
    {
        // Ambil data reservasi berdasarkan ID
        $reservation = Reservation::findOrFail($id);

        // Cari jadwal terkait dari reservasi
        $schedule = $reservation->schedule;

        // $schedule->update(['is_available' => false]);

        // Hapus data reservasi
        $reservation->delete();

        // Update status is_available pada jadwal menjadi true
        // $schedule->update(['is_available' => true]);
        return redirect()->route('dashboard.reservations.index')->with('success', 'Reservation deleted successfully!');
    }

    public function sendWhatsApp($id)
    {
        // Ambil data reservasi berdasarkan ID
        $reservation = Reservation::with('patient')->findOrFail($id);

        // Nomor telepon pasien
        $phoneNumber = $reservation->patient->home_mobile;

        // Pesan template
        $message = "Halo {$reservation->patient->fname} {$reservation->patient->mname} {$reservation->patient->lname}, untuk konfirmasi kehadiran di {$reservation->tanggal_reservasi} dan {$reservation->jam_mulai} ya. Terima kasih!";

        // Redirect ke wa.me dengan pesan template
        return redirect("https://wa.me/{$phoneNumber}?text=" . urlencode($message));
    }

    public function waConfirmation($id)
    {
        // dd('oi');
        $reservation = Reservation::findOrFail($id);
        $reservation->status_konfirmasi = 'Sudah Dikonfirmasi';
        $reservation->save();
        return redirect()->back()->with('success', 'Reservation confirmed successfully!');
    }

    public function index(Request $request)
    {
        // dd('hai');
        if ($request->has('date')) {
            $date = $request->date;
            $dayOfWeek = Carbon::parse($date)->format('l');

            dd($request['date']);

            // Mengambil jadwal dokter yang tersedia untuk tanggal yang dipilih
            $schedules = Schedules::where('date', $date)
                ->where('is_available', true)
                ->with('doctor') // Eager loading relasi doctor
                ->get()
                ->groupBy('doctor_id')
                ->map(function ($schedules) {
                    return [
                        'doctor' => $schedules->first()->doctor,
                        'schedules' => $schedules
                    ];
                });

            return view('reservation.index', [
                'title' => 'Reservation',
                'schedules' => $schedules,
                'date' => $date,
                'day_of_week' => $dayOfWeek
            ]);
        }

        return view('reservation.index', [
            'title' => 'Reservation'
        ]);
    }

    // public function edit($id)
    // {
    //     $reservation = Reservation::findOrFail($id);
    //     $patients = Patient::all(); // Ambil daftar pasien
    //     $doctors = ScheduleTemplate::distinct()->pluck('doctor_id'); // Ambil daftar dokter

    //     return view('dashboard.reservations.edit', compact('reservation', 'patients', 'doctors'));
    // }


    // public function update(Request $request, $id)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'patient_id' => 'required|integer',
    //         'doctor_id' => 'required|integer',
    //         'tanggal_reservasi' => 'required|date',
    //         'jam_mulai' => 'required|date_format:H:i',
    //         'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
    //     ]);

    //     // Ambil data reservasi berdasarkan ID
    //     $reservation = Reservation::findOrFail($id);

    //     // Update data reservasi
    //     $reservation->update([
    //         'patient_id' => $request->patient_id,
    //         'doctor_id' => $request->doctor_id,
    //         'tanggal_reservasi' => $request->tanggal_reservasi,
    //         'jam_mulai' => $request->jam_mulai,
    //         'jam_selesai' => $request->jam_selesai,
    //     ]);

    //     // Set flash message dan redirect
    //     return redirect()->route('dashboard.schedules.index')->with('success', 'Reservation updated successfully!');
    // }

    // public function storeReservation(Request $request)
    // {
    //     // Pastikan user yang login adalah pasien
    //     $patientId = auth()->user()->id; // Dapatkan ID pasien yang sedang login

    //     // Validasi input
    //     $request->validate([
    //         'doctor_id' => 'required|integer',
    //         'tanggal_reservasi' => 'required|date',
    //         'jam_mulai' => 'required|date_format:H:i',
    //         'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
    //     ]);

    //     // Simpan data reservasi
    //     $reservation = Reservation::create([
    //         'patient_id' => $patientId, // Menggunakan ID pasien yang sedang login
    //         'doctor_id' => $request->doctor_id,
    //         'tanggal_reservasi' => $request->tanggal_reservasi,
    //         'jam_mulai' => $request->jam_mulai,
    //         'jam_selesai' => $request->jam_selesai,
    //     ]);

    //     // Menyimpan flash message ke session
    //     session()->flash('success', 'Reservasi berhasil dibuat. Silakan cek data reservasi.');

    //     return redirect()->route('dashboard.reservations.index'); // Redirect ke halaman dashboard setelah sukses
    // }


    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'doctor_id' => 'required|integer',
            'tanggal_reservasi' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        // Mengambil ID pasien yang sedang login
        $patient = auth()->guard('patient')->user(); // Ambil seluruh objek patient
        $patientId = $patient->id;
        $patientName = $patient->name; // Asumsi nama pasien ada di kolom 'name' di model Patient/User yang digunakan guard 'patient'

        // Simpan data reservasi ke database
        MedicalRecord::create([
            'patient_id' => $patientId,
            'doctor_id' => $request->doctor_id,
            'tanggal_reservasi' => $request->tanggal_reservasi,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        // Membuat detail reservasi untuk email
        $startDateTime = Carbon::parse($request->tanggal_reservasi . ' ' . $request->jam_mulai);
        $endDateTime = Carbon::parse($request->tanggal_reservasi . ' ' . $request->jam_selesai);

        // Ambil data dokter dari tabel users menggunakan doctor_id
        $doctorUser = User::find($request->doctor_id);

        if ($doctorUser) {
            $reservationDetails = [
                'title' => 'Reservasi Baru dengan Pasien ' . $patientName, // Judul yang jelas untuk dokter
                'description' => 'Detail Reservasi: Pasien ' . $patientName . ' pada tanggal ' . $request->tanggal_reservasi . ' jam ' . $request->jam_mulai . ' sampai ' . $request->jam_selesai . '.',
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'doctor_name' => $doctorUser->name, // Nama dokter dari model User
                'doctor_email' => $doctorUser->email, // Email dokter dari model User
                'patient_name' => $patientName, // Nama pasien untuk detail deskripsi
            ];

            // Kirim email undangan ke dokter
            Mail::to($doctorUser->email)
                ->send(new ReservationInvitation($reservationDetails));
        }

        // Menyimpan flash message ke session
        session()->flash('success', 'Reservasi berhasil dibuat. Mohon tunggu Admin menghubungi Anda untuk konfirmasi.');

        return redirect()->route('reservations.upcoming');
    }

    public function upcomingReservations()
    {
        // Ambil user yang sedang login
        $patientId = auth()->id();

        // Ambil daftar reservasi yang masih berlaku (tanggal >= hari ini)
        $reservations = MedicalRecord::where('patient_id', $patientId)
            ->whereDate('tanggal_reservasi', '>=', now()->toDateString()) // Cek jika tanggal masih berlaku
            ->with('doctor') // Ambil data dokter
            ->orderBy('tanggal_reservasi', 'asc') // Urutkan berdasarkan tanggal
            ->get();

        return view('reservation.upcoming', compact('reservations'));
    }


    // public function createForAdmin()
    // {
    //     // Ambil jadwal yang tersedia
    //     $schedules = Schedules::where('is_available', true)->get();

    //     // Ambil data pasien dari tabel patients
    //     $patients = Patient::all();

    //     // Kirim data ke view
    //     return view('dashboard.reservations.create', [
    //         'title' => 'Add Reservation',
    //         'schedules' => $schedules,
    //         'patients' => $patients,
    //     ]);
    // }

    // public function storeForAdmin(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'schedule_id' => 'required|exists:schedules,id',
    //         'patient_id' => 'required|exists:patients,id',
    //     ]);

    //     // Ambil jadwal yang dipilih
    //     $schedule = Schedules::findOrFail($request->schedule_id);

    //     // Simpan reservasi baru
    //     Reservation::create([
    //         'schedule_id' => $request->schedule_id,
    //         'patient_id' => $request->patient_id,
    //         'doctor_id' => $schedule->doctor_id,
    //         'tanggal_reservasi' => $schedule->date,
    //         'jam_mulai' => $schedule->time_start,
    //         'jam_selesai' => $schedule->time_end,
    //     ]);

    //     // Tandai jadwal sebagai tidak tersedia
    //     $schedule->update(['is_available' => false]);
    //     // $schedule->delete();

    //     return redirect()->route('dashboard.reservations.index')->with('success', 'Reservation added successfully!');
    // }

}
