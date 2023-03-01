<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBeli extends Model
{
    use HasFactory;
    protected $table = 'detail_beli';

    protected $fillable = [
        'id_header_beli',
        'id_produk',
        'harga_satuan',
        'quantity',
        'diskon_persen',
        'diskon_rupiah',
    ];

    protected $hidden = [
        'created_at'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function header_beli()
    {
        return $this->belongsTo(HeaderBeli::class, 'id_header_beli');
    }
}
