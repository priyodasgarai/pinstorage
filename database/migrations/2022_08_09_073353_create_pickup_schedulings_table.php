<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupSchedulingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_schedulings', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->comment('Refer orders: id')->nullable();
            $table->string('pickup_date',255)->nullable();
            $table->string('pickup_time',255)->nullable();
            $table->string('exp_delivery_date',255)->nullable();
            $table->longtext('pickup_address')->nullable();
            $table->string('contact_person_name',255)->nullable();
            $table->string('contact_person_number',255)->nullable();
            $table->string('contact_person_email',255)->nullable();
            /*$table->string('alternative_contact_person_name',255)->nullable();*/
            $table->string('contact_person_alternative_number',255)->nullable();
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
        Schema::dropIfExists('pickup_schedulings');
    }
}
