<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Edukasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EdukasiController extends Controller
{
    public function getAll()
    {
        $edukasi = Edukasi::all();
        return response()->json([
            'message' => 'Data Edukasi Berhasil diambil',
            'data' => $edukasi
        ]);
    }

    public function create(Request $request)
    {
        if (!Gate::allows('only-petugas')) {
            return response()->json([
                'message' => 'Hanya petugas yang memiliki akses untuk fitur ini'
            ], 403);
        }
        $request->validate([
            'judul' => 'required|string',
            'penulis' => 'required|string',
            'published' => 'required|numeric|in:0,1',
            'isi' => 'required|string',
            'user_id' => 'required|numeric',
        ]);

        $edukasi = Edukasi::create([
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'published' => $request->published,
            'isi' => $request->isi,
            'user_id' => Auth::user()->id,
        ]);

        return response()->json([
            'message' => 'Konten Edukasi berhasil dibuat',
            'data' => $edukasi,
        ]);
    }

    public function update(Request $request, $id)
    {
        $edukasi = Edukasi::find($id);
        $edukasi->judul = $request->judul;
        $edukasi->isi = $request->isi;
        $edukasi->save();

        return response()->json([
            'message' => 'konten edukasi berhasil diperbaharui',
            'data' => $edukasi
        ]);
    }

    public function publish($id)
    {
        $edukasi = Edukasi::find($id);
        $edukasi->published = 2;
        return response()->json([
            'message' => 'Konten Edukasi Sudah dipublish'
        ]);
    }


    public function delete($id)
    {
        $edukasi = Edukasi::find($id);
        $edukasi->delete();

        return response()->json([
            'message' => 'Konten Edukasi Berhasil Dihapus'
        ]);
    }
}
