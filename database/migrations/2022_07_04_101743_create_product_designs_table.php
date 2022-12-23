<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDesignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_designs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->comment('refers to Category table id')->nullable();
            $table->bigInteger('delivery_id')->comment('refers to deliveries table id')->nullable();
            $table->bigInteger('size_id')->comment('refers to sizes table id')->nullable();
            $table->string('size',255)->nullable();
            $table->string('title',255)->nullable();
            $table->string('quantity',255)->nullable();
            $table->string('short_description',255)->nullable();
            $table->decimal('price', 12,2)->nullable();
            $table->enum('inner_type', ['1', '2','3','4','5','6','7','8','9'])->comment('1:Blouse,2:Gown,3:Kurti,4:Suits,5:T-Shirts,6:Pants,7:Shirt,8:T-shirt,9:Nightwear')->nullable();
            $table->string('type_banner_img',255)->nullable();
            $table->enum('size', ['S','M','L','XL','XS','XXL'])->nullable();
            $table->tinyInteger('is_featured')->comment('0:Non-featured,1:featured')->nullable();
            $table->tinyInteger('is_trending')->comment('0:Non-trending,1:trending')->nullable();
            $table->tinyInteger('status')->default('1')->comment('0:Inactive,1:Active,3:deleted')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('product_designs');
    }
}
