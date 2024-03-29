<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nik',
        'nama',
        'email',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_hp',
        'alamat',
        'pekerjaan',
        'kewarganegaraan',
        'role',
        'password',
    ];

    public $incrementing = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $primaryKey = 'nik';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function progressLaporan()
    {
        return $this->hasMany(ProgressLaporan::class, 'nik', 'nik');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'pelapor', 'nik');
    }
}
