<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_master', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['1', '2','3'])->comment('1:Intro Screen,2:Advertisement,3:Discount')->nullable();
            $table->integer('role_id')->comment('Refer roles: id')->nullable();
            $table->string('banner_title',255)->nullable();
            $table->string('banner_sub_title',255)->nullable();
            $table->longText('banner_description')->nullable();
            $table->longText('redirect_url')->nullable();
            $table->string('image',255)->nullable();
            $table->tinyInteger('status')->default('1')->comment('0:Inactive,1:Active,3:deleted')->nullable();
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
        Schema::dropIfExists('banner_master');
    }
}
