<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alamat extends Model
{
    use HasFactory;

    protected $fillable = [
        'kecamatan_id',
        'kabupaten_id',
        'provinsi_id',
        'keterangan'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'alamat_id', 'id');
    }

    public function pemilu()
    {
        return $this->hasOne(User::class, 'alamat_id', 'id');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id', 'id');
    }
    public function kecamatan()
    {
        return $this->belongsTo(kecamatan::class, 'kecamatan_id', 'id');
    }
}
