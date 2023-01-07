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
        'nominal',
        'pemberi',
        'penerima',
        'tanggal_kejadian',
        'alamat_kejadian',
        'kronologi_kejadian',
        'bukti',
        'pemilu_id',
        'pelapor',
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
        return $this->belongsTo(User::class, 'pelapor', 'nik');
    }
    public function pemilu()
    {
        return $this->belongsTo(Pemilu::class, 'pemilu_id', 'id_pemilu');
    }

    public function progressLaporans()
    {
        return $this->hasMany(ProgressLaporan::class, 'nomor_laporan', 'nomor_laporan');
    }
}
