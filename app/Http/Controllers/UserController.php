<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->input('role'); // Ambil parameter 'role' dari request

        // Query untuk mengambil data sesuai filter
        $query = User::query();
        if ($role) {
            $query->where('role_id', $role);
        }

        $users = $query->get();

        return view('dashboard.masters.index', compact('users', 'role'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id); // Temukan pengguna berdasarkan ID
        return view('dashboard.masters.edit', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'tanggal_bergabung' => 'nullable|date',
            'nomor_sip' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:255|unique:users',
            'nomor_telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'nomor_rekening' => 'nullable|string|max:255|unique:users',
            'deskripsi' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
        ]);

        // dd($validated);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);
        return redirect()->route('dashboard.masters.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id = null)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'password' => 'nullable|string|min:8',
            'tanggal_bergabung' => 'nullable|date',
            'nomor_sip' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:255|unique:users,nik,' . ($id ?? 'NULL'),
            'nomor_telepon' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'nomor_rekening' => 'nullable|string|max:255|unique:users,nomor_rekening,' . ($id ?? 'NULL'),
            'deskripsi' => 'nullable|string',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        // Cek apakah user ada di database
        $user = User::find($id);

        if (!$user) {
            // Jika user tidak ditemukan, buat user baru (wajib ada password)
            $validated['password'] = Hash::make($request->password ?? 'defaultpassword123');
            $user = User::create($validated);
        } else {
            // Jika password tidak diisi, gunakan password lama
            if (!$request->filled('password')) {
                $validated['password'] = $user->password;
            } else {
                $validated['password'] = Hash::make($request->password);
            }

            // Update data
            $user->update($validated);
        }

        return redirect()->route('dashboard.masters.index')->with('success', 'User created or updated successfully.');
    }


    public function destroy($id)
    {
        // dd('hai');
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('dashboard.masters.index')->with('success', 'User deleted successfully!');
    }


    public function create()
    {
        return view('dashboard.masters.create');
    }

    // Menampilkan user berdasarkan role
    // public function showByRole($role_id)
    // {
    //     $users = User::where('role_id', $role_id)->get();
    //     return view('dashboard.masters.index', compact('users', 'role_id'));
    // }

    // Menampilkan form edit user
    // public function edit($role_id, $id)
    // {
    //     $user = User::findOrFail($id); // Mencari user berdasarkan ID
    //     return view('dashboard.masters.edit', compact('user', 'role_id'));
    // }

    // Menyimpan perubahan user
    // public function update(Request $request, $role_id, $id)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255',
    //         'role_id' => 'required|integer',
    //     ]);

    //     $user = User::findOrFail($id); // Mencari user berdasarkan ID
    //     $user->update($request->all()); // Update data user

    //     return redirect()->route('dashboard.masters.role', $role_id)->with('success', 'User updated successfully!');
    // }

    // Menghapus user
    // public function destroy($role_id, $id)
    // {
    //     $user = User::findOrFail($id); // Mencari user berdasarkan ID
    //     $user->delete(); // Menghapus user

    //     return redirect()->route('dashboard.masters.role', $role_id)->with('success', 'User deleted successfully!');
    // }

}
