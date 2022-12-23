<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDesignAddonsImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_design_addons_images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_design_addon_id')->comment('refers to Product design addon table id')->nullable();
            $table->string('title',255)->nullable();
            $table->decimal('price',12,2)->nullable();
            $table->string('addon_image',255)->nullable();
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
        Schema::dropIfExists('product_design_addons_images');
    }
}
