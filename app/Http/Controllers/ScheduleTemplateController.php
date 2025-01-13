<?php

namespace App\Http\Controllers;

use App\Models\ScheduleTemplate;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleTemplateController extends Controller
{
    public function index()
    {
        $templates = ScheduleTemplate::with('doctor')->get();
        return view('dashboard.schedules.templates.index', compact('templates'));
    }

    public function create()
    {
        $doctors = User::where('role_id', 2)->get();
        return view('dashboard.schedules.templates.create', compact('doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'day_of_week' => 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        ScheduleTemplate::create($request->all());

        return redirect()->route('dashboard.schedules.templates.index')->with('success', 'Schedule Template created successfully.');
    }

    public function edit(ScheduleTemplate $template)
    {
        $doctors = User::where('role_id', 2)->get();
        return view('dashboard.schedules.templates.edit', compact('template', 'doctors'));
    }

    public function update(Request $request, ScheduleTemplate $template)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'day_of_week' => 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $template->update($request->all());

        return redirect()->route('dashboard.schedules.templates.index')->with('success', 'Schedule Template updated successfully.');
    }

    public function destroy(ScheduleTemplate $template)
    {
        $template->delete();
        return redirect()->route('dashboard.schedules.templates.index')->with('success', 'Schedule Template deleted successfully.');
    }
}
