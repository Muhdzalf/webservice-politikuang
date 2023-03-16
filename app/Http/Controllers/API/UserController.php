<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class UserController extends Controller
{
    public function fetchUser()
    {
        $user = Auth::user();

        if ($user->role == 'masyarakat') {
            $data = User::where('id', $user->id)->with('masyarakat')->first();
        }

        if ($user->role == 'administrator') {
            $data = User::where('id', $user->id)->with('administrator')->first();
        }

        if ($user->role == 'pengawas') {
            $data = User::where('id', $user->id)->with('pengawas')->first();
        }

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'data user berhasil diambil',
            'data' => $data
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email',
            'no_hp' => 'required|regex:/(0)[0-9]{11}/',
        ]);

        $user = $request->user();

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;

        $user->save();

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => "data berhasil diperbaharui",
            'data' => $user
        ]);
    }

    public function getAllUser()
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Hanya Petugas Yang dapat Mengakses Fitur Ini',
            ], 403);
        }

        $user = User::all();

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data User Berhasil Diambil',
            'data' => $user
        ]);
    }
}
