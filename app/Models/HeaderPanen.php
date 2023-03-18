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

    public function detail_panen()
    {
        return $this->hasMany(DetailPanen::class, 'id_header_panen', 'id');
    }
}
