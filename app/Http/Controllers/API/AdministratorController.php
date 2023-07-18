<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AdministratorController extends Controller
{
    public function create(Request $request)
    {
        $kode = 200;
        try{
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

            if(!$user){
                $kode = 500;
                throw new Exception('Registrasi Admin Gagal');
            }else{
                Administrator::create([
                    'user_id' => $user->id_user
                ]);
            }

            $admin = User::where('email', $request->email)->with('administrator')->first();
            // JSON response user
            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Proses Registrasi Admin Berhasil!',
                'data' => $admin,
            ], 200);
        }catch(Throwable $err){
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: '.$err->getMessage(),
            ], $kode);
        }

    }
}
