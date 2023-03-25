<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alamat extends Model
{
    use HasFactory;

    protected $table = 'alamat';
    protected $primaryKey = 'id_alamat';

    protected $fillable = [
        'kecamatan_id',
        'kabupaten_kota_id',
        'provinsi_id',
        'desa',
        'detail_alamat'
    ];


    public function pemilu()
    {
        return $this->hasOne(Pemilu::class, 'alamat_id', 'id_alamat');
    }

    public function masyarakat()
    {
        return $this->hasOne(Masyarakat::class, 'alamat_id', 'id_alamat');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id_provinsi');
    }
    public function kabupaten()
    {
        return $this->belongsTo(KabupatenKota::class, 'kabupaten_kota_id', 'id_kabupaten_kota');
    }
    public function kecamatan()
    {
        return $this->belongsTo(kecamatan::class, 'kecamatan_id', 'id_kecamatan');
    }
}
