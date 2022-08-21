<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

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
        'bukti',
        'pengirim_laporan',
        'pemilu_id',
    ];

    //Relasi

    public function user()
    {
        return $this->belongsTo(User::class, 'pengirim_laporan', 'id');
    }
    public function pemilu()
    {
        return $this->belongsTo(Pemilu::class, 'pemilu_id', 'id');
    }

    public function progressLaporan()
    {
        return $this->hasMany(ProgressLaporan::class, 'laporan_id', 'id');
    }
}
