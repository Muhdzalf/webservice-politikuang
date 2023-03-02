<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdministratorController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'no_hp' => 'required|regex:/(0)[0-9]{11}/',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            // konversi password kedalam bentuk hash
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'role' => 'administrator',
        ]);

        Admin::create([
            'user_id' => $user->id
        ]);

        $user = User::where('email', $request->email)->with('administrator')->first();

        // JSON response user
        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Proses Registrasi Admin Berhasil!',
            'data' => $user,
        ], 200);
    }
}
