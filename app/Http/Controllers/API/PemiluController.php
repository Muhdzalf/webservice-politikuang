<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Pemilu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PemiluController extends Controller
{
    public function create(Request $request)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }

        $request->validate([
            'nama' => 'required|string',
            'tanggal_pelaksanaan' => 'required|date_format:Y-m-d',
            'waktu_pelaksanaan' => 'required|date_format:h:i',
            'jenis_id' => 'required|numeric',
            'kecamatan_id' => 'required|numeric',
            'kabupaten_id' => 'required|numeric',
            'provinsi_id' => 'required|numeric',
            'detail_alamat' => 'required|string',
        ]);

        $alamat = Alamat::create([
            'kecamatan_id' => $request->kecamatan_id,
            'kabupaten_id' => $request->kabupaten_id,
            'provinsi_id' => $request->provinsi_id,
            'detail_alamat' => $request->detail_alamat,
        ]);

        $alamatId = $alamat->id;

        $pemilu = Pemilu::create([
            'nama' => $request->nama,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
            'jenis_id' => $request->jenis_id,
            'alamat_id' => $alamatId,
        ]);

        return response()->json([
            'message' => 'data pemilu berhasil ditambahkan',
            'data' => $pemilu
        ]);
    }

    public function getAll()
    {

        // $data = Pemilu::all();

        // if ($id) {
        //     $data = Pemilu::find($id);
        //     if ($data) {
        //         return response()->json([
        //             'message' => 'Data Pemilu Berhasil Ditemukan',
        //             'data' => $data
        //         ]);
        //     } else {
        //         return response()->json([
        //             'message' => 'Data Pemilu Tidak Ditemukan',
        //             'data' => null
        //         ], 404);
        //     }
        // }

        // if ($nama || $id) {
        $data = Pemilu::query()->search(request(['nama', 'id']))->get();
        if (count($data) < 1) {
            return response()->json([
                'message' => 'Data Pemilu Tidak Ditemukan',
                'data' => null
            ], 404);
        }
        // }

        return response()->json([
            'message' => 'Data Pemilu Berhasil Diambil',
            'data' => $data
        ]);
    }

    public function updatePemilu(Request $request, $id)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }
        // MEncari data pemilu yang sesuai dengan id
        $pemilu = Pemilu::find($id);

        $pemilu->nama = $request->nama;
        $pemilu->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
        $pemilu->waktu_pelaksanaan = $request->waktu_pelaksanaan;
        $pemilu->jenis_id = $request->jenis_id;

        $alamat = Alamat::find($pemilu->alamat_id);

        $alamat->provinsi_id = $request->provinsi_id;
        $alamat->kabupaten_id = $request->kabupaten_id;
        $alamat->kecamatan_id = $request->kecamatan_id;
        $alamat->detail_alamat = $request->detail_alamat;

        $alamat->save();
        $pemilu->save();

        return response()->json([
            'message' => 'Data Pemilu Berhasil Diperbaharui',
            'data' => $pemilu,
        ]);
    }

    public function details(Request $request, $id)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }

        $laporans = $request->input('show_laporan');
        $alamat = $request->input('show_alamat');
        $jenis = $request->input('show_jenis');

        // $pemilu = Pemilu::with(['laporans', 'alamat', 'jenis'])->find($id);
        $pemilu = Pemilu::find($id);

        if ($laporans) {
            $pemilu = Pemilu::with('laporans')->find($id);
        }

        if ($alamat) {
            $pemilu = Pemilu::with('alamat')->find($id);
        }

        if ($jenis) {
            $pemilu = Pemilu::with('jenis')->find($id);
        }


        return response()->json([
            'message' => 'Data Pemilu Berhasil Diambil',
            'data' => $pemilu
        ]);
    }

    public function deletePemilu($id)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
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
