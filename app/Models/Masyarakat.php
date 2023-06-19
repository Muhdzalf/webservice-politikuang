<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Masyarakat extends Model
{
    use HasFactory;

    protected $table = 'masyarakat';
    public $incrementing = false;
    protected $primaryKey = 'nik';

    protected $fillable = [
        'nik',
        'tanggal_lahir',
        'jenis_kelamin',
        'pekerjaan',
        'kewarganegaraan',
        'alamat_id',
        'user_id'
    ];

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'nik', 'nik');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function alamat()
    {
        return $this->hasMany(Alamat::class, 'alamat_id', 'id_alamat');
    }
}
