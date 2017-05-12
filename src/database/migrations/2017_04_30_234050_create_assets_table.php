<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Table: assets
         */
        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('type', 10);
            $table->string('name', 100)->unique();
            $table->string('library', 100);
            $table->string('current_version', 50);
            $table->string('latest_version', 50);
            $table->string('new_version', 50)->nullable();
            $table->string('file', 255);
            $table->unsignedTinyInteger('version_mask_check');
            $table->unsignedTinyInteger('version_mask_autoupdate');
            $table->boolean('testing')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assets');
    }

}