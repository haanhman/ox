<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableYoutube extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('youtube', 's1')) {
            Schema::table('youtube', function (Blueprint $table) {
                $table->integer('s1')->default(0);
                $table->integer('e1')->default(0);
                $table->integer('s2')->default(0);
                $table->integer('e2')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('youtube', 's1')) {
            Schema::table('youtube', function (Blueprint $table) {
                $table->dropColumn(['s1', 's2', 'e1', 'e2']);
            });
        }
    }
}
