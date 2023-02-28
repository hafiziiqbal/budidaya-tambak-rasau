<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::updateOrCreate(
            [
                'nama' => 'Perusahaan Luar',
                'alamat' => 'Jl.Contoh Kota Apa Saja',
                'telepon' => '089513591812',
            ],
        );

        Supplier::updateOrCreate(
            [
                'nama' => 'Perusahaan Cabang',
                'alamat' => 'Jl.Contoh Kota Dekat',
                'telepon' => '089513591813',
            ],
        );
    }
}
