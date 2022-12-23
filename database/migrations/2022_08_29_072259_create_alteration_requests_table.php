<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlterationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alteration_requests', function (Blueprint $table) {
           $table->id();
            $table->integer('user_id')->comment('Refer users: id')->nullable();
             $table->enum('alteration_type', ['1', '2'])->comment('1:New Stiching Screen,2:Alteration')->nullable();
            $table->string('length',255)->nullable();
            $table->string('job_title',255)->nullable();
            $table->string('alteration_image',255)->nullable();
            $table->longtext('job_description')->nullable();
            $table->decimal('alteration_price',12,2)->nullable();
            $table->tinyInteger('status')->default('2')->comment('0:Denied,1:Accepted,2:Pending,3:Processing');
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
        Schema::dropIfExists('alteration_requests');
    }
}
