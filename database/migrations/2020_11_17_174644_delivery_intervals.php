<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeliveryIntervals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE TABLE "deliveryIntervals" (
                "id" SERIAL PRIMARY KEY,
                "dayOfWeek" SMALLINT NOT NULL,
                "startHour" SMALLINT NOT NULL,
                "startMinute" SMALLINT NOT NULL,
                "endHour" SMALLINT NOT NULL,
                "endMinute" SMALLINT NOT NULL,
                "deletedAt" TIMESTAMP DEFAULT NULL,
                "updatedAt" TIMESTAMP NOT NULL,
                "createdAt" TIMESTAMP NOT NULL
            );
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
            DROP TABLE IF EXISTS "deliveryIntervals"
        ');
    }
}
