<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('idorders');
            $table->integer('idusers');
            $table->string('total')->nullable();
            // $table->string('quantity')->nullable();
            $table->string('codeorder');
            $table->string('snap_token')->nullable();
            $table->enum('status_order', array('pending', 'success', 'failed', 'expired'));
            $table->enum('status_payments', array('paid','unpaid'));
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
}
