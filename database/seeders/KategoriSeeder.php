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
                'nama' => 'Pakan',
                'deskripsi' => '',
            ],
        );
        Kategori::updateOrCreate(
            [
                'nama' => 'Bibit',
                'deskripsi' => '',
            ],
        );
        Kategori::updateOrCreate(
            [
                'nama' => 'Ikan',
                'deskripsi' => '',
            ],
        );
    }
}
