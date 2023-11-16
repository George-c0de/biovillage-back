<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Realgifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE "orderGifts" ADD COLUMN "realQty" INT NOT NULL DEFAULT 0
        ');
        DB::statement('
            ALTER TABLE "orderGifts" ADD COLUMN "realTotalBonuses" INT NOT NULL DEFAULT 0
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('
            ALTER TABLE "orderGifts" DROP COLUMN "realQty"
        ');
        DB::statement('
            ALTER TABLE "orderGifts" DROP COLUMN "realTotalBonuses"
        ');
    }
}
