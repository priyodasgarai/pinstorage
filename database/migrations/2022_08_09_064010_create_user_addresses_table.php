<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('full_name',255)->nullable();
            $table->string('phone',50)->nullable();
            $table->mediumText('address')->nullable();
            $table->mediumText('atra_street_sector_vilager')->nullable();
            $table->mediumText('landmark')->nullable();
            $table->string('pincode',50)->nullable();
            $table->integer('city_id')->comment('Refer city: id')->nullable();
            $table->integer('state_id')->comment('Refer state: id')->nullable();
            $table->integer('country_id')->comment('Refer country: id')->nullable();
            $table->integer('otp')->nullable();
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
        Schema::dropIfExists('user_addresses');
    }
}