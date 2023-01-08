<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KabupatenKota extends Model
{
    use HasFactory;

    protected $table = 'kabupaten_kota';

    protected $fillable = [
        'nama',
        'provinsi_id'
    ];

    protected $primaryKey = 'id_kabupaten_kota';


    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id_provinsi');
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'kabupaten_kota_id', 'id_kabupaten_kota');
    }

    public function alamat()
    {
        return $this->hasMany(Alamat::class, 'kabupaten_kota_id', 'id_kabupaten_kota');
    }
}
