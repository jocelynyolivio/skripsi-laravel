<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->input('role');
        $search = $request->input('search');

        $query = User::query();

        if ($role) {
            $query->where('role_id', $role);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%");
            });
        }

        $users = $query->latest()->get();

        return view('dashboard.masters.index', compact('users', 'role', 'search'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard.masters.edit', compact('user'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role_id' => 'required|exists:roles,id',
                'tempat_lahir' => 'nullable|string|max:255',
                'tanggal_lahir' => 'nullable|date',
                'tanggal_bergabung' => 'required|date',
                'nomor_sip' => 'nullable|string|max:255',
                'nik' => 'required|string|max:255|unique:users',
                'nomor_telepon' => 'required|string|max:20',
                'alamat' => 'required|string',
                'nomor_rekening' => 'required|string|max:255|unique:users',
                'deskripsi' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            // dd($validated);

            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('profile-photos', 'public');
            }

            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = $request->has('is_active');

            User::create($validated);

            return redirect()->route('dashboard.masters.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
                'role_id' => 'nullable|exists:roles,id',
                'tempat_lahir' => 'nullable|string|max:255',
                'tanggal_lahir' => 'nullable|date',
                'tanggal_bergabung' => 'nullable|date',
                'nomor_sip' => 'nullable|string|max:255',
                'nik' => 'nullable|string|max:255|unique:users,nik,' . $user->id,
                'nomor_telepon' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'nomor_rekening' => 'nullable|string|max:255|unique:users,nomor_rekening,' . $user->id,
                'deskripsi' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'boolean'
            ]);

            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
                $validated['photo'] = $request->file('photo')->store('profile-photos', 'public');
            }

            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['is_active'] = $request->has('is_active');

            $user->update($validated);

            return redirect()->route('dashboard.masters.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete photo if exists
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return redirect()->route('dashboard.masters.index')
            ->with('success', 'User deleted successfully.');
    }


    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('dashboard.masters.show', compact('user'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard.masters.index')->with('error', 'User not found.');
        }
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'User status updated successfully.');
    }

    public function create()
    {
        return view('dashboard.masters.create');
    }
}
