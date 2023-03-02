<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fqa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FQAController extends Controller
{

    public function getAll()
    {
        $fqa = Fqa::query()->filter(request(['cari']))->get();
        if (count($fqa) < 1) {
            return response()->json([
                'kode' => 404,
                'status' => 'Not Found',
                'message' => 'Data FQA yang anda cari tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Data FQA berhasil diambil',
            'data' => $fqa
        ], 200);
    }

    public function create(Request $request)
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Anda tidak memiliki akses untuk fitur ini, Hanya admin yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $request->validate([
            'pertanyaan' => 'required|string',
            'jawaban' => 'required|string'
        ]);

        $fqa = Fqa::create([
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban
        ]);

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Data FQA Berhasil Ditambahkan',
            'data' => $fqa
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Anda tidak memiliki akses untuk fitur ini, Hanya admin yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $fqa = Fqa::find($id);

        $request->validate([
            'pertanyaan' => 'required|string',
            'jawaban' => 'required|string'
        ]);

        $fqa->pertanyaan = $request->pertanyaan;
        $fqa->jawaban = $request->jawaban;
        $fqa->save();

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Data FQA Berhasil Diperbaharui',
            'data' => $fqa
        ], 200);
    }

    public function delete($id)
    {
        if (!Gate::allows('only-admin')) {
            return response()->json([
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Anda tidak memiliki akses untuk fitur ini, Hanya admin yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $fqa = Fqa::find($id);
        $fqa->delete();

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Data FQA berhasil Dihapus',
        ], 200);
    }
}
