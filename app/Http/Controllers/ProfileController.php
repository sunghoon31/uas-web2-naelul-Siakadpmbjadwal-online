<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Menampilkan profile user
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    // Menampilkan form edit profile
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // Update profile
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update nama & email
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Upload foto jika ada
        if ($request->hasFile('profile_photo')) {
            // hapus foto lama kalau ada
            if ($user->profile_photo && Storage::disk('public')->exists('profile/' . $user->profile_photo)) {
                Storage::disk('public')->delete('profile/' . $user->profile_photo);
            }

            $file = $request->file('profile_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profile', $filename, 'public'); // pastikan pakai disk 'public'
            $user->profile_photo = $filename;
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profile berhasil diperbarui!');
    }
}
