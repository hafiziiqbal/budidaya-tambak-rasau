<?php

namespace Database\Seeders;

use App\Models\DetailBeli;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailBeliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DetailBeli::updateOrCreate(
            [
                'id_header_beli' => 1,
                'id_produk' => 1,
                'harga_satuan' => 1000,
                'quantity' => 10,
                'quantity_stok' => 10,
                'diskon_persen' => 10,
                'diskon_rupiah' => 0,
                'subtotal' => 9000,
            ],
        );

        DetailBeli::updateOrCreate(
            [
                'id_header_beli' => 1,
                'id_produk' => 2,
                'harga_satuan' => 2000,
                'quantity' => 10,
                'quantity_stok' => 10,
                'diskon_persen' => 0,
                'diskon_rupiah' => 1000,
                'subtotal' => 19000,
            ],
        );
    }
}
