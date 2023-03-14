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
        Schema::create('detail_pembagian_pakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_header_pembagian_pakan')->constrained('header_pembagian_pakan');
            $table->foreignId('id_detail_beli')->constrained('detail_beli');
            $table->foreignId('id_tong')->constrained('master_tong');
            $table->decimal('quantity');
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
        Schema::dropIfExists('detail_pembagian_pakan');
    }
};
