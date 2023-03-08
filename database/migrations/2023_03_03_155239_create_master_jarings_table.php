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
        Schema::create('master_jaring', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_kolam')->nullable();
            $table->string('nama');
            $table->string('posisi');
            $table->timestamps();

            $table->index(['id_kolam']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_jaring');
    }
};
