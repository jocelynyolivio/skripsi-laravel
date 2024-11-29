<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Menampilkan user berdasarkan role
    public function showByRole($role_id)
    {
        $users = User::where('role_id', $role_id)->get();
        return view('dashboard.masters.index', compact('users', 'role_id'));
    }

    // Menampilkan form edit user
    public function edit($role_id, $id)
    {
        $user = User::findOrFail($id); // Mencari user berdasarkan ID
        return view('dashboard.masters.edit', compact('user', 'role_id'));
    }

    // Menyimpan perubahan user
    public function update(Request $request, $role_id, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role_id' => 'required|integer',
        ]);

        $user = User::findOrFail($id); // Mencari user berdasarkan ID
        $user->update($request->all()); // Update data user

        return redirect()->route('dashboard.masters.role', $role_id)->with('success', 'User updated successfully!');
    }

    // Menghapus user
    public function destroy($role_id, $id)
    {
        $user = User::findOrFail($id); // Mencari user berdasarkan ID
        $user->delete(); // Menghapus user

        return redirect()->route('dashboard.masters.role', $role_id)->with('success', 'User deleted successfully!');
    }
}
