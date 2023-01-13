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

    protected $primaryKey = 'id_pemilu';


    public function scopeSearch($query, array $filters)
    {
        //Pencarian berdasarkan nama
        $query->when($filters['nama'] ?? false, function ($query, $nama) {
            return $query->where('nama', 'like', '%' . $nama . '%');
        });

        //Pencarian berdasarkan id
        $query->when($filters['id'] ?? false, function ($query, $id) {
            return $query->where('id', 'like', '%' . $id . '%');
        });
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'alamat_id', 'id_alamat');
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'pemilu_id', 'id_pemilu');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisPemilu::class, 'jenis_id', 'id_jenis');
    }
}
