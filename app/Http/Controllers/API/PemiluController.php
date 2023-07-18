<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\Pemilu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PemiluController extends Controller
{
    public function create(Request $request)
    {
        $kode = 200;
        $rules = [
            'nama' => 'required|string',
                'tanggal_pelaksanaan' => 'required|date_format:Y-m-d',
                'waktu_pelaksanaan' => 'required|date_format:H:i',
                'jenis_id' => 'required|numeric',

                // validation for alamat
                'kecamatan_id' => 'required|numeric',
                'kabupaten_kota_id' => 'required|numeric',
                'provinsi_id' => 'required|numeric',
                'desa' => 'required|string',
                'detail_alamat' => 'required|string'
        ];
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini.');
            }
            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                $kode = 400;
                throw new Exception($validator->messages()->first());
            }

            $alamat = Alamat::create([
                'kecamatan_id' => $request->kecamatan_id,
                'kabupaten_kota_id' => $request->kabupaten_kota_id,
                'provinsi_id' => $request->provinsi_id,
                'desa' => $request->desa,
                'detail_alamat' => $request->detail_alamat,
            ]);

            // get alamat id
            $alamatId = $alamat->id_alamat;

            if (!$alamat || !$alamatId) {
                $kode = 500;
                throw new Exception('Terdapat Kesalahan Pada Alamat');
            }

            $pemilu = Pemilu::create([
                'nama' => $request->nama,
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
                'jenis_id' => $request->jenis_id,
                'alamat_id' => $alamatId,
            ]);

            if (!$pemilu) {
                $kode = 400;
                throw new Exception('Terdapat Kesalahan pada Data Pemilu');
            }

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data pemilu berhasil ditambahkan',
                'data' => $pemilu
            ]);

        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        };
    }

    public function getAll()
    {
        $kode = 200;
        try {
            $data = Pemilu::query()->search(request(['nama', 'id']))->get();

            if (is_null($data)) {
                $kode = 404;
                throw new Exception('Data Pemilu Tidak Ditemukan');
            }

            $filteredData = $data->map(function ($item) {
                return [
                    'id_pemilu'=> $item->id_pemilu,
                    'nama'=> $item->nama,
                    'tanggal_pelaksanaan'=> $item->tanggal_pelaksanaan,
                    'waktu_pelaksanaan'=> $item->waktu_pelaksanaan,
                ];
            });

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data Pemilu Berhasil Diambil',
                'data' => $filteredData
            ]);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' .$err->getMessage(),
            ], $kode);
        };
    }

    public function update(Request $request, $id)
    {
        $kode = 200;
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya petugas yang memiliki akses untuk fitur ini.');
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

            if(!$pemilu){
                $kode = 404;
                throw new Exception('Data Pemilu Tidak Ditemukan');
            }

            // update data pemilu
            $pemilu->nama = $request->nama;
            $pemilu->tanggal_pelaksanaan = $request->tanggal_pelaksanaan;
            $pemilu->waktu_pelaksanaan = $request->waktu_pelaksanaan;
            $pemilu->jenis_id = $request->jenis_id;
            $pemilu->save();

            $alamat = Alamat::find($pemilu->alamat_id);

            if(!$alamat){
                $kode = 404;
                throw new Exception('Data Alamat Tidak Ditemukan');
            }

            //update data Alamat
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
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        };
    }

    public function details($id)
    {
        $kode = 200;
        try {
            $pemilu = Pemilu::with('alamat','jenis')->find($id);
            if (!$pemilu) {
                $kode = 404;
                throw new Exception('Data Tidak ditemukan');
            }
            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data Pemilu Berhasil Diambil',
                'data' => $pemilu
            ]);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        };
    }

    public function delete($id)
    {
        $kode = 200;
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya petugas yang memiliki akses untuk fitur ini');
            }

            $pemilu = Pemilu::find($id)->first();
            if (!$pemilu) {
                $kode = 404;
                throw new Exception('Data Tidak ditemukan');
            }
            $pemilu->delete();

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data Pemilu Berhasil Dihapus',
            ], 200);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        };
    }
}
