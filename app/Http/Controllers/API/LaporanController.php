<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Masyarakat;
use App\Models\Pemilu;
use App\Models\ProgressLaporan;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Throwable;

use function PHPSTORM_META\map;

class LaporanController extends Controller
{
    //
    public function create(Request $request)
    {
        $kode = 200; // default
        $rules = [
                'nomor_laporan' => 'unique:laporan',
                'judul' => 'required|string',
                'tanggal_kejadian' => 'required|date_format:Y-m-d',
                'pemberi' => 'required|string',
                'penerima' => 'required|string',
                'nominal' => 'required|numeric',
                'tempat_kejadian' => 'required|string',
                'kronologi_kejadian' => 'required|string',
                'bukti' => 'required|url',
                'pemilu_id' => 'required|numeric'
        ];

        try {
            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                $kode = 400;
                throw new Exception($validator->messages()->first());
            }

            // mendapatkan nik user yang sedang login
            $userId = Auth::user()->id_user;
            $pengirimId = Masyarakat::where('user_id', $userId)->first();


            // membuat nomor laporan
            $nomorLaporan = $this->generateNomorLaporan($request->pemilu_id);

            if (!$userId || !$pengirimId || !$nomorLaporan) {
                $kode = 500;
                throw new Exception('Tidak dapat membuat Laporan. Terdapat Kesalahan pada sistem');
            }

            $laporan = Laporan::create([
                'nomor_laporan' => $nomorLaporan,
                'judul' => $request->judul,
                'tanggal_kejadian' => $request->tanggal_kejadian,
                'pemberi' => $request->pemberi,
                'penerima' => $request->penerima,
                'nominal' => $request->nominal,
                'tempat_kejadian' => $request->tempat_kejadian,
                'kronologi_kejadian' => $request->kronologi_kejadian,
                'bukti' => $request->bukti,
                'nik' => $pengirimId->nik,
                'pemilu_id' => $request->pemilu_id
            ]);

            // pada saat pembuatan laporan maka otomatis akan langsung tercatat pada progress laporan
            if ($laporan) {
                ProgressLaporan::create([
                    'nomor_laporan' => $nomorLaporan,
                    'status' => 'menunggu',
                    'keterangan' => 'Laporan telah dibuat oleh ' . Auth::user()->nama . ' menunggu untuk diproses oleh pengawas.'
                ]);
            }

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Laporan Berhasil dibuat dengan nomor ' . $laporan->nomor_laporan,
                'data' => $laporan,
            ]);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => false,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }

    // update isinya hanya bisa oleh user pemilik laporan
    public function update(Request $request, $nomor_laporan)
    {
        try {
            $kode = 200;
            $status = true;

            $laporan = Laporan::find($nomor_laporan);

            if (!$laporan) {
                $kode = 404;
                $status = false;
                throw new Exception('Nomor Laporan Tidak ditemukan');
            }

            if (!Gate::allows('isOwner', $laporan)) {
                $kode = 403;
                $status = false;
                throw new Exception('Akses Ditolak!. Hanya pemilik Laporan yang dapat menggunakan fitur ini');
            }
            //mendapatkan proses laporan terakhir untuk melihat status terakhir dari progress laporan
            $ProgressLaporan = ProgressLaporan::where('nomor_laporan', $nomor_laporan)->latest()->first();

            if ($ProgressLaporan->status === 'diproses' || $ProgressLaporan->status === 'ditolak' || $ProgressLaporan->status === 'selesai') {
                $kode = 403;
                $status = false;
                throw new Exception('Akses Ditolak!. Laporan sedang diproses, tidak dapat diubah');
            }

            $laporan->judul = $request->judul;
            $laporan->tanggal_kejadian = $request->tanggal_kejadian;
            $laporan->pemberi = $request->pemberi;
            $laporan->penerima = $request->penerima;
            $laporan->nominal = $request->nominal;
            $laporan->tempat_kejadian = $request->tempat_kejadian;
            $laporan->kronologi_kejadian = $request->kronologi_kejadian;
            $laporan->bukti = $request->bukti;
            $laporan->save();

            // memperbaharui status progress laporan
            if ($laporan->save()) {
                ProgressLaporan::create([
                    'nomor_laporan' => $laporan->nomor_laporan,
                    'status' => 'menunggu',
                    'keterangan' => 'Laporan telah diperbaharui oleh ' . Auth::user()->nama . ' menunggu untuk diproses oleh pengawas'
                ], 200);
            }

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Laporan ' . $laporan->nama . 'Telah berhasil diperbaharui',
                'data' => $laporan
            ], 200);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => $status,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }

    // getAll Laporan (Pengawas dan Admin)
    public function getAll()
    {
        try {
            $kode = 200;
            $status = true;

            if (!Gate::allows('only-petugas')) {
                $kode = 403;
                $status = false;
                throw new Exception('Hanya Petugas Yang dapat Mengakses Fitur Ini');
            }

            $laporan = Laporan::query()->filter(request(['cari']))->get(['nomor_laporan', 'judul', 'nominal', 'created_at']); //hanya menampilkan nomor dan judul

            if (is_null($laporan)) {
                $kode = 404;
                $status = false;
                throw new Exception('Laporan Tidak Ditemukan');
            }

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Data Laporan Berhasil ditemukan',
                'data' => $laporan,
            ], 200);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => $status,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }

    // get User Owned Laporan
    public function getUserLaporan()
    {
        $kode = 200;
        $status = true;
        try {
            $user = Auth::user();
            $masy = Masyarakat::where('user_id', $user->id_user)->first();
            $laporan = Laporan::where('nik', $masy->nik)->filter(request(['cari']))->get(['nomor_laporan', 'judul', 'nominal', 'created_at']);

            if ($laporan->count() < 1) {
                $kode = 404;
                $status = false;
                throw new Exception('Laporan Tidak Ditemukan.');
            }

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Daftar Laporan Berhasil diambil',
                'data' => $laporan
            ]);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => $status,
                'message' => 'Gagal' . $err->getMessage(),
            ], $kode);
        }
    }

    // Only petugas and owner dapat melihat detail laporan
    public function details($nomor_laporan)
    {
        try {
            $kode = 200;
            $status = false;

            $laporan = Laporan::find($nomor_laporan);
            if (!$laporan) {
                $kode = 404;
                $status = false;
                throw new Exception('Laporan tidak ditemukan');
            }

            if (!Gate::allows('owner-and-petugas-can-open', $laporan)) {
                $kode = 403;
                $status = false;
                throw new Exception('Anda Tidak Memiliki Akses Untuk Melihat Laporan Ini');
            }

            $result = Laporan::where('nomor_laporan', $nomor_laporan)->first();
            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Detail Laporan berhasil diambil',
                'data' => $result
            ]);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => $status,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }

    // Delete hanya bisa dilakukan oleh pemilik laporan
    public function delete($nomor_laporan)
    {
        try {
            $kode = 200;
            $status = false;

            $laporan = Laporan::find($nomor_laporan);
            if (!$laporan) {
                $kode = 404;
                $status = false;
                throw new Exception('Laporan tidak ditemukan');
            }

            if (!Gate::allows('isOwner', $laporan)) {
                $kode = 403;
                $status = false;
                throw new Exception('Laporan Hanya dapat dihapus oleh Pemilik Laporan');
            }

            $ProgressLaporan = ProgressLaporan::where('nomor_laporan', $nomor_laporan)->latest()->first();

            if ($ProgressLaporan->status !== 'menunggu') {
                $kode = 403;
                $status = false;
                throw new Exception('Permintaan Ditolak. Laporan sedang diproses, tidak dapat dihapus');
            }

            $laporan->delete();

            return response()->json([
                'kode' => 200,
                'status' => true,
                'message' => 'Laporan dengan judul '.'"'. $laporan->judul .'"'. ' berhasil dihapus'
            ]);
        } catch (Throwable $err) {
            return response()->json([
                'kode' => $kode,
                'status' => $status,
                'message' => 'Gagal: ' . $err->getMessage(),
            ], $kode);
        }
    }

    //Generate a nomor laporan
    private function generateNomorLaporan($idPemilu)
    {
        try {
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
        } catch (Throwable $err) {
            return null;
        }
    }
}
