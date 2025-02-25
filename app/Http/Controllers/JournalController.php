<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $journals = JournalEntry::with('details.account')->get();

        return view('dashboard.journals.index', [
            'title' => 'Journal Entries',
            'journals' => $journals
        ]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $journal = JournalEntry::with('details.account')->findOrFail($id);

        return view('dashboard.journals.show', [
            'title' => 'Journal Details',
            'journal' => $journal
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
