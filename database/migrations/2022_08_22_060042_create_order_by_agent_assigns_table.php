<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderByAgentAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_by_agent_assigns', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->comment('Refer orders: id')->nullable();
            $table->integer('user_id')->comment('Refer users: id')->nullable();
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
        Schema::dropIfExists('order_by_agent_assigns');
    }
}
