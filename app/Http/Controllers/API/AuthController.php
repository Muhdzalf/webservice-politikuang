<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alamat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // try {
        // $provinsi = AddressController::getAllProvinsi();
        // $kabupaten = AddressController::getAllKabupaten();
        // $kecamatan = AddressController::getAllKecamatan();

        $request->validate([
            'nama' => 'required|string|max:50',
            'nik' => 'required|numeric',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|min:1',
            'nomor_tlp' => 'required|numeric',
            'kecamatan_id' => 'required',
            'kabupaten_id' => 'required',
            'provinsi_id' => 'required',
            'keterangan' => 'required|string',
            'pekerjaan' => 'required|string',
            'kewarganegaraan' => 'required|string',
            'role' => 'required'
        ]);

        $alamat = Alamat::create([
            'keterangan' => $request->keterangan,
            'kecamatan_id' => $request->kecamatan_id,
            'kabupaten_id' => $request->kabupaten_id,
            'provinsi_id' => $request->provinsi_id,

        ]);

        $alamatId = $alamat->id;

        $user = User::create([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'email' => $request->email,
            // konversi password kedalam bentuk hash
            'password' => Hash::make($request->password),
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nomor_tlp' => $request->nomor_tlp,
            'alamat_id' => $alamatId,
            'pekerjaan' => $request->pekerjaan,
            'kewarganegaraan' => $request->kewarganegaraan,
            'role' => $request->role,
        ]);

        $user = User::where('email', $request->email)->first();

        // membuat akses token
        $token = $user->createToken('userToken')->plainTextToken;

        // JSON response user
        return response()->json([
            'message' => 'Proses Registrasi Berhasil!',
            'status code' => 200,
            'data' => $user,
            'access_token' => $token,
            'type' => 'Bearer'
        ]);
        // } catch (Exception $error) {
        //     return response()->json(
        //         [
        //             'message' => 'Registrasi Gagal',
        //             'error' => $error,
        //         ]
        //     );
        // }
    }

    public function login(Request $request)
    {
        // try {
        // validasi email sama password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credential = $request->only('email', 'password');
        if (!Auth::attempt($credential)) {
            return response()->json(
                [
                    'message' => 'Unauthorized'
                ],
                401
            );
        }

        // mencari data yang sesuai dengan email
        $user = User::where('email', $request->email)->first();

        // cek apakah password yang dimasukkan sama dengan password user yang ada di dalam database
        if (!Hash::check($request->password, $user->password, [])) {
            throw new Exception('Invalid');
        }
        // membuat akses token
        $token = $user->createToken('usertoken')->plainTextToken;

        // return berhasil
        return response()->json([
            'message' => 'Login Berhasil',
            'data' => $user,
            'akses token' => $token,
            'token type' => 'bearer',
        ]);
        // } catch (Exception $error) {
        //     return response()->json([
        //         'message' => 'login gagal',
        //         'error' => $error,
        //     ]);
        // }
    }

    public function fetchUser(Request $request)
    {
        return response()->json([
            'message' => 'data user berhasil diambil',
            'data' => $request->user()
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
    }


    public function logout(Request $request)
    {
        // try {
        //menghapus token yang sudah aktif
        $token = $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Berhasil',
            'akses token' => $token
        ]);
        // } catch (Exception $error) {
        //     return response()->json([
        //         'message' => 'Logout Gagal',
        //         'error' => $error
        //     ]);
        // }
    }
}
