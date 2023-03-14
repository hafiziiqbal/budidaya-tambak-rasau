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
        Schema::create('detail_pemberian_pakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_detail_pembagian_pakan')->constrained('detail_pembagian_pakan');
            $table->foreignId('id_detail_pembagian_bibit')->constrained('detail_pembagian_bibit');
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
        Schema::dropIfExists('detail_pemberian_pakan');
    }
};
