<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_registers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sim_allocation_id', 50);
            $table->string('agent_id', 50);
            $table->string('dial_code', 5);
            $table->string('phone_number', 15);
            $table->string('saved_name', 100)->nullable();
            $table->unsignedInteger('duration')->default(0);
            $table->timestamp('device_time')->nullable()->index();
            $table->string('call_type')->nullable();
            $table->boolean('status');
            $table->timestamps();
            $table->index(['sim_allocation_id', 'device_time']);
            $table->index(['agent_id', 'device_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call_registers');
    }
}
