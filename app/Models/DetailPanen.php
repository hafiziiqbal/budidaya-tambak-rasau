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
        'status',
        'id_detail_pembagian_bibit',
        'nama_kolam',
        'posisi_kolam',
        'nama_jaring',
        'posisi_jaring',
        'quantity',
        'quantity_awal'
    ];

    protected $hidden = [
        'created_at'
    ];

    public function detail_pembagian_bibit()
    {
        return $this->belongsTo(DetailPembagianBibit::class, 'id_detail_pembagian_bibit');
    }

    public function header_panen()
    {
        return $this->belongsTo(HeaderPanen::class, 'id_header_panen');
    }

    public function detail_jual()
    {
        return $this->hasMany(DetailJual::class, 'id_detail_panen', 'id');
    }

    public function header_pembagian_bibit()
    {
        return $this->hasMany(HeaderPembagianBibit::class, 'id_detail_panen', 'id');
    }

    public function getQuantityAwalPanenAttribute()
    {
        $totalQuantity = $this->detail_jual->sum('quantity');
        return $this->quantity + $totalQuantity;
    }

    public function hpp()
    {
        return $this->hasOne(Hpp::class, 'id_detail_panen', 'id');
    }
}
