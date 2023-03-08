<?php

namespace Database\Seeders;

use App\Models\MasterKolam;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KolamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MasterKolam::updateOrCreate(
            [
                'nama' => 'Kolam 1',
                'posisi' => '1234',
            ],
        );
        MasterKolam::updateOrCreate(
            [
                'nama' => 'Kolam 2',
                'posisi' => '1234',
            ],
        );
        MasterKolam::updateOrCreate(
            [
                'nama' => 'Kolam 3',
                'posisi' => '1234',
            ],
        );
    }
}
