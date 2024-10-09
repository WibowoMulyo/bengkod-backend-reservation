<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Mendapatkan profil pengguna
    public function getProfile()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'foto_profil' => $user->foto_profil, // URL foto profil
            ]
        ]);
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validasi input untuk foto profil
        $validated = $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi tipe file gambar
        ]);

        // Meng-handle upload foto
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $path = $file->store('profile_pictures', 'public'); // Menyimpan file di folder "profile_pictures"

            // Update foto_profil di database
            $user->update([
                'foto_profil' => $path, // Simpan path foto
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diupdate',
            'data' => [
                'name' => $user->name, // Tetap tampilkan nama dan email
                'email' => $user->email,
                'foto_profil' => $user->foto_profil,
            ]
        ]);
    }

}

