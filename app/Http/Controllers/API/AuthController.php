<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $kode = 200;
        try {
            // validasi email sama password
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            $credential = $request->only('email', 'password');
            if (!Auth::attempt($credential)) {
                $kode= 400;
                throw new Exception('cek kembali email dan password Anda');
            }

            // mencari data yang sesuai dengan email
            $user = User::where('email', $request->email)->first();

            // cek apakah password yang dimasukkan sama dengan password user yang ada di dalam database
            if (!Hash::check($request->password, $user->password, [])) {
                $kode = 500;
                throw new Exception('Password Salah');
            }
            // membuat akses token
            $token = $user->createToken('usertoken')->plainTextToken;

            // return berhasil
            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Login Berhasil',
                'access_token' => $token,
                'type' => 'Bearer',
                'data' => [
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'role' => $user->role,
                ],
            ], 200);
        } catch (Throwable $err) {
            return response()->json(
                [
                    'kode' => $kode,
                    'status' => false,
                    'message' => 'Gagal: ' . $err->getMessage(),
                ],
                $kode
            );
        }
    }

    public function logout(Request $request)
    {
        try {
            //menghapus token yang sudah aktif
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Logout Berhasil',
            ], 200);
        } catch (Throwable $errrr) {
            return response()->json([
                'kode' => 500,
                'status' => false,
                'message' => 'Gagal: ' . $errrr->getMessage(),
            ], 500);
        }
    }
}
