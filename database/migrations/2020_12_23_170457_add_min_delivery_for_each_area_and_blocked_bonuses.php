<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMinDeliveryForEachAreaAndBlockedBonuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE clients ADD COLUMN "blockedBonuses" INT NOT NULL DEFAULT 0
        ');
        DB::statement('
            ALTER TABLE "deliveryArea" ADD COLUMN "deliveryFreeSum" INT NOT NULL DEFAULT 0
        ');
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
