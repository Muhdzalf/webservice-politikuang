<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Provinsi;
use App\Models\Alamat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AddressController extends Controller
{
    //
    public function getAllProvinsi()
    {
        try {
            $provinsi = Provinsi::all();
            return response()->json([
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
            $kabupaten = Kabupaten::all();
            return response()->json([
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

    // Kecamatan
    public function getAllKecamatan()
    {
        try {
            $kecamatan = Kecamatan::all();
            return response()->json([
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
}
