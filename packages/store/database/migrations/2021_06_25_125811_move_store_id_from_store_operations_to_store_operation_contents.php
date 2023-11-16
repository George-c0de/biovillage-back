<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveStoreIdFromStoreOperationsToStoreOperationContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('storeOperations', function (Blueprint $table) {
            $table->dropColumn('storeId');
        });

        Schema::table('storeOperationContents', function (Blueprint $table) {
            $table->integer('storeId');
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
