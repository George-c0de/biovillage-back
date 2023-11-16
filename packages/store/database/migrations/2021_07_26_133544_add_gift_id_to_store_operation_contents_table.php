<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddGiftIdToStoreOperationContentsTable extends Migration
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
            ALTER "productId" DROP NOT NULL;
        ');
        DB::statement('
            ALTER TABLE "storeOperationContents"
            ALTER "unitId" DROP NOT NULL;
        ');
        DB::statement('
            ALTER TABLE "storeOperationContents"
            ALTER COLUMN "netCostPerStep" SET DEFAULT 0;
        ');
        Schema::table('storeOperationContents', function (Blueprint $table) {
            $table->integer('giftId')->nullable();
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
