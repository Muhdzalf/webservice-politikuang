<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fqa;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Throwable;

class FQAController extends Controller
{

    public function getAll()
    {
        $kode = 200;
        try {
            $fqa = Fqa::query()->filter(request(['cari']))->get();
            if (is_null($fqa)) {
                $kode = 404;
                throw new Exception('Data FQA Tidak Ditemukan');
            }

            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Data FQA berhasil diambil',
                'data' => $fqa
            ], 200);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }

    public function create(Request $request)
    {
        $kode = 200;
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini');
            }
            $request->validate([
                'pertanyaan' => 'required|string',
                'jawaban' => 'required|string'
            ]);

            $fqa = Fqa::create([
                'pertanyaan' => $request->pertanyaan,
                'jawaban' => $request->jawaban,
                'admin_id' => Auth::user()->administrator->id_admin
            ]);

            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Data FQA Berhasil Ditambahkan',
                'data' => $fqa
            ]);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }

    public function update(Request $request, $id)
    {
        $kode = 200;
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini');
            }
            $fqa = Fqa::find($id);

            $request->validate([
                'pertanyaan' => 'required|string',
                'jawaban' => 'required|string'
            ]);

            if(!$fqa){
                $kode = 404;
                throw new Exception('Data Tidak Ditemukan');
            }

            $fqa->pertanyaan = $request->pertanyaan;
            $fqa->jawaban = $request->jawaban;
            $fqa->admin_id = $fqa->admin_id;
            $fqa->save();

            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Data FQA Berhasil Diperbaharui',
                'data' => $fqa
            ], 200);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }

    public function delete($id)
    {
        $kode = 200;
        try {
            if (!Gate::allows('only-admin')) {
                $kode = 403;
                throw new Exception('Akses ditolak. Hanya admin yang memiliki akses untuk fitur ini');
            }

            $fqa = Fqa::find($id);

            if (!$fqa) {
                $kode = 404;
                throw new Exception('Data Tidak Ditemukan');
            }

            $fqa->delete();

            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Data FQA berhasil Dihapus',
            ], 200);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }
}
