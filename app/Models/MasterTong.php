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
        'id_jaring',
        'nama',
    ];

    protected $hidden = [
        'created_at'
    ];

    public function kolam()
    {
        return $this->belongsTo(MasterKolam::class, 'id_kolam');
    }

    public function jaring()
    {
        return $this->belongsTo(MasterJaring::class, 'id_jaring');
    }
}
