<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTong extends Model
{
    use HasFactory;
    protected $table = 'master_tong';

    protected $fillable = [
        'id_kolam',
        'nama',
    ];

    protected $hidden = [
        'created_at'
    ];

    protected $casts = [
        'id_kolam' => 'array',
    ];
}
