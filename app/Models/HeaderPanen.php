<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderPanen extends Model
{
    use HasFactory;

    protected $table = 'header_panen';

    protected $fillable = [
        'tgl_panen'
    ];

    protected $hidden = [
        'created_at'
    ];
}
