<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChageUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE units 
            DROP COLUMN step;
        ');
        DB::statement('
            ALTER TABLE units 
            RENAME COLUMN min TO step;
        ');
        Schema::table('units', function (Blueprint $table) {
            $table->string('shortDerName')->default('');
            $table->integer('factor')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
