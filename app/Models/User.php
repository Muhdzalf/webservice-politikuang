<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'nama',
        'email',
        'password',
        'tanggal_lahir',
        'jenis_kelamin',
        'nomor_hp',
        'alamat_id',
        'pekerjaan',
        'kewarganegaraan',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'alamat_id', 'id');
    }

    public function progressLaporan()
    {
        return $this->hasMany(ProgressLaporan::class, 'user_id', 'id');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'user_id', 'id');
    }

    public function edukasi()
    {
        return $this->hasMany(Edukasi::class, 'user_id', 'id');
    }
}
