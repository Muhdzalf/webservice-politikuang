<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JenisPemilu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JenisPemiluController extends Controller
{
    public function getAll()
    {
        $jenis = JenisPemilu::all();

        if (count($jenis) < 1) {
            return response()->json([
                'kode' => 404,
                'status' => 'Not Found',
                'message' => 'Data Jenis Pemilu Tidak Ditemukan',
                'data' => $jenis,
            ], 404);
        }

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data Jenis Pemilu Berhasil Diambil',
            'data' => $jenis,
        ], 200);
    }

    public function create(Request $request)
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => false,
                'message' => 'Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $request->validate([
            'nama' => 'required|string'
        ]);

        $jenisPemilu = JenisPemilu::create([
            'nama' => $request->nama
        ]);

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data Jenis Pemilu berhasil dibuat',
            'data' => $jenisPemilu
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => false,
                'message' => 'Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $request->validate([
            'nama' => 'required|string'
        ]);

        $data = JenisPemilu::find($id);
        $data->nama = $request->nama;
        $data->save();

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data Jenis Pemilu Berhasil Diperbaharui',
            'data' => $data
        ], 200);
    }

    public function delete($id)
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => false,
                'message' => 'Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $data = JenisPemilu::find($id);
        $data->delete();

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data Jenis Pemilu Berhasil Dihapus'
        ]);
    }
}
