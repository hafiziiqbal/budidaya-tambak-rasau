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
            $table->foreignId('id_supplier')->constrained('supplier')->cascadeOnDelete();
            $table->bigInteger('total_bruto')->nullable();
            $table->bigInteger('potongan_harga');
            $table->bigInteger('total_netto')->nullable();
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
