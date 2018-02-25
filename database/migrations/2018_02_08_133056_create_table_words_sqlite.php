<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWordsSqlite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('sqlite')->create('words', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->index();
            $table->string('name');
            $table->string('word_type');
            $table->longText('use');
            $table->longText('video_data');
            $table->longText('audio_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlite')->drop('words');
    }
}
