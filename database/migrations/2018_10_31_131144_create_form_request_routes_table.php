<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormRequestRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_request_routes', function (Blueprint $table) {
            $table->unsignedInteger('form_map_id')->index();
            $table->unsignedInteger('form_request_id');
            $table->unsignedInteger('crm_table_id');
            $table->unsignedInteger('form_field_id');
            $table->unsignedInteger('crm_column_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_request_routes');
    }
}
