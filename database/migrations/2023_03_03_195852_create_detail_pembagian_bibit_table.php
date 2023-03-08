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
        Schema::create('detail_pembagian_bibit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_header_pembagian_bibit')->constrained('header_pembagian_bibit');
            $table->decimal('quantity');
            $table->bigInteger('id_jaring')->nullable();
            $table->foreignId('id_master_kolam')->constrained('master_kolam');
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
        Schema::dropIfExists('detail_pembagian_bibit');
    }
};
