<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPemilu extends Model
{
    use HasFactory;

    protected $table = 'jenis_pemilu';
    protected $primaryKey = 'id_jenis';

    protected $fillable = [
        'nama',
    ];

    public function pemilu()
    {
        return $this->hasMany(Pemilu::class, 'jenis_id', 'id_jenis');
    }
}
