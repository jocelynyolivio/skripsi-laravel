<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Patient;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\ScheduleOverride;
use App\Models\ScheduleTemplate;
use App\Mail\ReservationInvitation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class ScheduleController extends Controller
{
   public function index() // Ini adalah metode yang kemungkinan me-render halaman Make Reservations
    {
        $templates = ScheduleTemplate::where('is_active', true)->get();
        $overrides = ScheduleOverride::all();
        $schedules = $this->generateSchedules($templates, $overrides);

        // --- TAMBAHKAN KODE INI ---
        $today = Carbon::today()->toDateString(); // Tanggal hari ini (YYYY-MM-DD)
        $oneMonthFromNow = Carbon::today()->addMonth()->toDateString(); // Tanggal satu bulan dari sekarang (YYYY-MM-DD)
        // --- AKHIR TAMBAH KODE ---

        return view('dashboard.schedules.index', [ // Sesuaikan nama view jika berbeda
            'title' => 'Make Reservation',
            'schedules' => $schedules,
            'today' => $today, // Kirim tanggal hari ini ke view
            'oneMonthFromNow' => $oneMonthFromNow // Kirim tanggal satu bulan ke depan ke view
        ]);
    }

   public function getPatients(Request $request) // Pastikan hanya ada SATU deklarasi ini
    {
        $query = Patient::query();

        if ($request->has('q') && $request->q != '') {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('fname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('mname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('lname', 'like', '%' . $searchTerm . '%')
                  ->orWhere('home_mobile', 'like', '%' . $searchTerm . '%')
                  ->orWhere('patient_id', 'like', '%' . $searchTerm . '%');
            });
        }

        $patients = $query->select('id', 'fname', 'mname', 'lname', 'home_mobile', 'date_of_birth')
                          ->limit(20)
                          ->get();

        return response()->json($patients);
    }


    public function getDoctorsByDate(Request $request)
    {
        // $who = auth()->id();
        $request->validate([
            'date' => 'required|date',
        ]);

        $selectedDate = $request->date;
        $dayOfWeek = date('l', strtotime($selectedDate));

        // Ambil template berdasarkan hari
        // $templates = ScheduleTemplate::where('day_of_week', $dayOfWeek)->get();
        $templates = ScheduleTemplate::where('day_of_week', $dayOfWeek)
            ->where('is_active', true) // Hanya template aktif
            ->get();

        // Ambil override pada tanggal tersebut
        $overrides = ScheduleOverride::where('override_date', $selectedDate)->get();

        $doctorsSchedules = [];
        // Waktu sekarang dalam GMT+7
        $now = Carbon::now('Asia/Jakarta');

        foreach ($templates as $template) {
            // Generate sessions untuk setiap template
            $sessions = [];
            $currentStartTime = strtotime($template->start_time);
            $endTime = strtotime($template->end_time);

            while ($currentStartTime < $endTime) {
                $nextStartTime = $currentStartTime + 3600; // 1 jam

                // Konversi ke format jam
                $timeStart = date('H:i', $currentStartTime);
                $timeEnd = date('H:i', $nextStartTime);

                // Abaikan sesi yang sudah lewat jika tanggal hari ini
                if ($selectedDate === $now->toDateString() && $currentStartTime <= strtotime($now->format('H:i'))) {
                    $currentStartTime = $nextStartTime;
                    continue;
                }
                // Cek apakah ada override
                $override = $overrides->first(function ($override) use ($template, $currentStartTime, $nextStartTime) {
                    return $override->doctor_id == $template->doctor_id &&
                        (!$override->start_time || strtotime($override->start_time) <= $currentStartTime) &&
                        (!$override->end_time || strtotime($override->end_time) >= $nextStartTime);
                });

                // Cek apakah sesi sudah terreservasi
                $isReserved = MedicalRecord::where('doctor_id', $template->doctor_id)
                    ->where('tanggal_reservasi', $selectedDate)
                    ->where('jam_mulai', date('H:i', $currentStartTime))
                    ->exists();

                // Jika sudah terreservasi, skip jadwal ini
                if ($isReserved) {
                    $currentStartTime = $nextStartTime;
                    continue;
                }

                // Jika ada override dan tidak available, skip jadwal ini
                if (!($override && !$override->is_available)) {
                    $sessions[] = [
                        'time_start' => date('H:i', $currentStartTime),
                        'time_end' => date('H:i', $nextStartTime),
                        'is_available' => $override ? $override->is_available : true
                    ];
                }

                $currentStartTime = $nextStartTime;
            }

            if (!empty($sessions)) {
                $doctorsSchedules[] = [
                    'doctor' => [
                        'id' => $template->doctor->id,
                        'name' => $template->doctor->name
                    ],
                    'schedules' => $sessions
                ];
            }
        }

        return response()->json([
            'date' => $selectedDate,
            'day_of_week' => $dayOfWeek,
            'doctors' => $doctorsSchedules
        ]);
    }


    private function generateSchedules($templates, $overrides)
    {
        $schedules = [];

        foreach ($templates as $template) {
            $sessions = $this->generateSessions($template);

            foreach ($sessions as $session) {
                // Apply overrides
                $isAvailable = $this->applyOverrides($session, $overrides);

                if ($isAvailable) {
                    $schedules[] = $session;
                }
            }
        }

        return $schedules;
    }

    private function generateSessions($template)
    {
        $sessions = [];
        $currentStartTime = strtotime($template->start_time);
        $endTime = strtotime($template->end_time);

        while ($currentStartTime < $endTime) {
            $nextStartTime = $currentStartTime + 3600; // Add 1 hour

            $sessions[] = [
                'doctor' => $template->doctor,
                'date' => now()->next($template->day_of_week)->format('Y-m-d'),
                'time_start' => date('H:i', $currentStartTime),
                'time_end' => date('H:i', $nextStartTime),
                'is_available' => true,
            ];

            $currentStartTime = $nextStartTime;
        }

        return $sessions;
    }

    private function applyOverrides($session, $overrides)
    {
        foreach ($overrides as $override) {
            if (
                $override->doctor_id == $session['doctor']->id &&
                $override->override_date == $session['date']
            ) {
                $overrideStartTime = strtotime($override->start_time);
                $overrideEndTime = strtotime($override->end_time);

                $sessionStartTime = strtotime($session['time_start']);
                $sessionEndTime = strtotime($session['time_end']);

                // Jika sesi jatuh dalam rentang override, terapkan ketersediaan
                if (
                    (!$override->start_time || $overrideStartTime <= $sessionStartTime) &&
                    (!$override->end_time || $overrideEndTime >= $sessionEndTime)
                ) {
                    return $override->is_available;
                }
            }
        }

        return true;
    }

    // public function storeReservation(Request $request)
    // {
    //     // Validasi form input
    //     $request->validate([
    //         'patient_id' => 'required|integer',
    //         'doctor_id' => 'required|integer',
    //         'tanggal_reservasi' => 'required|date',
    //         'jam_mulai' => 'required|date_format:H:i',
    //         'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
    //     ]);

    //     // Proses penyimpanan reservasi
    //     $reservation = Reservation::create([
    //         'patient_id' => $request->patient_id,
    //         'doctor_id' => $request->doctor_id,
    //         'tanggal_reservasi' => $request->tanggal_reservasi,
    //         'jam_mulai' => $request->jam_mulai,
    //         'jam_selesai' => $request->jam_selesai,
    //     ]);

    //     // Menyimpan flash message ke session
    //     session()->flash('success', 'Reservasi berhasil dibuat. Silakan cek data reservasi.');

    //     return redirect()->route('dashboard.schedules.index'); // Redirect kembali ke halaman jadwal
    // }

    public function getAvailableTimes(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $selectedDate = $request->date;
        $dayOfWeek = date('l', strtotime($selectedDate));

        $templates = ScheduleTemplate::where('day_of_week', $dayOfWeek)->get();
        $overrides = ScheduleOverride::where('override_date', $selectedDate)->get();
        $schedules = $this->generateSchedules($templates, $overrides);

        return response()->json(['date' => $selectedDate, 'schedules' => $schedules]);
    }

    public function editReservation($id)
    {
        $reservation = MedicalRecord::with(['patient', 'doctor'])->findOrFail($id);
        $patients = Patient::all();
        $doctors = ScheduleTemplate::with('doctor')->get()->pluck('doctor')->unique();

        return view('dashboard.reservations.edit', compact('reservation', 'patients', 'doctors'));
    }

    // public function updateReservation(Request $request, $id)
    // {
    //     $request->validate([
    //         'patient_id' => 'required|integer',
    //         'doctor_id' => 'required|integer',
    //         'tanggal_reservasi' => 'required|date',
    //         'jam_mulai' => 'required|date_format:H:i',
    //         'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
    //     ]);

    //     dd($request);
    //     $isReserved = Reservation::where('doctor_id', $request->doctor_id)
    //         ->where('tanggal_reservasi', $request->tanggal_reservasi)
    //         ->where('jam_mulai', $request->jam_mulai)
    //         ->where('id', '!=', $id)
    //         ->exists();

    //     if ($isReserved) {
    //         return redirect()->back()->withErrors(['error' => 'Jadwal sudah direservasi. Pilih jadwal lain.']);
    //     }

    //     $reservation = Reservation::findOrFail($id);
    //     $reservation->update($request->all());

    //     return redirect()->route('dashboard.reservations.index')->with('success', 'Reservasi berhasil diperbarui.');
    // }


    //     public function editReservation($id)
    // {
    //     $reservation = Reservation::findOrFail($id);
    //     $patients = Patient::all(); // Ambil daftar pasien
    //     $doctors = ScheduleTemplate::distinct()->pluck('doctor_id'); // Ambil daftar dokter
    //     return view('dashboard.schedules.edit', compact('reservation', 'patients', 'doctors'));
    // }


    // public function updateReservation(Request $request, $id)
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

    public function saveReservation(Request $request, $id = null)
    {
        // dd('masuk save reservation');
        // Validasi input
        $request->validate([
            'patient_id' => 'required|integer',
            'doctor_id' => 'required|integer',
            'tanggal_reservasi' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);
        // dd($request);

        // Jika ID diberikan, update reservasi yang ada, jika tidak buat baru
        $reservation = $id ? MedicalRecord::findOrFail($id) : new MedicalRecord();

        // dd('masuk save');
        $admin = auth()->id();
        if ($admin) {
            $reservation->fill([
                'status_konfirmasi' => 'Belum Konfirmasi',
            ]);
        }

        $reservation->fill([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'tanggal_reservasi' => $request->tanggal_reservasi,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        $reservation->save();
        // dd('reservation saved to database');

        // Membuat detail reservasi untuk email
        $startDateTime = Carbon::parse($request->tanggal_reservasi . ' ' . $request->jam_mulai);
        $endDateTime = Carbon::parse($request->tanggal_reservasi . ' ' . $request->jam_selesai);

        // dd($startDateTime);
        // Ambil data dokter dari tabel users menggunakan doctor_id
        $doctorUser = User::find($request->doctor_id);
        // dd($doctorUser);

        // Ambil juga data pasien untuk deskripsi, jika diperlukan
        $patient = \App\Models\Patient::find($request->patient_id);

        if ($doctorUser && $patient) { // Pastikan user dokter dan pasien ditemukan
            $reservationDetails = [
                'title' => 'Reservasi Dokter ' . $doctorUser->name, // Gunakan nama dari tabel users
                'description' => 'Reservasi dengan dokter ' . $doctorUser->name . ' untuk pasien ' . $patient->nama . '.', // Sesuaikan dengan nama field di model Patient
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'doctor_name' => $doctorUser->name, // Gunakan nama dari tabel users
                'doctor_email' => $doctorUser->email, // Ambil email dari tabel users
                'patient_name' => $patient->nama, // Nama pasien untuk deskripsi acara
                // 'patient_email' tidak perlu lagi di sini jika tidak mengirim ke pasien
            ];

            // Kirim email hanya ke dokter
            Mail::to($doctorUser->email)
                ->send(new ReservationInvitation($reservationDetails));
                // dd('mail sent!!!!');
        }

        // Menyimpan flash message ke session
        $message = $id ? 'Reservation Updated' : 'Reservation Created.';
        session()->flash('success', $message);


        return redirect()->route('dashboard.reservations.index');
    }

    // Store Reservation memanggil saveReservation
    public function storeReservation(Request $request)
    {
        return $this->saveReservation($request);
    }

    // Update Reservation memanggil saveReservation dengan ID reservasi
    public function updateReservation(Request $request, $id)
    {
        // dd($request->all());
        return $this->saveReservation($request, $id);
    }
}
