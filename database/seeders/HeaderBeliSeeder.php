<?php

namespace Database\Seeders;

use App\Models\HeaderBeli;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeaderBeliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HeaderBeli::updateOrCreate(
            [
                'tgl_beli' => date('Y-m-d', strtotime('2023-03-07')),
                'id_supplier' => 1,
                'total_bruto' => 28000,
                'potongan_harga' => 3000,
                'total_netto' => 25000,
            ],
        );
    }
}
