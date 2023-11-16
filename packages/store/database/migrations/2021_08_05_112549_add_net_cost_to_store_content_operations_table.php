<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNetCostToStoreContentOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE "storeOperationContents"
            ALTER COLUMN "netCostPerStep" DROP DEFAULT;
        ');
        DB::statement('
            ALTER TABLE "storeOperationContents"
            ALTER COLUMN "netCostPerStep" DROP NOT NULL;
        ');
        Schema::table('storeOperationContents', function (Blueprint $table) {
            $table->integer('netCost')->nullable();
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
