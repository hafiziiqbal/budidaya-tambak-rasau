<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderJual extends Model
{
    use HasFactory;

    protected $table = 'header_jual';

    protected $fillable = [
        'invoice',
        'user_id',
        'id_customer',
        'total_bruto',
        'potongan_harga',
        'total_netto',
        'pay',
        'change'
    ];

    protected $hidden = [
        'created_at'
    ];
}
