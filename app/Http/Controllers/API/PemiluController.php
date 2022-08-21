<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Pemilu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PemiluController extends Controller
{
    //
    public function createPemilu(Request $request)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang mememiliki akses untuk fitur ini'
            ], 403);
        }
        $request->validate([
            'nama' => 'required|string',
            'tanggal_pelaksanaan' => 'required|date_format:d/m/y',
            'waktu_pelaksanaan' => 'required',
            'kecamatan_id' => 'required|numeric',
            'kabupaten_id' => 'required|numeric',
            'provinsi_id' => 'required|numeric',
            'keterangan' => 'required|string',
            'jenis' => 'required|string|in:pilpres,pilgub,pilkada,pildes', // Jenis
        ]);

        $alamat = Alamat::create([
            'keterangan' => $request->keterangan,
            'kecamatan_id' => $request->kecamatan_id,
            'kabupaten_id' => $request->kabupaten_id,
            'provinsi_id' => $request->provinsi_id,
        ]);

        $alamatId = $alamat->id;

        $pemilu = Pemilu::create([
            'nama' => $request->nama,
            'tanggal_pelaksanaan' => $request->tanggal,
            'waktu_pelaksanaan' => $request->waktu,
            'alamat_id' => $alamatId,
            'jenis' => $request->jenis
        ]);

        return response()->json([
            'message' => 'data pemilu berhasil ditambahkan',
            'data' => $pemilu,
        ]);
    }

    public function getAll()
    {
        $data = Pemilu::all();
        return response()->json([
            'message' => 'Data Pemilu Berhasil Diambil',
            'data' => $data
        ]);
    }

    public function updatePemilu(Request $request, $id)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang mememiliki akses untuk fitur ini'
            ], 403);
        }
        // MEncari data pemilu yang sesuai dengan id
        $pemilu = Pemilu::find($id);

        $pemilu->nama = $request->nama;
        $pemilu->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
        $pemilu->waktu_pelaksanaan = $request->waktu_pelaksanaan;
        $pemilu->jenis = $request->jenis;

        $alamat = Alamat::find($pemilu->alamat_id);

        $alamat->provinsi_id = $request->provinsi_id;
        $alamat->kabupaten_id = $request->kabupaten_id;
        $alamat->kecamatan_id = $request->kecamatan_id;
        $alamat->keterangan = $request->keterangan;

        $alamat->save();
        $pemilu->save();

        return response()->json([
            'message' => 'Data Pemilu Berhasil Diperbaharui',
            'data' => $pemilu,
        ]);
    }
    public function deletePemilu($id)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang mememiliki akses untuk fitur ini'
            ], 403);
        }
        $pemilu = Pemilu::find($id);
        $pemilu->delete();

        return response()->json([
            'message' => 'Data Pemilu Berhasil Dihapus',
            'status' => $pemilu
        ]);
    }
}
