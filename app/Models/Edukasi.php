<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edukasi extends Model
{
    use HasFactory;

    protected $table = 'edukasi';


    protected $fillable = [
        'judul',
        'penulis',
        'published',
        'tanggal_terbit',
        'isi',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
