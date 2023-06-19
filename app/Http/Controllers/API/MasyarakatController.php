<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Masyarakat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class MasyarakatController extends Controller
{
    // Registrasi Masyarakat
    public function register(Request $request)
    {
        try{
            $request->validate([
                'nik' => 'required|numeric|digits:16|unique:masyarakat',
                'nama' => 'required|string|max:50',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'tanggal_lahir' => 'required|date_format:Y-m-d',
                'jenis_kelamin' => 'required|string|max:1|in:L,P',
                'no_hp' => 'required|regex:/(0)[0-9]{11}/',
                'pekerjaan' => 'required|string',
                'kewarganegaraan' => 'sometimes|string',
                'role' => 'sometimes|in:pengawas,masyarakat,administrator',

                //validation Alamat
                'provinsi_id' => 'required|numeric',
                'kabupaten_kota_id' => 'required|numeric',
                'kecamatan_id' => 'required|numeric',
                'desa' => 'required|string',
                'detail_alamat' => 'required|string',
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

            $alamat = Alamat::create([
                'kecamatan_id' => $request->kecamatan_id,
                'kabupaten_kota_id' => $request->kabupaten_kota_id,
                'provinsi_id' => $request->provinsi_id,
                'desa' => $request->desa,
                'detail_alamat' => $request->detail_alamat,
            ]);

            if (!is_null($user) && !is_null($alamat)) {
                Masyarakat::create([
                    'nik' => $request->nik,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'alamat_id' => $alamat->id_alamat,
                    'pekerjaan' => $request->pekerjaan,
                    'kewarganegaraan' => $request->kewarganegaraan ?? 'Indonesia',
                    'user_id' => $user->id_user
                ]);
            }

            $user = User::where('email', $request->email)->with('masyarakat')->first();

            // membuat akses token
            $token = $user->createToken('userToken')->plainTextToken;

            // JSON response user
            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Proses Registrasi Berhasil!',
                'access_token' => $token,
                'type' => 'Bearer',
            ], 200);
        } catch(Throwable $e){
              // JSON response user
              return response()->json([
                'kode' => 500,
                'status' => false,
                'message' => 'Gagal: '.$e->getMessage(),
            ], 500);
        }
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
