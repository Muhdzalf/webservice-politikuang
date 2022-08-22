<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fqa;
use App\Models\Laporan;
use App\Models\Pemilu;
use App\Models\ProgressLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LaporanController extends Controller
{
    //

    public function getAll()
    {
        $laporan = Laporan::all();

        return response()->json([
            'message' => 'Data Laporan Berhasil Diambil',
            'data' => $laporan,
        ]);
    }
    public function createLaporan(Request $request)
    {
        $request->validate([
            'nomor_laporan' => 'required',
            'judul' => 'required|string',
            'tahun_kejadian' => 'required|date_format:Y',
            'tanggal_kejadian' => 'required|date_format:Y-m-d',
            'pemberi' => 'required|string',
            'penerima' => 'required|string',
            'nominal' => 'required|numeric',
            'lokasi_kejadian' => 'required|string',
            'kronologi_kejadian' => 'required|string',
            'bukti' => 'required|url',
            'pengirim_laporan' => 'required|string',
            'pemilu_id' => 'required|numeric'
        ]);

        // mendapatkan id user yang sedang login
        $pengirimId = Auth::user()->id;

        $laporan = Laporan::create([
            'judul' => $request->judul,
            'tahun_kejadian' => $request->tahun_kejadian,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'pemberi' => $request->pemberi,
            'penerima' => $request->penerima,
            'nominal' => $request->nominal,
            'lokasi_kejadian' => $request->lokasi_kejadian,
            'kronologi_kejadian' => $request->kronologi_kejadian,
            'bukti' => $request->bukti,
            'pengirim_laporan' => $pengirimId,
            'pemilu_id' => $request->pemilu_id
        ]);

        $pemilu = Pemilu::find($request->pemilu_id);
        $laporan = Laporan::find($request->id);

        // generate nomor laporan dengan format tahun
        $nomor = $pemilu->tanggal_pelaksanaan . '-' . $pemilu->jenis_id; //jumlah terakhir laporan pada pemilu tersebut + 1;

        $laporan->nomor_laporan = $nomor;
        $laporan->save();


        // pada saat pembuatan laporan maka otomatis akan langsung tercatat pada progress laporan
        ProgressLaporan::create([
            'laporan_id' => $laporan->id,
            'user_id' => Auth::user()->id,
            'status' => 'Dibuat',
            'keterangan' => 'Laporan telah dibuat oleh ' . Auth::user()->nama
        ]);


        return response()->json([
            'message' => 'Laporan Berhasil dibuat',
            'data' => $laporan
        ]);
    }

    // update isinya sama user
    public function updateByUser(Request $request, $id)
    {
        $laporan = Laporan::find($id);
        $laporan->judul = $request->judul;
        $laporan->tahun_kejadian = $request->tahun_kejadian;
        $laporan->tanggal_kejadian = $request->tanggal_kejadian;
        $laporan->pemberi = $request->pemberi;
        $laporan->penerima = $request->penerima;
        $laporan->nominal = $request->nominal;
        $laporan->lokasi_kejadian = $request->lokasi_kejadian;
        $laporan->kronologi_kejadian = $request->kronologi_kejadian;
        $laporan->bukti = $request->bukti;

        ProgressLaporan::create([
            'laporan_id' => $laporan->id,
            'user_id' => Auth::user()->id,
            'status' => 'Di perbaharui',
            'keterangan' => 'Laporan telah diperbaharui oleh ' . Auth::user()->nama
        ]);

        $laporan->save();

        return response()->json([
            'message' => 'Laporan Telah Diperbaharui',
            'data' => $laporan
        ]);
    }

    // mengupdate hanya statusnya saja eh bukan update deng tapi nambahin status progress laporan
    public function changeStatus(Request $request, $id)
    {
        // $progress = ProgressLaporan::where('laporan_id', $id);
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang mememiliki akses untuk fitur ini'
            ], 403);
        }
        $progress = ProgressLaporan::create([
            'laporan_id' => $id,
            'user_id' => Auth::user()->id,
            'status' => $request->status,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'message' => 'progress laporan berhasil ditambahkan',
            'data' => $progress
        ]);
    }
    public function delete($id)
    {
        $laporan = Laporan::find($id);
        $laporan->delete();
    }
}
