<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressLaporan extends Model
{
    use HasFactory;

    protected $table = 'progress_laporan';


    protected $fillable = [
        'nomor_laporan',
        'nik',
        'status',
        'keterangan'
    ];

    protected $primaryKey = 'id_progress';

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'nomor_laporan', 'nomor_laporan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }
}
