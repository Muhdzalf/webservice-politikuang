<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pengawas;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Throwable;

class PengawasController extends Controller
{
    public function create(Request $request)
    {
        $kode = 200;
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini');
            }
            // Hanya Admin yang dapat mendaftarkan Pengawas
            $request->validate([
                'nama' => 'required|string|max:50',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'no_hp' => 'required|regex:/(0)[0-9]{11}/',
                'no_spt' => 'required|string',
                'jabatan' => 'required|string',
                'mulai_tugas' => 'required|date|date_format:Y-m-d',
                'selesai_tugas' => 'required|date|date_format:Y-m-d|after:mulai_tugas',
                'role' => 'sometimes|in:pengawas,masyarakat,administrator'
            ]);

            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                // konversi password kedalam bentuk hash
                'password' => Hash::make($request->password),
                'no_hp' => $request->no_hp,
                'role' => 'pengawas',
            ]);

            if ($user) {
                Pengawas::create([
                    'no_spt' => $request->no_spt,
                    'jabatan' => $request->jabatan,
                    'mulai_tugas' => $request->mulai_tugas,
                    'selesai_tugas' => $request->selesai_tugas,
                    'user_id' => $user->id_user
                ]);
            } else {
                $kode = 500;
                throw new Exception('Registrasi Pengawas Gagal');
            }


            $user = User::where('email', $request->email)->with('pengawas')->first();

            // JSON response user
            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Proses Registrasi Pengawas Berhasil!',
                'data' => $user,
            ], 200);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }
}
