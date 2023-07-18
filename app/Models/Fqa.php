<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fqa extends Model
{
    use HasFactory;

    protected $table = 'fqa';
    protected $primaryKey = 'id_fqa';

    protected $fillable = [
        'pertanyaan',
        'jawaban',
        'admin_id',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['cari'] ?? false, function ($query, $cari) {
            $query->where('pertanyaan', 'like', '%' . $cari . '%')->orWhere('jawaban', 'like', '%' . $cari . '%');
        });
    }

    public function admin()
    {
        return $this->belongsTo(Administrator::class, 'admin_id', 'id_admin');
    }
}
