<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsiteMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('website_id');
            $table->unsignedInteger('form_request_id');
            $table->unsignedInteger('form_map_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_maps');
    }
}
