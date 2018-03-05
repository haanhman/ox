<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableWordAddWordTypeIntColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('words','wt')) {
            Schema::table('words', function (Blueprint $table) {
                $table->integer('wt')->default(0);
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
        if (Schema::hasColumn('words','wt')) {
            Schema::table('words', function (Blueprint $table) {
                $table->dropColumn(['wt']);
            });
        }
    }
}
