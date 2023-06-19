<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Throwable;

class UserController extends Controller
{
    public function fetchUser()
    {
        $kode = 200;
        try{ $user = Auth::user();

            If(!$user){
                throw new Exception('Unauthenticated');
            }

            if ($user->role == 'masyarakat') {
                $data = User::where('id_user', $user->id_user)->with('masyarakat')->first();
            }

            if ($user->role == 'administrator') {
                $data = User::where('id_user', $user->id_user)->with('administrator')->first();
            }

            if ($user->role == 'pengawas') {
                $data = User::where('id_user', $user->id_user)->with('pengawas')->first();
            }

            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'data user berhasil diambil',
                'data' => $data
            ]);}catch(Throwable $err){
            return response()->json([
                'kode' => $kode,
                'status' => 'false',
                'message' => 'Gagal: '. $err->getMessage(),
            ], $kode);
        };
    }

    public function updateProfile(Request $request, User $user)
    {
        $kode = 200;
        try{}catch(Throwable $err){}
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
        $kode = 200;
        try{
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Hanya Petugas Yang dapat Mengakses Fitur Ini');
            }
            $user = User::all();
            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data User Berhasil Diambil',
                'data' => $user
            ]);
        }catch(Throwable $err){
            return response()->json([
            'kode' => $kode,
            'status' => 'false',
            'message' => 'Gagal: '.$err->getMessage(),
        ], $kode);}
    }
}
