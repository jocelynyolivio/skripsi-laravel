<?php

namespace App\Http\Controllers;

use App\Models\Schedules;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Requests\StoreSchedulesRequest;
use App\Http\Requests\UpdateSchedulesRequest;

class SchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSchedulesRequest $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
        ]);
    
        Schedule::create([
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'time_start' => $request->time_start,
            'time_end' => $request->time_end,
            'is_available' => true,
        ]);
    
        return redirect()->route('schedule.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedules $schedules)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedules $schedules)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchedulesRequest $request, Schedules $schedules)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedules $schedules)
    {
        //
    }
}
