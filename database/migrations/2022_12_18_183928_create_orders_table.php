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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->double('total')->nullable();


            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->string('city');
            $table->string('zip');
            $table->string('country');
            $table->string('name');
            $table->enum('payment_method' , ['CASH' , 'PAYPAL']);
            $table->enum('status' , ['SUCCESS' , 'CANCELED' , 'WAITING' , 'ERROR' , 'PENDING']);

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('orders');
    }
};
