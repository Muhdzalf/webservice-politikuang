<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kabupaten_id'
    ];

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }

    public function alamat()
    {
        return $this->hasMany(Alamat::class, 'kecamatan_id', 'id');
    }
}
