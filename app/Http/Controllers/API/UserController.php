<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
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
                'status' => false,
                'message' => 'Gagal: '. $err->getMessage(),
            ], $kode);
        };
    }

    public function updateProfile(Request $request)
    {
        $kode = 200;
        $Msyrules = [
            'nama' => 'required|string|max:50',
            'email' => 'required|email',
            'no_hp' => 'required|regex:/(0)[0-9]{11}/',
        ];
        try{
            $user = $request->user();
            $validator = Validator::make($request->all(), $Msyrules);

            if($validator->fails()){
                $kode = 400;
                throw new Exception($validator->messages()->first());
            }


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
        }catch(Throwable $err){  return response()->json([
            'kode' => $kode,
            'status' => false,
            'message' => 'Gagal: '.$err->getMessage(),
        ], $kode);}
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

            $filteredUser = $user->map(function ($item) {
                return [
                    'id_user' => $item->id_user,
                    'nama' => $item->nama,
                    'email' => $item->email,
                    'role' => $item->role,
                ];
            });

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data User Berhasil Diambil',
                'data' => $filteredUser,
            ]);
        }catch(Throwable $err){
            return response()->json([
            'kode' => $kode,
            'status' => false,
            'message' => 'Gagal: '.$err->getMessage(),
        ], $kode);}
    }
}
