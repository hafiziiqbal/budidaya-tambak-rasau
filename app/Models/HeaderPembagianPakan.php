<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderPembagianPakan extends Model
{
    use HasFactory;
    protected $table = 'header_pembagian_pakan';

    protected $fillable = [
        'tgl_pembagian_pakan',
    ];

    protected $hidden = [
        'created_at'
    ];

    public function detail_pembagian_pakan()
    {
        return $this->hasMany(DetailPembagianPakan::class, 'id_header_pembagian_pakan', 'id');
    }
}
