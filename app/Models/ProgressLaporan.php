<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressLaporan extends Model
{
    use HasFactory;

    protected $table = 'progress_laporan';
    protected $primaryKey = 'id_progress';


    protected $fillable = [
        'nomor_laporan',
        'pengawas_id',
        'status',
        'keterangan'
    ];


    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'nomor_laporan', 'nomor_laporan');
    }

    public function pengawas()
    {
        return $this->belongsTo(Pengawas::class, 'pengawas_id', 'id_pengawas');
    }
}
