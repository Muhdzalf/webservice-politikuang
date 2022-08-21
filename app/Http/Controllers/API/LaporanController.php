<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Fqa;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    //

    public function getAll()
    {
    }
    public function createLaporan(Request $request)
    {
        $request->validate([
            'nomor_laporan' => 'required',
            'judul' => 'required|string',
            'waktu_kejadian' => 'required',
            'tanggal_kejadian' => 'required|date',
            'pemberi' => 'required|string',
            'penerima' => 'required|string',
            'nominal' => 'required|numeric',
            'lokasi_kejadian' => 'required|string',
            'kronologi_kejadian' => 'required|string',
            'pengirim_laporan' => 'required|string',
            'pemilu_id' => 'required|numeric'
        ]);

        $pengirimId = Auth::user()->id;

        $laporan = Laporan::create([
            'nomor_laporan' => $request->nama,
            'judul' => $request->judul,
            'waktu_kejadian' => $request->waktu_kejadian,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'pemberi' => $request->pemberi,
            'penerima' => $request->penerima,
            'nominal' => $request->nominal,
            'lokasi_kejadian' => $request->lokasi_kejadian,
            'kronologi_kejadian' => $request->kronologi_kejadian,
            'pengirim_laporan' => $pengirimId,
            'pemilu_id' => $request->pemilu_id
        ]);

        return response()->json([
            'message' => 'Laporan Berhasil dibuat',
            'data' => $laporan
        ]);
    }
    public function update(Request $request, $id)
    {
    }
    public function delete($id)
    {
        $laporan = Laporan::find($id);
        $laporan->delete();
    }
}
