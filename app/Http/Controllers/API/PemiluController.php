<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Pemilu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PemiluController extends Controller
{
    public function create(Request $request)
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => false,
                'message' => 'Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini.'
            ], 403);
        }

        $request->validate([
            'nama' => 'required|string',
            'tanggal_pelaksanaan' => 'required|date_format:Y-m-d',
            'waktu_pelaksanaan' => 'required|date_format:H:i',
            'jenis_id' => 'required|numeric',

            // validation for alamat
            'kecamatan_id' => 'required|numeric',
            'kabupaten_kota_id' => 'required|numeric',
            'provinsi_id' => 'required|numeric',
            'desa' => 'required|string',
        ]);

        //default alamat
        $alamatId = 0;

        $alamat = Alamat::create([
            'kecamatan_id' => $request->kecamatan_id,
            'kabupaten_kota_id' => $request->kabupaten_kota_id,
            'provinsi_id' => $request->provinsi_id,
            'desa' => $request->desa,
        ]);

        if (!$alamat) {
            return response()->json([
                'kode' => 500,
                'status' => false,
                'message' => 'Terdapat kesalahan pada alamat',
            ]);
        }

        // get alamat id
        $alamatId = $alamat->id_alamat;

        $pemilu = Pemilu::create([
            'nama' => $request->nama,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
            'jenis_id' => $request->jenis_id,
            'alamat_id' => $alamatId,
        ]);

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data pemilu berhasil ditambahkan',
            'data' => $pemilu
        ]);
    }

    public function getAll()
    {

        $data = Pemilu::query()->search(request(['nama', 'id']))->get();
        if (count($data) < 1) {
            return response()->json([
                'kode' => 404,
                'status' => false,
                'message' => 'Data Pemilu Tidak Ditemukan',
            ], 404);
        }
        // }

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data Pemilu Berhasil Diambil',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => false,
                'message' => 'Akses ditolak. Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }

        //validation
        $request->validate([
            'nama' => 'required|string',
            'tanggal_pelaksanaan' => 'required|date_format:Y-m-d',
            'jenis_id' => 'required|numeric',

            // validation for alamat
            'kecamatan_id' => 'required|numeric',
            'kabupaten_kota_id' => 'required|numeric',
            'provinsi_id' => 'required|numeric',
            'desa' => 'required|string',
        ]);

        // Mencari data pemilu yang sesuai dengan id
        $pemilu = Pemilu::find($id);

        // update data pemilu
        $pemilu->nama = $request->nama;
        $pemilu->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
        $pemilu->waktu_pelaksanaan = $request->waktu_pelaksanaan;
        $pemilu->jenis_id = $request->jenis_id;
        $pemilu->save();

        $alamat = Alamat::find($pemilu->alamat_id);

        //update data provinsi
        $alamat->provinsi_id = $request->provinsi_id;
        $alamat->kabupaten_kota_id = $request->kabupaten_kota_id;
        $alamat->kecamatan_id = $request->kecamatan_id;
        $alamat->desa = $request->desa;
        $alamat->save();

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data Pemilu Berhasil Diperbaharui',
            'data' => $pemilu,
        ]);
    }

    public function details($id)
    {
        // if (!Gate::allows('only-admin')) {
        //     return response()->json([
        //         'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
        //     ], 403);
        // }

        $pemilu = Pemilu::find($id)->first();

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data Pemilu Berhasil Diambil',
            'data' => $pemilu
        ]);
    }

    public function delete($id)
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => false,
                'message' => 'Akses ditolak. Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }

        $pemilu = Pemilu::find($id)->first();
        $pemilu->delete();

        return response()->json([
            'kode' => 200,
            'status' => true,
            'message' => 'Data Pemilu Berhasil Dihapus',
        ], 200);
    }
}
