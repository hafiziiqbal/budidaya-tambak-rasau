<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hpp extends Model
{
    use HasFactory;

    protected $table = 'hpp';

    protected $fillable = [
        'id_detail_panen',
        'id_detail_pembagian_bibit',
        'id_detail_pemberian_pakan',
        'status',
        'total_biaya_bibit',
        'total_biaya_pakan',
    ];

    protected $hidden = [
        'created_at'
    ];
}
