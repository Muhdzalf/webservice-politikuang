<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    use HasFactory;

    protected $fillable = [
        'kecamatan_id',
        'kabupaten_id',
        'provinsi_id',
        'keterangan'
    ];
}
