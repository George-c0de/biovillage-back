<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDeletedAndRealAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE "orderItems" ADD COLUMN IF NOT EXISTS "deletedAt" TIMESTAMP DEFAULT NULL
        ');
        DB::statement('
            ALTER TABLE "orderItems" ADD COLUMN IF NOT EXISTS "realQty" INT
        ');
        DB::statement('
            ALTER TABLE "orderItems" ADD COLUMN IF NOT EXISTS "realPrice" INT
        ');
        DB::statement('
            ALTER TABLE "orderItems" ADD COLUMN IF NOT EXISTS "realTotal" INT
        ');
        DB::statement('
            ALTER TABLE "orderItems" ADD COLUMN IF NOT EXISTS "unitStep" INT
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
