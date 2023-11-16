<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNetCostPerStepToStoreOperationContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            TRUNCATE "storeOperationContents"
        ');

        DB::statement('
            TRUNCATE "storeOperations"
        ');

        Schema::table('storeOperationContents', function (Blueprint $table) {
            $table->integer('netCostPerStep');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
