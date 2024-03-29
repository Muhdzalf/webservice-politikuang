<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alamat extends Model
{
    use HasFactory;

    protected $table = 'alamat_pemilu';

    protected $fillable = [
        'kecamatan_id',
        'kabupaten_kota_id',
        'provinsi_id',
        'desa'
    ];

    protected $primaryKey = 'id_alamat';

    public function pemilu()
    {
        return $this->hasOne(User::class, 'alamat_id', 'id_alamat');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id_provinsi');
    }
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_kota_id', 'id_kabupaten)kota');
    }
    public function kecamatan()
    {
        return $this->belongsTo(kecamatan::class, 'kecamatan_id', 'id_kecamatan');
    }
}
