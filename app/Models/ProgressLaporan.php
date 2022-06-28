<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_id',
        'user_id',
        'status',
        'keterangan'
    ];
}
