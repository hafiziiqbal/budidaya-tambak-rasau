<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCustomer extends Model
{
    use HasFactory;
    protected $table = 'master_customer';

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
    ];

    protected $hidden = [
        'created_at'
    ];
}
