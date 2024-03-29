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
        Schema::create('hpp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_detail_panen')->constrained('detail_panen')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('id_detail_pembagian_bibit')->constrained('detail_pembagian_bibit');
            $table->integer('jumlah_ikan_panen');
            $table->integer('total_biaya_pakan');
            $table->integer('hpp');
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
        Schema::dropIfExists('hpp');
    }
};
