<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fqa;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FQAController extends Controller
{
    //
    public function getAll()
    {
        $fqa = Fqa::all();

        return response()->json([
            'message' => 'Data FQA berhasil diambil',
            'data' => $fqa
        ], 200);
    }
    public function create(Request $request)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang mememiliki akses untuk fitur ini'
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
            'message' => 'Data FQA Berhasil Dibuat',
            'data' => $fqa
        ]);
    }
    public function update(Request $request, $id)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang mememiliki akses untuk fitur ini'
            ], 403);
        }
        $fqa = Fqa::find($id);

        $fqa->pertanyaan = $request->pertanyaan;
        $fqa->jawaban = $request->jawaban;
        $fqa->save();

        return response()->json([
            'message' => ' Data FQA Berhasil Diperbaharui',
            'Data' => $fqa
        ], 200);
    }
    public function delete($id)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang mememiliki akses untuk fitur ini'
            ], 403);
        }
        $fqa = Fqa::find($id);
        $fqa->delete();

        return response()->json([
            'message' => 'Data FQA berhasil Dihapus',
        ]);
    }
}
