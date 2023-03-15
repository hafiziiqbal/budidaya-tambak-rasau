<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPanen extends Model
{
    use HasFactory;

    protected $table = 'detail_panen';

    protected $fillable = [
        'id_header_panen',
        'id_detail_pembagian_bibit',
        'id_produk',
        'nama_kolam',
        'posisi_kolam',
        'nama_jaring',
        'posisi_jaring',
        'quantity'
    ];

    protected $hidden = [
        'created_at'
    ];
}
