<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Kategori::updateOrCreate(
            [
                'id' => 5,
                'nama' => 'Pakan',
                'deskripsi' => '',
            ],
        );
        Kategori::updateOrCreate(
            [
                'id' => 7,
                'nama' => 'Bibit',
                'deskripsi' => '',
            ],
        );
        Kategori::updateOrCreate(
            [
                'id' => 6,
                'nama' => 'Ikan',
                'deskripsi' => '',
            ],
        );
    }
}
