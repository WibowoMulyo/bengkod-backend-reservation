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
                'photo' => $user->photo,
            ]
        ]);
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validasi input untuk foto profil
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi tipe file gambar
        ]);
        
        // Meng-handle upload foto
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $path = $file->store('profile_pictures', 'public'); // Menyimpan file di folder "profile_pictures"

            // Update photo di database
            $user->update([
                'photo' => $path, // Simpan path foto
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diupdate',
            'data' => [
                'name' => $user->name, // Tetap tampilkan nama dan email meskipun tidak bisa diupdate
                'email' => $user->email,
                'photo' => $user->foto_profil,
            ]
        ]);
    }

}

