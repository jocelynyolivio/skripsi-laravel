<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::query();

        // Filter berdasarkan bulan jika parameter bulan diberikan
        if ($request->has('bulan')) {
            $query->whereMonth('tanggal', Carbon::parse($request->bulan)->format('m'))
                ->whereYear('tanggal', Carbon::parse($request->bulan)->format('Y'));
        }

        $attendances = $query->get();

        return view('dashboard.attendances.index', compact('attendances'));
    }

    public function create()
    {
        $users = User::all(); // Ambil data user untuk dropdown
        return view('dashboard.attendances.create', compact('users'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'no_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required'
        ]);

        // Ambil nama dari tabel users berdasarkan no_id
        $user = User::findOrFail($request->no_id);
        $validatedData['nama'] = $user->name;

        Attendance::create($validatedData);

        return redirect()->route('dashboard.attendances.index')->with('success', 'Data successfully created');
    }

    public function edit(Attendance $attendance)
    {
        $users = User::all();
        return view('dashboard.attendances.edit', compact('attendance', 'users'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validatedData = $request->validate([
            'no_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required'
        ]);

        // Ambil nama dari tabel users berdasarkan no_id
        $user = User::findOrFail($request->no_id);
        $validatedData['nama'] = $user->name;

        $attendance->update($validatedData);

        return redirect()->route('dashboard.attendances.index')->with('success', 'Data successfully updated.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('dashboard.attendances.index')->with('success', 'Data successfully deleted.');
    }
}
