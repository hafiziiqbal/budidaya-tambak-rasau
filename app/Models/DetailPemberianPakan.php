<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemberianPakan extends Model
{
    use HasFactory;
    protected $table = 'detail_pemberian_pakan';

    protected $fillable = [
        'id_detail_pembagian_pakan',
        'id_detail_pembagian_bibit',
        'quantity',
    ];

    protected $hidden = [
        'created_at'
    ];

    public function detail_pembagian_pakan()
    {
        return $this->belongsTo(DetailPembagianPakan::class, 'id_detail_pembagian_pakan');
    }

    public function detail_pembagian_bibit()
    {
        return $this->belongsTo(DetailPembagianBibit::class, 'id_detail_pembagian_bibit');
    }
}
