<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masyarakat extends Model
{
    use HasFactory;

    protected $table = 'masyarakat';

    protected $fillable = [
        'nik',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'pekerjaan',
        'kewarganegaraan',
        'user_id'
    ];


    public $incrementing = false;
    protected $primaryKey = 'nik';

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'pelapor', 'nik');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
