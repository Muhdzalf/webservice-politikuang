<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressLaporan extends Model
{
    use HasFactory;

    protected $table = 'progress_laporan';


    protected $fillable = [
        'laporan_id',
        'user_id',
        'status',
        'keterangan'
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
