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
                'id_kolam' => null,
                'nama' => 'jaring 1',
                'posisi' => '111',
            ],
        );

        MasterJaring::updateOrCreate(
            [
                'id_kolam' => null,
                'nama' => 'jaring 2',
                'posisi' => '111',
            ],
        );
    }
}
