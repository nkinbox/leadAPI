<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSimAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sim_allocations', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->unsignedInteger('agent_id');
            $table->string('operator', 30)->nullable();
            $table->string('dial_code', 5)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->index(['agent_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sim_allocations');
    }
}
