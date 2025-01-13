<?php

namespace App\Http\Controllers;

use App\Models\ScheduleTemplate;
use App\Models\ScheduleOverride;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $templates = ScheduleTemplate::where('is_active', true)->get();
        $overrides = ScheduleOverride::all();

        $schedules = $this->generateSchedules($templates, $overrides);

        return view('dashboard.schedules.index', [
            'title' => 'Schedule List',
            'schedules' => $schedules,
        ]);
    }

    public function getDoctorsByDate(Request $request)
{
    $request->validate([
        'date' => 'required|date',
    ]);

    $selectedDate = $request->date;
    $dayOfWeek = date('l', strtotime($selectedDate)); // Mendapatkan nama hari (e.g., Monday, Tuesday)

    // Ambil dokter berdasarkan template hari
    $templates = ScheduleTemplate::where('day_of_week', $dayOfWeek)->get();

    // Ambil semua overrides pada tanggal yang dipilih
    $overrides = ScheduleOverride::where('override_date', $selectedDate)->get();

    $doctors = $templates->filter(function ($template) use ($overrides) {
        // Cek apakah dokter memiliki override yang menandai "tidak tersedia"
        $isOverridden = $overrides->firstWhere('doctor_id', $template->doctor_id);

        // Jika ada override dan `is_available` adalah false, jangan masukkan
        if ($isOverridden && !$isOverridden->is_available) {
            return false;
        }

        return true;
    })->map(function ($template) {
        return $template->doctor; // Kembalikan data dokter dari template
    });

    return response()->json([
        'date' => $selectedDate,
        'day_of_week' => $dayOfWeek,
        'doctors' => $doctors,
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

}
