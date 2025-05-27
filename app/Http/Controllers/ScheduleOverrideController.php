<?php

namespace App\Http\Controllers;

use App\Models\ScheduleOverride;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleOverrideController extends Controller
{
    public function index()
    {
        $overrides = ScheduleOverride::with('doctor')->get();
        return view('dashboard.schedules.overrides.index', compact('overrides'));
    }

    public function create()
    {
        $doctors = User::where('role_id', 2)->get();
        return view('dashboard.schedules.overrides.create', compact('doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'override_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_available' => 'required|boolean',
            'reason' => 'nullable|string|max:255',
        ]);

        ScheduleOverride::create($request->all());

        return redirect()->route('dashboard.schedules.overrides.index')->with('success', 'Schedule Override created successfully.');
    }

    public function edit(ScheduleOverride $override)
    {
        $doctors = User::where('role_id', 2)->get();
        return view('dashboard.schedules.overrides.edit', compact('override', 'doctors'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'doctor_id' => 'nullable|exists:users,id',
            'override_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'is_available' => 'nullable|boolean',
            'reason' => 'nullable|string|max:255',
        ]);

        // dd($request);

        $override = ScheduleOverride::findOrFail($id);
        $override->update($request->all());

        return redirect()->route('dashboard.schedules.overrides.index')
            ->with('success', 'Schedule override updated successfully.');
    }

    public function destroy(ScheduleOverride $override)
    {
        $override->delete();
        return redirect()->route('dashboard.schedules.overrides.index')->with('success', 'Schedule Override deleted successfully.');
    }
}
