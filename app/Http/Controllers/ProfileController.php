<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.profile.index', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('dashboard.profile.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|min:8|confirmed',
                'tempat_lahir' => 'nullable|string|max:255',
                'tanggal_lahir' => 'nullable|date',
                'nik' => 'nullable|string|max:255|unique:users,nik,' . $user->id,
                'nomor_telepon' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'nomor_rekening' => 'nullable|string|max:255|unique:users,nomor_rekening,' . $user->id,
            ]);
    
            // Masukkan semua data kecuali password
            $user->fill(collect($validated)->except('password')->toArray());
    
            // Kalau password diisi, baru set password baru
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }
    
            $user->save();
    
            return redirect()->route('dashboard.profile.index')->with('success', 'Profile successfully updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
}
