<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Produk::updateOrCreate(
            [
                'id_kategori' => 2,
                'nama' => 'Nila',
                'quantity' => 10,
            ],
        );

        Produk::updateOrCreate(
            [
                'id_kategori' => 2,
                'nama' => 'Lele',
                'quantity' => 10,
            ],
        );
    }
}