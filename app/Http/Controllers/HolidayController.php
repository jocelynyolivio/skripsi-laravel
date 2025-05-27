<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = DB::table('holidays')->get();
        return view('dashboard.holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('dashboard.holidays.create');
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'tanggal' => 'required|date|unique:holidays,tanggal',
                'keterangan' => 'required|string|max:255',
            ]);


            DB::table('holidays')->insert([
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('dashboard.holidays.index')->with('success', 'Holiday created');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Failed : ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $holiday = DB::table('holidays')->where('id', $id)->first();
        return view('dashboard.holidays.show', compact('holiday'));
    }

    public function edit($id)
    {
        $holiday = DB::table('holidays')->where('id', $id)->first();
        return view('dashboard.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
        ]);

        DB::table('holidays')->where('id', $id)->update([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('dashboard.holidays.index')->with('success', 'Holiday updated');
    }

    public function destroy($id)
    {
        DB::table('holidays')->where('id', $id)->delete();
        return redirect()->route('dashboard.holidays.index')->with('success', 'Holiday deleted');
    }
}
