<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fqa extends Model
{
    use HasFactory;

    protected $table = 'fqa';

    protected $fillable = [
        'pertanyaan',
        'jawaban',
    ];
}
