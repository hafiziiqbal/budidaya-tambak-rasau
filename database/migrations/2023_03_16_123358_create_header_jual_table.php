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
        Schema::create('header_jual', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('id_customer')->constrained('master_customer');
            $table->bigInteger('total_bruto');
            $table->bigInteger('potongan_harga');
            $table->bigInteger('total_netto');
            $table->bigInteger('pay');
            $table->bigInteger('change');
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
        Schema::dropIfExists('header_jual');
    }
};
