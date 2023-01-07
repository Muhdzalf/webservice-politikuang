<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JenisPemilu;
use App\Models\Pemilu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JenisPemiluController extends Controller
{
    public function getAll()
    {
        $jenis = JenisPemilu::all();

        return response()->json([
            'message' => 'Data Jenis Pemilu Berhasil Diambil',
            'data' => $jenis,
        ]);
    }

    public function create(Request $request)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $request->validate([
            'nama' => 'required|string'
        ]);

        $jenisPemilu = JenisPemilu::create([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Data Jenis Pemilu berhasil dibuat',
            'data' => $jenisPemilu
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $request->validate([
            'nama' => 'required|string'
        ]);

        $data = JenisPemilu::find($id);
        $data->nama = $request->nama;
        $data->save();

        return response()->json([
            'message' => 'Data Jenis Pemilu Berhasil Diperbaharui',
        ]);
    }

    public function delete($id)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $data = JenisPemilu::find($id);
        $data->delete();

        return response()->json([
            'message' => 'Data Jenis Pemilu Berhasil Dihapus'
        ]);
    }
}
