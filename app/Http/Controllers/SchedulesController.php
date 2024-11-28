<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Schedules;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchedulesRequest;

class SchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedules::with('doctor')->get();

        return view('dashboard.schedules.index', [
            'title' => 'Schedule List',
            'schedules' => $schedules,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }
    
        $doctors = User::where('role_id', 2)->get();
    
        return view('dashboard.schedules.create', [
            'title' => 'Add Schedule',
            'doctors' => $doctors,
        ]);
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
    
        Schedules::create([
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'time_start' => $request->time_start,
            'time_end' => $request->time_end,
            'is_available' => true,
        ]);
    
        return redirect()->route('dashboard.schedule.index')->with('success', 'Jadwal berhasil ditambahkan!');
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
    public function edit($id)
    {
        // Ambil jadwal berdasarkan ID
        $schedule = Schedules::findOrFail($id);
        // Ambil daftar dokter
        $doctors = User::where('role_id', 2)->get();

        return view('dashboard.schedules.edit', [
            'title' => 'Edit Schedule',
            'schedule' => $schedule,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedules::findOrFail($id);
    
        // if (auth()->user()->id !== $schedule->doctor_id) {
        //    abort(403, 'Unauthorized action.');
        //}
    
        // Lanjutkan dengan logika update jika semua sudah benar
        $schedule->update([
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'time_start' => $request->time_start,
            'time_end' => $request->time_end,
        ]);
    
        return redirect()->route('dashboard.schedules.index')->with('success', 'Schedule updated successfully!');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Hapus jadwal
        $schedule = Schedules::findOrFail($id);
        $schedule->delete();

        return redirect()->route('dashboard.schedules.index')->with('success', 'Schedule deleted successfully!');
    }
}
