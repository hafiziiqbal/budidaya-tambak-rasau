<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailJual extends Model
{
    use HasFactory;

    protected $table = 'detail_jual';

    protected $fillable = [
        'id_header_jual',
        'id_produk',
        'harga_satuan',
        'diskon',
        'quantity',
        'sub_total'
    ];

    protected $hidden = [
        'created_at'
    ];
}
