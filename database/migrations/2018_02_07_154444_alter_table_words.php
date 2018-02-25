<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableWords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function(Blueprint $table) {
            $table->boolean('is_ok')->default(false);
            $table->string('word_type')->index();
            $table->longText('audio');
            $table->longText('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('words', function(Blueprint $table) {
            $table->dropColumn(['is_ok', 'word_type', 'audio', 'content']);
        });
    }
}
