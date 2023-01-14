<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\ProgressLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProgressController extends Controller
{
    public function getProgressLaporan($nomor_laporan)
    {
        $laporan = Laporan::find($nomor_laporan);

        if (!Gate::allows('owner-and-petugas-can-open', $laporan)) {
            return response()->json([
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Anda Tidak Memiliki Akses Untuk Melihat Laporan Ini'
            ], 403);
        }

        $progress = ProgressLaporan::where('nomor_laporan', $nomor_laporan)->get();

        if (count($progress)) {
            return response()->json([
                'kode' => 200,
                'status' => 'OK',
                'message' => 'Progress laporan nomor ' . $laporan->nomor_laporan,
                'data' => $progress
            ], 200);
        } else {
            return response()->json([
                'kode' => 404,
                'status' => 'Not Found',
                'message' => 'tidak Ditemukan!'
            ], 404);
        }
    }

    // mengupdate hanya statusnya saja eh bukan update deng tapi nambahin status progress laporan
    public function responLaporan(Request $request, $nomor_laporan)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'kode' => 404,
                'status' => 'Forbidden',
                'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }

        $request->validate([
            'status' => 'required|string|in:menunggu, diproses, ditolak, dikembalikan, selesai',
            'keterangan' => 'required|string',
        ]);

        $progress = ProgressLaporan::create([
            'nomor_laporan' => $nomor_laporan,
            'nik' => Auth::user()->nik,
            'status' => $request->status,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'progress laporan berhasil ditambahkan',
            'data' => $progress
        ]);
    }
}
