<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Pemilu;
use App\Models\ProgressLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LaporanController extends Controller
{
    //
    public function create(Request $request)
    {
        $request->validate([
            'nomor_laporan' => 'unique:laporan',
            'judul' => 'required|string',
            'tanggal_kejadian' => 'required|date_format:Y-m-d',
            'pemberi' => 'required|string',
            'penerima' => 'required|string',
            'nominal' => 'required|numeric',
            'alamat_kejadian' => 'required|string',
            'kronologi_kejadian' => 'required|string',
            'bukti' => 'required|url',
            'pemilu_id' => 'required|numeric'
        ]);

        // mendapatkan nik user yang sedang login
        $pengirimId = Auth::user()->nik;

        // membuat nomor laporan
        $nomorLaporan = $this->generateNomorLaporan($request->pemilu_id);

        $laporan = Laporan::create([
            'nomor_laporan' => $nomorLaporan,
            'judul' => $request->judul,
            'tanggal_kejadian' => $request->tanggal_kejadian,
            'pemberi' => $request->pemberi,
            'penerima' => $request->penerima,
            'nominal' => $request->nominal,
            'alamat_kejadian' => $request->alamat_kejadian,
            'kronologi_kejadian' => $request->kronologi_kejadian,
            'bukti' => $request->bukti,
            'pelapor' => $pengirimId,
            'pemilu_id' => $request->pemilu_id
        ]);

        // pada saat pembuatan laporan maka otomatis akan langsung tercatat pada progress laporan
        ProgressLaporan::create([
            'nomor_laporan' => $nomorLaporan,
            'nik' => Auth::user()->nik,
            'status' => 'dibuat',
            'keterangan' => 'Laporan telah dibuat oleh ' . Auth::user()->nama
        ]);

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Laporan Berhasil dibuat dengan nomor' . $laporan->nomor_laporan,
            'data' => $laporan
        ]);
    }

    // update isinya hanya bisa oleh user pemilik laporan
    public function update(Request $request, $nomor_laporan, Laporan $laporan)
    {
        $laporan = Laporan::find($nomor_laporan);

        if (!Gate::allows('isOwner', $laporan)) {
            return response()->json([
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Hanya Pebuat laporan yang dapat mengupdate laporan ini'
            ], 403);
        }

        $laporan->judul = $request->judul;
        $laporan->tanggal_kejadian = $request->tanggal_kejadian;
        $laporan->pemberi = $request->pemberi;
        $laporan->penerima = $request->penerima;
        $laporan->nominal = $request->nominal;
        $laporan->alamat_kejadian = $request->alamat_kejadian;
        $laporan->kronologi_kejadian = $request->kronologi_kejadian;
        $laporan->bukti = $request->bukti;
        $laporan->save();

        if ($laporan->save()) {
            ProgressLaporan::create([
                'nomor_laporan' => $laporan->nomor_laporan,
                'nik' => Auth::user()->nik,
                'status' => 'Diubah',
                'keterangan' => 'Laporan telah diperbaharui oleh ' . Auth::user()->nama
            ], 200);
        }

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Laporan ' . $laporan->nama . 'Telah berhasil diperbaharui',
            'data' => $laporan
        ], 200);
    }

    // getAll Laporan
    public function getAll()
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya Petugas Yang dapat Mengakses Fitur Ini',
            ], 403);
        }

        // cara ngakses statusnya gimana??!!
        // $laporan = Laporan::with(['ProgressLaporans', 'user'])->where('judul', 'LIKE', '%' . $judul . '%')->get();
        $laporan = Laporan::query()->filter(request(['cari']))->get();
        if (count($laporan) < 1) {
            return response()->json([
                'message' => 'Data Laporan Tidak ditemukan',
                'data' => null,
            ], 404);
        }


        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Data Laporan Berhasil ditemukan',
            'data' => $laporan,
        ], 200);
    }

    // get User Owned Laporan
    public function getUserLaporan()
    {
        $userNIK = Auth::user()->nik;
        $laporan = Laporan::where('pelapor', $userNIK)->get();

        if ($laporan->count() < 1) {
            return response()->json([
                'kode' => 404,
                'status' => 'Not Found',
                'message' => 'Anda Belum Membuat Laporan',
            ], 404);
        }

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Daftar Laporan Berhasil diambil',
            'data' => $laporan
        ]);
    }

    // Only petugas and owner dapat melihat detail laporan
    public function details($nomor_laporan)
    {
        $laporan = Laporan::find($nomor_laporan);
        if (!Gate::allows('owner-and-petugas-can-open', $laporan)) {
            return response()->json([
                'kode' => 403,
                'status' => 'Forbidden',
                'message' => 'Anda Tidak Memiliki Akses Untuk Melihat Laporan Ini'
            ], 403);
        }

        $result = Laporan::with('user', 'pemilu', 'progressLaporans')->find($nomor_laporan);
        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Detail Laporan berhasil diambil',
            'data' => $result
        ]);
    }

    // Delete hanya bisa dilakukan oleh pemilik laporan
    public function delete($nomor_laporan)
    {
        $laporan = Laporan::find($nomor_laporan);
        if (!Gate::allows('isOwner', $laporan)) {
            return response()->json([
                'message' => 'Data Laporan Hanya dapat dihapus oleh Pemilik Laporan'
            ]);
        }
        $laporan->delete();

        return response()->json([
            'kode' => 200,
            'status' => 'OK',
            'message' => 'Data Laporan ' . $laporan->judul . ' berhasil dihapus'
        ]);
    }

    //Generate a nomor laporan
    private function generateNomorLaporan($idPemilu)
    {
        // mendapatkan data pemilu (untuk mencari tanggal)
        $pemilu = Pemilu::find($idPemilu);

        // mendapatkan total laporan
        $totalLaporan = Laporan::where('pemilu_id', $idPemilu)->count();

        // pendeklarasian tiap variabel
        $tahunPemilu = date('Y', strtotime($pemilu->tanggal_pelaksanaan));
        $jenisPemiluID = '0' . $pemilu->jenis_id;
        $pemiluID = '0' . $idPemilu;
        $totalLaporan = '0' . $totalLaporan + 1;

        // generate nomor laporan dengan format: Tahun Pemilu-JenisPemiluID-PemiluID-jumlahLaporanPadaPemiluTersebut
        $nomor = "$tahunPemilu-$jenisPemiluID-$pemiluID-$totalLaporan";

        return $nomor;
    }
}
