<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateYoutubesTable.
 */
class CreateYoutubesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('youtube', function(Blueprint $table) {
            $table->increments('id');
            $table->string('youtube_id', 25)->unique();
            $table->integer('cid');
            $table->string('accent');
            $table->integer('id2');
            $table->string('src');
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
		Schema::drop('youtube');
	}
}
