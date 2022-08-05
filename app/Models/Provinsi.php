<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi'
    ];

    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::class, 'provinsi_id', 'id');
    }

    public function alamat()
    {
        return $this->hasMany(Alamat::class, 'provinsi_id', 'id');
    }
}
