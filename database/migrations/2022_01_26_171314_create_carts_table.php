<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
			$table->integer('customer_id')->nullable();
			$table->string('device_id')->nullable();
			$table->integer('product_qnty')->nullable();
			$table->integer('total_purchase_price')->nullable();
			$table->integer('total_sale_price')->nullable();
			$table->integer('total_discount')->nullable();
			$table->integer('total_final_price')->nullable();
			$table->integer('total_profit')->nullable();
            $table->string('cart_status')->nullable();
			$table->string('cart_by')->nullable();
	        $table->string('notes')->nullable();
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
        Schema::dropIfExists('carts');
    }
}
