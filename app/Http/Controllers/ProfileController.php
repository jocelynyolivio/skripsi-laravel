<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    // Menampilkan profil pengguna
    public function index()
    {
        $user = Auth::user(); // Ambil data pengguna yang sedang login
        return view('dashboard.profile.index', compact('user'));
    }

    // Menampilkan form edit profil
    public function edit()
    {
        $user = Auth::user(); // Ambil data pengguna yang sedang login
        return view('dashboard.profile.edit', compact('user'));
    }

    // Mengupdate profil pengguna
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|confirmed|min:6',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('dashboard.profile.show')->with('success', 'Profile successfully updated.');
    }
}
