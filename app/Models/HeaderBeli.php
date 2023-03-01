<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderBeli extends Model
{
    use HasFactory;

    protected $table = 'header_beli';

    protected $fillable = [
        'tgl_beli',
        'id_supplier',
        'total_bruto',
        'potongan_harga',
        'total_netto',
    ];

    protected $hidden = [
        'created_at'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }
}
