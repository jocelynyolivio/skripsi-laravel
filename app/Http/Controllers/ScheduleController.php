<?php

namespace App\Http\Controllers;

use App\Models\Schedules;
use Illuminate\Http\Request;
use App\Models\ScheduleOverride;
use App\Models\ScheduleTemplate;

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
        $dayOfWeek = date('l', strtotime($selectedDate));

        // Ambil template berdasarkan hari
        $templates = ScheduleTemplate::where('day_of_week', $dayOfWeek)->get();
        
        // Ambil override pada tanggal tersebut
        $overrides = ScheduleOverride::where('override_date', $selectedDate)->get();

        $doctorsSchedules = [];

        foreach ($templates as $template) {
            // Generate sessions untuk setiap template
            $sessions = [];
            $currentStartTime = strtotime($template->start_time);
            $endTime = strtotime($template->end_time);

            while ($currentStartTime < $endTime) {
                $nextStartTime = $currentStartTime + 3600; // 1 jam

                // Cek apakah ada override
                $override = $overrides->first(function($override) use ($template, $currentStartTime, $nextStartTime) {
                    return $override->doctor_id == $template->doctor_id &&
                           (!$override->start_time || strtotime($override->start_time) <= $currentStartTime) &&
                           (!$override->end_time || strtotime($override->end_time) >= $nextStartTime);
                });

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
                    'doctor' => $template->doctor,
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
}


