<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDesignImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_design_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_design_id')->comment('refers to Product design table id')->nullable();
            $table->string('file_name',255)->nullable();
            $table->tinyInteger('is_primary')->comment('0:not primary,1:primary')->nullable();
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
        Schema::dropIfExists('product_design_images');
    }
}
