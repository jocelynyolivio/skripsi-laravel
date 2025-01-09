<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'title' => 'Edit Profile',
            'user' => auth()->guard('patient')->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->guard('patient')->user();
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|file|max:1024',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ];

        $validatedData = $request->validate($rules);

        // Handle Password Update
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
            }
            
            if ($request->filled('new_password')) {
                $validatedData['password'] = Hash::make($request->new_password);
            }
        }

        // Handle Image Upload
        if ($request->file('image')) {
            if ($user->image) {
                Storage::delete($user->image);
            }
            $validatedData['image'] = $request->file('image')->store('profile-images');
        }

        $user->update($validatedData);

        return redirect()->route('profile.edit')
            ->with('success', 'Profile berhasil diperbarui!');
    }
} 