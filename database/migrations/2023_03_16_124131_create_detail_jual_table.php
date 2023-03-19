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
        Schema::create('detail_jual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_header_jual')->constrained('header_jual');
            $table->foreignId('id_produk')->constrained('produk');
            $table->bigInteger('harga_satuan');
            $table->decimal('diskon');
            $table->decimal('quantity');
            $table->bigInteger('sub_total');
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
        Schema::dropIfExists('detail_jual');
    }
};