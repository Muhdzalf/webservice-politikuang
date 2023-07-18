<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Provinsi;
use App\Models\KabupatenKota;
use Exception;

class AddressController extends Controller
{
    //
    public function getAllProvinsi()
    {
        try {
            $provinsi = Provinsi::all();

            if(is_null($provinsi)){
                throw new Exception('Data Provinsi Tidak Ditemukan');
            }

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data provinsi berhasil diambil',
                'data' => $provinsi
            ]);
        } catch (Exception $error) {
            return response()->json([
                'kode' => 404,
                'status' => false,
                'message' => 'Gagal: '. $error->getMessage(),
            ]);
        }
    }

    public function getAllKabupatenByProvinsiId($id)
    {
        try {
            $kabupaten = KabupatenKota::query()->where('provinsi_id', $id)->get();
            if(is_null($kabupaten)){
                throw new Exception('Data Provinsi Tidak Ditemukan');
            }

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'List kabupaten berhasil diambil',
                'data' => $kabupaten
            ]);
        } catch (Exception $error) {
            return response()->json([
                'kode' => 404,
                'status' => false,
                'message' => 'Gagal: '. $error->getMessage(),
            ]);
        }
    }

    public function getAllKecamatanByKabupatenKotaId($id)
    {
        try {
            $kecamatan = kecamatan::query()->where('kabupaten_kota_id', $id)->get();
            if(is_null($kecamatan)){
                throw new Exception('Data Provinsi Tidak Ditemukan');
            }
            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'List kecamatan berhasil diambil',
                'data' => $kecamatan
            ]);
        } catch (Exception $error) {
            return response()->json([
                'kode' => 404,
                'status' => false,
                'message' => 'Gagal: '. $error->getMessage(),
            ]);
        }
    }
}
