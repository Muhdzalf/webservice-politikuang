<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    use HasFactory;
    protected $table = 'kecamatan';


    protected $fillable = [
        'nama',
        'kabupaten_kota_id'
    ];

    protected $primaryKey = 'id_kecamatan';


    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id', 'id_kabupaten_kota');
    }

    public function alamat()
    {
        return $this->hasMany(Alamat::class, 'kecamatan_id', 'id_kecamatan');
    }
}
