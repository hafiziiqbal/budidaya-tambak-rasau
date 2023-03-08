<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderPembagianBibit extends Model
{
    use HasFactory;

    protected $table = 'header_pembagian_bibit';

    protected $fillable = [
        'tgl_pembagian',
        'id_detail_beli',
        'id_detail_panen',
    ];

    protected $hidden = [
        'created_at'
    ];


    public function detail_beli()
    {
        return $this->belongsTo(DetailBeli::class, 'id_detail_beli');
    }
}
