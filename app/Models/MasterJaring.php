<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJaring extends Model
{
    use HasFactory;

    protected $table = 'master_jaring';

    protected $fillable = [
        'id_kolam',
        'nama',
        'posisi',
    ];

    protected $hidden = [
        'created_at'
    ];

    public function kolam()
    {
        return $this->belongsTo(MasterKolam::class, 'id_kolam');
    }
}
