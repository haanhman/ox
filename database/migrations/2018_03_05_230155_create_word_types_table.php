<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateWordTypesTable.
 */
class CreateWordTypesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{	
		Schema::create('word_type', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('weight')->default(0);
		});
		DB::update('INSERT INTO word_type (name) SELECT DISTINCT word_type FROM words');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('word_type');
	}
}
