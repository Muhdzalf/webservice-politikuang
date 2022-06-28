<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $filleable = [
        'nomor_laporan',
        'judul',
        'waktu_kejadian',
        'tanggal_kejadian',
        'pemberi',
        'penerima',
        'nominal',
        'lokasi_kejadian',
        'kronologi_kejadian',
        'pengirim_laporan',
        'pemilu_id',
    ];
}
