<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Registrasi
    public function register(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric|digits:16|unique:users',
            'nama' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'tanggal_lahir' => 'required|date_format:Y-m-d',
            'jenis_kelamin' => 'required|string|max:1|in:L,P',
            'no_hp' => 'required|regex:/(0)[0-9]{11}/',
            'alamat' => 'required|string',
            'pekerjaan' => 'required|string',
            'kewarganegaraan' => 'required|string',
            'role' => 'required|in:petugas,masyarakat,administrator'
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'email' => $request->email,
            // konversi password kedalam bentuk hash
            'password' => Hash::make($request->password),
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'pekerjaan' => $request->pekerjaan,
            'kewarganegaraan' => $request->kewarganegaraan,
            'role' => $request->role,
        ]);

        $user = User::where('email', $request->email)->first();

        // membuat akses token
        $token = $user->createToken('userToken')->plainTextToken;

        // JSON response user
        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Proses Registrasi Berhasil!',
            'access_token' => $token,
            'type' => 'Bearer',
            'data' => $user,
        ], 200);
    }

    public function login(Request $request)
    {

        // validasi email sama password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credential = $request->only('email', 'password');
        if (!Auth::attempt($credential)) {
            return response()->json(
                [
                    'kode' => 401,
                    'status' => 'Unauthorized',
                    'message' => 'Proses login gagal, siahkan cek kembali email dan password Anda'
                ],
                401
            );
        }

        // mencari data yang sesuai dengan email
        $user = User::where('email', $request->email)->first();

        // cek apakah password yang dimasukkan sama dengan password user yang ada di dalam database
        if (!Hash::check($request->password, $user->password, [])) {
            throw new Exception('Invalid');
        }
        // membuat akses token
        $token = $user->createToken('usertoken')->plainTextToken;

        // return berhasil
        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Login Berhasil',
            'akses token' => $token,
            'token type' => 'bearer',
            'data' => $user,
        ], 200);
    }


    public function logout(Request $request)
    {
        //menghapus token yang sudah aktif
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Logout Berhasil',
        ], 200);
    }
}
