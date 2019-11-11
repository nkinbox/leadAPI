<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastUpdateAtToAgents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->timestamp('last_update_at')->nullable();
        });
        Schema::table('call_registers', function (Blueprint $table) {
            $table->string('identified', 10)->nullable();
            $table->unsignedInteger('identified_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('last_update_at');
        });
        Schema::table('call_registers', function (Blueprint $table) {
            $table->dropColumn('identified');
            $table->dropColumn('identified_id');
        });
    }
}
