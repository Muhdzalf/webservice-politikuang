<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Masyarakat;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $kode = 200;
        $rules = [
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
        ];

        try{
            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                $kode = 400;
                throw new Exception($validator->messages()->first());
            }

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
                'data' => [
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'role' => $user->role,
                    'access_token' => $token,
                    'type' => 'Bearer',
                ]
            ], 200);
        } catch(Throwable $err){
              // JSON response user
              return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: '.$err->getMessage(),
            ], $kode);
        }
    }

    public function login(Request $request)
    {
        $kode = 200;
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];
        try {
            // validasi email sama password
            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                $kode = 400;
                throw new Exception($validator->messages()->first());
            }

            $credential = $request->only('email', 'password');
            if (!Auth::attempt($credential)) {
                $kode= 400;
                throw new Exception('Cek kembali email dan password Anda');
            }

            // mencari data yang sesuai dengan email
            $user = User::where('email', $request->email)->first();

            // cek apakah password yang dimasukkan sama dengan password user yang ada di dalam database
            if (!Hash::check($request->password, $user->password, [])) {
                $kode = 400;
                throw new Exception('Password Salah');
            }
            // membuat akses token
            $token = $user->createToken('usertoken')->plainTextToken;

            // return berhasil
            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Login Berhasil',
                'data' => [
                    'id_user' => $user->id_user,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'role' => $user->role,
                    'access_token' => $token,
                    'type' => 'Bearer',
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
