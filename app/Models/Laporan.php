<?php

namespace App\Models;

use GuzzleHttp\Psr7\Query;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    protected $primaryKey = 'nomor_laporan';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nomor_laporan',
        'judul',
        'nominal',
        'pemberi',
        'penerima',
        'tanggal_kejadian',
        'tempat_kejadian',
        'kronologi_kejadian',
        'bukti',
        'pemilu_id',
        'nik',
    ];



    public function scopeFilter($query, array $filters)
    {

        $query->when($filters['cari'] ?? false, function ($query, $cari) {
            $query->where('nomor_laporan', 'like', '%' . $cari . '%')->orWhere('judul', 'like', '%' . $cari . '%');
        });
    }

    //Relasi

    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class, 'nik', 'nik');
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
