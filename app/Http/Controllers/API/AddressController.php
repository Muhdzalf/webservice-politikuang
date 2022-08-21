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

    public function createProvinsi(Request $request)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Fitur ini hanya dapat ditambahkan oleh petugas'
            ]);
        }
        $request->validate([
            'nama' => 'required|string',
        ]);

        $provinsi = Provinsi::create([
            'nama' => 'Provinsi ' . $request->nama
        ]);

        return response()->json([
            'message' => 'Data Provinsi berhasil dibuat',
            'data' => $provinsi
        ]);
    }

    public function updateProvinsi(Request $request, $id)
    {
        try {
            if (!Gate::allows('only-petugas')) {
                return response()->json([
                    'message' => 'Data ini hanya bisa diperbaharui oleh petugas'
                ]);
            }
            $provinsi = Provinsi::find($id);

            $request->validate([
                'nama' => 'required|string',
            ]);

            $provinsi->nama = 'Provinsi ' . $request->nama;
            $provinsi->save();

            return response()->json([
                'message' => 'Data Provinsi berhasil diperbaharui',
                'data' => $provinsi
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Gagal Memperbaharui Data',
                'error' => $error
            ]);
        }
    }

    public function deleteProvinsi($id)
    {
        try {
            if (!Gate::allows('only-petugas')) {
                return response()->json([
                    'message' => 'Data ini hanya bisa dihapus oleh petugas'
                ]);
            }

            $provinsi = Provinsi::find($id);
            $provinsi->delete();

            return response()->json([
                'message' => 'Data Provinsi berhasil dihapus'
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Gagal Menghapus Data',
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

    public function createKabupaten(Request $request)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Fitur ini hanya dapat ditambahkan oleh petugas'
            ]);
        }
        $request->validate([
            'nama' => 'required|string',
            'provinsi_id' => 'required|string'
        ]);

        $kabupaten = Kabupaten::create([
            'nama' => $request->nama,
            'provinsi_id' => $request->provinsi_id
        ]);

        return response()->json([
            'message' => 'Data Kabupaten berhasil dibuat',
            'data' => $kabupaten
        ]);
    }


    public function updateKabupaten(Request $request, $id)
    {
        try {
            if (!Gate::allows('only-petugas')) {
                return response()->json([
                    'message' => 'Data ini hanya bisa diperbaharui oleh petugas'
                ]);
            }
            $kabupaten = Kabupaten::find($id);

            $request->validate([
                'nama' => 'required|string',
            ]);

            $kabupaten->nama = $request->nama;
            $kabupaten->save();

            return response()->json([
                'message' => 'Data Kabupaten berhasil diperbaharui',
                'data' => $kabupaten
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Gagal Memperbaharui Data',
                'error' => $error
            ]);
        }
    }

    public function deleteKabupaten($id)
    {
        try {
            if (!Gate::allows('only-petugas')) {
                return response()->json([
                    'message' => 'Data ini hanya bisa dihapus oleh petugas'
                ]);
            }

            $kabupaten = Kabupaten::find($id);
            $kabupaten->delete();

            return response()->json([
                'message' => 'Data Kabupaten berhasil dihapus'
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Gagal Menghapus Data',
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

    public function createKecamatan(Request $request)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Fitur ini hanya dapat ditambahkan oleh petugas'
            ]);
        }
        $request->validate([
            'nama' => 'required|string',
            'kabupaten_id' => 'required|string'
        ]);

        $kecamatan = Kecamatan::create([
            'nama' => $request->nama,
            'kabupaten_id' => $request->kabupaten_id,
        ]);

        return response()->json([
            'message' => 'Data Kecamatan berhasil dibuat',
            'data' => $kecamatan
        ]);
    }

    public function updateKecamatan(Request $request, $id)
    {
        try {
            if (!Gate::allows('only-petugas')) {
                return response()->json([
                    'message' => 'Data ini hanya bisa diperbaharui oleh petugas'
                ]);
            }
            $kecamatan = Kecamatan::find($id);

            $request->validate([
                'nama' => 'required|string',
                'kabupaten_id' => 'required'
            ]);

            $kecamatan->nama = $request->nama;
            $kecamatan->kabupaten_id = $request->kabupaten_id;
            $kecamatan->save();

            return response()->json([
                'message' => 'Data Kecamatan berhasil diperbaharui',
                'data' => $kecamatan
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Gagal Memperbaharui Data',
                'error' => $error
            ]);
        }
    }

    public function deleteKecamatan($id)
    {
        try {
            if (!Gate::allows('only-petugas')) {
                return response()->json([
                    'message' => 'Data ini hanya bisa dihapus oleh petugas'
                ]);
            }

            $kecamatan = Kecamatan::find($id);
            $kecamatan->delete();

            return response()->json([
                'message' => 'Data Kecamatan berhasil dihapus'
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Gagal Menghapus Data',
                'error' => $error
            ]);
        }
    }
}
