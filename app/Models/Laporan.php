<?php

namespace App\Models;

use GuzzleHttp\Psr7\Query;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'nomor_laporan',
        'judul',
        'tahun_kejadian',
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

    public function scopeFilter($query, array $filters)
    {

        $query->when($filters['cari'] ?? false, function ($query, $cari) {
            $query->where('nomor_laporan', 'like', '%' . $cari . '%')->orWhere('judul', 'like', '%' . $cari . '%');
        });
    }

    //Relasi

    public function user()
    {
        return $this->belongsTo(User::class, 'pengirim_laporan', 'id');
    }
    public function pemilu()
    {
        return $this->belongsTo(Pemilu::class, 'pemilu_id', 'id');
    }

    public function progressLaporans()
    {
        return $this->hasMany(ProgressLaporan::class, 'laporan_id', 'id');
    }
}
