<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function fetchUser(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'data user berhasil diambil',
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        $user = Auth::user();

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->tanggal_lahir = $request->tanggal_lahir;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->no_hp = $request->no_hp;
        $user->alamat = $request->alamat;
        $user->pekerjaan = $request->pekerjaan;
        $user->kewarganegaraan = $request->kewarganegaraan;

        // $user->save();

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => "data berhasil diperbaharui",
            'data' => $user
        ]);
    }
}
