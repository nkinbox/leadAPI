<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_columns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('crm_table_id');
            $table->string('name', 50);
            $table->string('type', 50)->default(null);
            $table->boolean('required')->default(0);
            $table->boolean('sometimes')->default(0);
            $table->string('default_value', 100)->default(null); //eval(date('Y-m-d')) or Fresh or null
            $table->unsignedInteger('max_length');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crm_columns');
    }
}
