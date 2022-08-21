<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemilu extends Model
{
    use HasFactory;

    protected $table = 'pemilu';

    protected $fillable = [
        'nama',
        'tanggal_pelaksanaan',
        'waktu_pelaksanaan',
        'alamat_id',
        'jenis_id',
    ];

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'alamat_id', 'id');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'pemilu_id', 'id');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisPemilu::class, 'jenis_id', 'id');
    }
}
