<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKolam extends Model
{
    use HasFactory;

    protected $table = 'master_kolam';

    protected $fillable = [
        'nama',
        'posisi',
    ];

    protected $hidden = [
        'created_at'
    ];
}
