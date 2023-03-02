<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // public function fqa()
    // {
    //     return $this->hasMany(Fqa::class, 'admin_id', 'id');
    // }

    // public function pemilu()
    // {
    //     return $this->hasMany(pemilu::class, 'admin_id', 'id');
    // }
}
