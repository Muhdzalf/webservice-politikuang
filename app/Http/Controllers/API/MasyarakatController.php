<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Masyarakat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MasyarakatController extends Controller
{
    // Registrasi
    public function registerMasyarakat(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric|digits:16|unique:masyarakat',
            'nama' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'tanggal_lahir' => 'required|date_format:Y-m-d',
            'jenis_kelamin' => 'required|string|max:1|in:L,P',
            'no_hp' => 'required|regex:/(0)[0-9]{11}/',
            'alamat' => 'required|string',
            'pekerjaan' => 'required|string',
            'kewarganegaraan' => 'sometimes|string',
            'role' => 'sometimes|in:pengawas,masyarakat,administrator'
        ]);

        // create User
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            // konversi password kedalam bentuk hash
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'role' => 'masyarakat',
        ]);

        // create masyarakat data with user_id
        Masyarakat::create([
            'nik' => $request->nik,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'pekerjaan' => $request->pekerjaan,
            'kewarganegaraan' => $request->kewarganegaraan,
            'user_id' => $user->id
        ]);

        // $masyarakat = User::with('masyarakat')->find($user->id)->get();
        $user = User::where('email', $request->email)->with('masyarakat')->first();

        // membuat akses token
        $token = $user->createToken('userToken')->plainTextToken;

        // JSON response user
        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Proses Registrasi User Berhasil!',
            'access_token' => $token,
            'type' => 'Bearer',
            'data' => $user,
        ], 200);
    }

    public function getProfile()
    {
        $user = Auth::user()->id;

        $data = Masyarakat::where('user_id', $user)->with('user')->first();

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'data user berhasil diambil',
            'data' => $data
        ]);
    }
}
