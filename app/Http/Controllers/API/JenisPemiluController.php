<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JenisPemilu;
use App\Models\Pemilu;
use Illuminate\Http\Request;

use function PHPUnit\Framework\returnSelf;

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
        $request->validate([
            'nama' => 'required|string'
        ]);

        $jenisPemilu = Pemilu::create([
            'nama' => $request->nama
        ]);

        return response()->json([
            'message' => 'Data Jenis Pemilu berhasil dibuat',
            'data' => $jenisPemilu
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string'
        ]);

        $data = JenisPemilu::find($id);
        $data->$request->nama;
        $data->save();

        return response()->json([
            'message' => 'Data Jenis Pemilu Berhasil Diperbaharui',
        ]);
    }

    public function delete($id)
    {
        $data = JenisPemilu::find($id);
        $data->delete();

        return response()->json([
            'message' => 'Data Jenis Pemilu Berhasil Dihapus'
        ]);
    }
}
