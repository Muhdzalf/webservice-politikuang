<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemilu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tanggal',
        'waktu',
        'alamat_id',
        'jenis',
    ];

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'alamat_id', 'id');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'pemilu_id', 'id');
    }
}
