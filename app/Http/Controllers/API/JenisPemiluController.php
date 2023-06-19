<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JenisPemilu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Throwable;

class JenisPemiluController extends Controller
{
    public function getAll()
    {
        $kode = 200;
        try {
            $jenis = JenisPemilu::all();
            if (is_null($jenis)) {
                $kode = 404;
                throw new Exception('Data Jenis Pemilu Tidak Ditemukan');
            }

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data Jenis Pemilu Berhasil Diambil',
                'data' => $jenis,
            ], 200);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
                'data' => null,
            ], $kode);
        }
    }

    public function create(Request $request)
    {
        $kode = 200;
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini');
            }
            $request->validate([
                'nama' => 'required|string'
            ]);

            $jenisPemilu = JenisPemilu::create([
                'nama' => $request->nama,
            ]);
            if (!$jenisPemilu) {
                $kode = 500;
                throw new Exception('Jenis Pemilu Gagal dibuat');
            }

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data Jenis Pemilu berhasil dibuat',
                'data' => $jenisPemilu
            ]);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $kode = 200;
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini');
            }
            $request->validate([
                'nama' => 'required|string'
            ]);

            $data = JenisPemilu::find($id);

            if ($data) {
                $kode = 500;
                throw new Exception('Data Tidak ditemukan');
            }

            $data->nama = $request->nama;
            $data->save();

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data Jenis Pemilu Berhasil Diperbaharui',
                'data' => $data
            ], 200);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        $kode = 200;
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini');
            }

            $data = JenisPemilu::find($id);
            if ($data) {
                $kode = 500;
                throw new Exception('Data Tidak ditemukan');
            }

            $data->delete();

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data Jenis Pemilu Berhasil Dihapus'
            ]);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ]);
        }
    }
}
