<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterWordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function(Blueprint $table) {
            $table->text('video_data')->nullable()->after('crawler_done');
            $table->boolean('crawler_video_done')->default(false)->after('video_data');
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
            $table->dropColumn(['video_data', 'crawler_video_done']);
        });
    }
}
