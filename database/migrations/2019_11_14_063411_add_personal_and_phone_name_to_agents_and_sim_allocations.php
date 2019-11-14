<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPersonalAndPhoneNameToAgentsAndSimAllocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sim_allocations', function (Blueprint $table) {
            $table->boolean('is_personal')->default(0);
            $table->string('sim_name', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sim_allocations', function (Blueprint $table) {
            $table->dropColumn('is_personal');
            $table->dropColumn('sim_name');
        });
    }
}
