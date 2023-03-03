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
        Schema::create('master_tong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kolam')->constrained('master_kolam');
            $table->bigInteger('id_jaring')->nullable();
            $table->string('nama');
            $table->timestamps();

            $table->index(['id_jaring']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_tong');
    }
};
