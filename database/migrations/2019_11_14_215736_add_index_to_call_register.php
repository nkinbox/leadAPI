<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCallRegister extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('call_registers', function (Blueprint $table) {
            $table->index(['saved_name']);
            $table->index(['phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_registers', function (Blueprint $table) {
            $table->dropIndex(['saved_name']);
            $table->dropIndex(['phone_number']);
        });
    }
}
