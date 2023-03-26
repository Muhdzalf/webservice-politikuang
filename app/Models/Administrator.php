<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;
    protected $table = 'administrator';
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function fqa()
    {
        return $this->hasMany(Fqa::class, 'admin_id', 'id_admin');
    }

    public function pemilu()
    {
        return $this->hasMany(pemilu::class, 'admin_id', 'id_admin');
    }
}
