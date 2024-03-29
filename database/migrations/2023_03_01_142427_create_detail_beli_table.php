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
            $table->foreignId('id_header_beli')->constrained('header_beli')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('produk');
            $table->bigInteger('harga_satuan');
            $table->decimal('quantity');
            $table->decimal('quantity_stok');
            $table->decimal('diskon_persen')->nullable();
            $table->bigInteger('diskon_rupiah')->nullable();
            $table->bigInteger('subtotal');
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
