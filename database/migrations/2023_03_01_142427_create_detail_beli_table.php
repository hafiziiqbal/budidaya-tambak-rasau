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
        Schema::create('detail_beli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_header_beli')->constrained('header_beli');
            $table->foreignId('id_produk')->constrained('produk');
            $table->decimal('harga_satuan');
            $table->decimal('quantity');
            $table->decimal('diskon_persen');
            $table->decimal('diskon_rupiah');
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
        Schema::dropIfExists('detail_beli');
    }
};
