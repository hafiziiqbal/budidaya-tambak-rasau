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
        Schema::create('header_pembagian_bibit', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_pembagian');
            $table->foreignId('id_detail_beli')->constrained('detail_beli');
            $table->bigInteger('id_detail_panen')->nullable();
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
        Schema::dropIfExists('header_pembagian_bibit');
    }
};
