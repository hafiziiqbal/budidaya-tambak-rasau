<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderJual extends Model
{
    use HasFactory;

    protected $table = 'header_jual';

    protected $fillable = [
        'invoice',
        'user_id',
        'id_customer',
        'total_bruto',
        'potongan_harga',
        'total_netto',
        'pay',
        'change'
    ];



    public function detail_jual()
    {
        return $this->hasMany(DetailJual::class, 'id_header_jual');
    }

    public function customer()
    {
        return $this->belongsTo(MasterCustomer::class, 'id_customer');
    }
}
