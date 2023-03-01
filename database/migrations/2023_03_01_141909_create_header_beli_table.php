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
        Schema::create('header_beli', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_beli');
            $table->foreignId('id_supplier')->constrained('supplier');
            $table->decimal('total_bruto');
            $table->decimal('potongan_harga');
            $table->decimal('total_netto');
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
        Schema::dropIfExists('header_beli');
    }
};
