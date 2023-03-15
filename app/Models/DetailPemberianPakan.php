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

    public function detail_beli()
    {
        return $this->belongsTo(DetailBeli::class, 'id_detail_beli');
    }
}
