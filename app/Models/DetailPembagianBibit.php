<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembagianBibit extends Model
{
    use HasFactory;

    protected $table = 'detail_pembagian_bibit';

    protected $fillable = [
        'id_header_pembagian_bibit',
        'quantity',
        'id_jaring',
        'id_jaring_old',
        'id_kolam',
    ];

    protected $hidden = [
        'created_at'
    ];


    public function header_pembagian_bibit()
    {
        return $this->belongsTo(HeaderPembagianBibit::class, 'id_header_pembagian_bibit');
    }

    public function jaring()
    {
        return $this->belongsTo(MasterJaring::class, 'id_jaring');
    }

    public function kolam()
    {
        return $this->belongsTo(MasterKolam::class, 'id_kolam');
    }
}
