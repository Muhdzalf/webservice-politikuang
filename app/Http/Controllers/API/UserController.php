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
        $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email',
            'tanggal_lahir' => 'required|date_format:Y-m-d',
            'jenis_kelamin' => 'required|string|max:1|in:L,P',
            'no_hp' => 'required|regex:/(0)[0-9]{11}/',
            'alamat' => 'required|string',
            'pekerjaan' => 'required|string',
        ]);

        $user = $request->user();

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;

        $user->save();

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => "data berhasil diperbaharui",
            'data' => $user
        ]);
    }
}
