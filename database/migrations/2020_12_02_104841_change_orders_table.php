<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE payments DROP COLUMN IF EXISTS "productId"
        ');
        DB::statement('
            ALTER TABLE payments ADD COLUMN IF NOT EXISTS status VARCHAR(7) NOT NULL
        ');
        DB::statement('
            ALTER TABLE payments ADD COLUMN IF NOT EXISTS data TEXT NOT NULL DEFAULT \'\'
        ');
        DB::statement('
            ALTER TABLE "orderItems" ADD COLUMN IF NOT EXISTS "unitId" INT
        ');
        DB::statement('
            ALTER TABLE "orderItems" ADD COLUMN IF NOT EXISTS "unitMin" INT
        ');
        DB::statement('
            ALTER TABLE "orders" ADD COLUMN IF NOT EXISTS "deliveryDate" DATE NOT NULL
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
