<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembagianPakan extends Model
{
    use HasFactory;
    protected $table = 'detail_pembagian_pakan';

    protected $fillable = [
        'id_header_pembagian_pakan',
        'id_detail_beli',
        'id_tong',
        'id_tong_old',
        'quantity',
    ];

    protected $hidden = [
        'created_at'
    ];

    public function detail_beli()
    {
        return $this->belongsTo(DetailBeli::class, 'id_detail_beli');
    }

    public function tong()
    {
        return $this->belongsTo(MasterTong::class, 'id_tong');
    }

    public function tong_old()
    {
        return $this->belongsTo(MasterTong::class, 'id_tong_old');
    }

    public function header_pembagian_pakan()
    {
        return $this->belongsTo(HeaderPembagianPakan::class, 'id_header_pembagian_pakan');
    }
}
