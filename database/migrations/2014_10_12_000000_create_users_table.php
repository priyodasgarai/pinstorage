<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->comment('Refer roles: id')->nullable();
            $table->string('name',255)->nullable();
            $table->string('email',50)->unique()->nullable();
            $table->string('phone',50)->nullable();
            $table->string('password')->nullable();
            $table->string('original_password')->nullable();
            $table->mediumText('address')->nullable();
            $table->mediumText('landmark')->nullable();
            $table->string('image',255)->nullable();
            $table->string('pincode',50)->nullable();
            $table->integer('city_id')->comment('Refer city: id')->nullable();
            $table->integer('state_id')->comment('Refer state: id')->nullable();
            $table->integer('country_id')->comment('Refer country: id')->nullable();
            $table->integer('otp')->nullable();
            $table->tinyInteger('device_type')->comment('1:Android,2:iOS')->nullable();
            $table->mediumText('device_token')->unique()->nullable();
            $table->mediumText('app_access_token')->unique()->nullable();
            $table->tinyInteger('email_validate')->default('0');
            $table->tinyInteger('phone_validate')->default('0');
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
        Schema::dropIfExists('users');
    }
}
