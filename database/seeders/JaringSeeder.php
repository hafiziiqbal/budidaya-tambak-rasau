<?php

namespace Database\Seeders;

use App\Models\MasterJaring;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JaringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MasterJaring::updateOrCreate(
            [
                'id_kolam' => 1,
                'nama' => 'jaring 1',
                'quantity' => 2,
            ],
        );

        MasterJaring::updateOrCreate(
            [
                'id_kolam' => 2,
                'nama' => 'jaring 2',
                'quantity' => 2,
            ],
        );
    }
}
