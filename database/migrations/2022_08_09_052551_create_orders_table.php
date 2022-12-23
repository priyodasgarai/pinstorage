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
            $table->id();
            $table->integer('user_id')->comment('Refer users: id')->nullable();
            $table->string('size_id',255)->nullable();
            $table->string('order_prefix_id',255)->nullable();
            //$table->integer('size_id')->comment('Refer product_designs: size')->nullable();
            $table->integer('design_id')->comment('Refer product_designs: id')->nullable();
            $table->decimal('price', 12,2)->nullable();
            $table->string('quantity',255)->nullable();
            $table->bigInteger('delivery_id')->comment('refers to deliveries table id')->nullable();
            $table->bigInteger('shipping_address_id')->comment('refers to address table id')->nullable();
            $table->bigInteger('measurement_address_id')->comment('refers to address table id')->nullable();
            $table->Text('stiching_info')->nullable();
            $table->Text('additional_info')->nullable();
            $table->tinyInteger('is_customize')->comment('0:No,1:Yes')->nullable();
            /*$table->tinyInteger('is_customize')->comment('0:No,1:Yes')->nullable();*/
            $table->tinyInteger('status')->comment('0:Canceled,1:Placed,2:Out For Measurement,3:Arrived Tomorrow,4:Out For Delivery')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
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
