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
            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Data provinsi berhasil diambil',
                'data' => $provinsi
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Data provinsi gagal diambil',
                'error' => $error
            ]);
        }
    }

    // Kabupaten
    public function getAllKabupaten()
    {
        try {
            $kabupaten = KabupatenKota::all();
            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Data kabupaten berhasil diambil',
                'data' => $kabupaten
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Data kabupaten gagal diambil',
                'error' => $error
            ]);
        }
    }

    public function getAllKabupatenByProvinsiId($id)
    {
        try {
            $kabupaten = KabupatenKota::query()->where('provinsi_id', $id)->get();
            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'List kabupaten berhasil diambil',
                'data' => $kabupaten
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Data kabupaten gagal diambil',
                'error' => $error
            ]);
        }
    }

    // Kecamatan
    public function getAllKecamatan()
    {
        try {
            $kecamatan = Kecamatan::all();
            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Data kecamatan berhasil diambil',
                'data' => $kecamatan
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Data kecamatan gagal diambil',
                'error' => $error
            ]);
        }
    }

    public function getAllKecamatanByKabupatenKotaId($id)
    {
        try {
            $kecamatan = kecamatan::query()->where('kabupaten_kota_id', $id)->get();
            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'List kecamatan berhasil diambil',
                'data' => $kecamatan
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Data kecamatan gagal diambil',
                'error' => $error
            ]);
        }
    }
}
