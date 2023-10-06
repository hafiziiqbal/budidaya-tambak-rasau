<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_header_panen')->constrained('header_panen');
            $table->tinyInteger('status');
            $table->foreignId('id_detail_pembagian_bibit')->constrained('detail_pembagian_bibit');
            $table->string('nama_kolam');
            $table->string('posisi_kolam');
            $table->string('nama_jaring')->nullable();
            $table->string('posisi_jaring')->nullable();
            $table->decimal('quantity')->default(0);
            $table->decimal('quantity_berat')->default(0);
            $table->bigInteger('id_produk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_panen');
    }
};
