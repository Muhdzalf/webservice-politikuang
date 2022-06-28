<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edukasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'penulis',
        'published',
        'isi',
        'user_id',
    ];
}
