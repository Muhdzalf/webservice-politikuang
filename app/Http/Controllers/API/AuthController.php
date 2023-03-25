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

class AuthController extends Controller
{
    // Registrasi Masyarakat
    public function register(Request $request)
    {
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
                'kewarganegaraan' => $request->kewarganegaraan,
                'user_id' => $user->id
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
                    'status' => false,
                    'message' => 'Proses login gagal, siahkan cek kembali email dan password Anda'
                ],
                401
            );
        }

        // mencari data yang sesuai dengan email
        $user = User::where('email', $request->email)->first();

        if ($user->role == 'masyarakat') {
            $data = User::where('id', $user->id)->with('masyarakat')->first();
        }

        if ($user->role == 'administrator') {
            $data = User::where('id', $user->id)->with('administrator')->first();
        }

        if ($user->role == 'pengawas') {
            $data = User::where('id', $user->id)->with('pengawas')->first();
        }

        // cek apakah password yang dimasukkan sama dengan password user yang ada di dalam database
        if (!Hash::check($request->password, $user->password, [])) {
            throw new Exception('Invalid');
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
            'data' => $data,
        ], 200);
    }


    public function logout(Request $request)
    {
        //menghapus token yang sudah aktif
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Logout Berhasil',
        ], 200);
    }
}
