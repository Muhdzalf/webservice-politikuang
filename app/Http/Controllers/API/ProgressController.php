<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Pengawas;
use App\Models\ProgressLaporan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProgressController extends Controller
{
    public function getProgressLaporan(Request $request, $nomor_laporan)
    {
        try {
            $kode = 200;
            $latest = $request->input('latest');
            $laporan = Laporan::find($nomor_laporan);

            if(!$laporan){
                $kode = 404;
                throw new Exception('Laporan tidak ditemukan');
            }

            if (!Gate::allows('owner-and-petugas-can-open', $laporan)) {
                $kode = 403;
                throw new Exception('Anda Tidak Memiliki Akses Untuk Melihat Laporan Ini');
            }

            $progress = ProgressLaporan::where('nomor_laporan', $nomor_laporan)->get();

            if ($latest) {
                $progress = ProgressLaporan::where('nomor_laporan', $nomor_laporan)->latest()->first();
            }

            if (is_null($progress)) {
                $kode = 404;
                throw new Exception('Progress Laporan tidak ditemukan');
            }
            return response()->json([
                'kode' => $kode,
                'status' => true,
                'message' => 'Progress laporan nomor ' . $laporan->nomor_laporan,
                'data' => $progress
            ], $kode);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }

    // mengupdate hanya statusnya saja eh bukan update deng tapi nambahin status progress laporan
    public function responLaporan(Request $request, $nomor_laporan)
    {
        $rules = [
            'status' => 'required|string|in:diproses, ditolak, dikembalikan, selesai',
            'keterangan' => 'required|string',
        ];

        try{
            $kode = 200;
            if (!Gate::allows('only-petugas')) {
                $kode = 403;
                throw new Exception('Hanya petugas yang memiliki akses untuk fitur ini');
            }

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                $kode= 400;
                throw new Exception($validator->messages()->first());
            }

            $userId = Auth::user()->id_user;
            $pengawas = Pengawas::where('user_id', $userId)->first();

            if(!$userId || !$pengawas){
                $kode= 500;
                throw new Exception('Terjadi Kesalahan pada sistem');
            }

           $progress = ProgressLaporan::create([
                'nomor_laporan' => $nomor_laporan,
                'pengawas_id' => $pengawas->id_pengawas,
                'status' => $request->status,
                'keterangan' => $request->keterangan
            ]);

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'progress untuk laporan dengan nomor '.'"'.$nomor_laporan.'"'. ' berhasil ditambahkan',
                'data' => $progress
            ]);
        }catch(Throwable $err){
            return response()->json([
                'kode' => $kode,
                'status' =>  false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }

    }
}
