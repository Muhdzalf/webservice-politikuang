<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengawas extends Model
{
    use HasFactory;

    protected $table = 'pengawas';


    protected $fillable = [
        'no_spt',
        'jabatan',
        'mulai_tugas',
        'selesai_tugas',
        'user_id'
    ];

    public function Progresslaporan()
    {
        return $this->hasMany(ProgressLaporan::class, 'pengawas_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
