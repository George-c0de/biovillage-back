<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeOrderTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE orders ADD COLUMN IF NOT EXISTS "deliveryAreaId" INT
        ');
        DB::statement('
            UPDATE orders SET "deliveryAreaId" = 0 WHERE "deliveryAreaId" IS NULL
        ');
        DB::statement('
            ALTER TABLE orders ALTER COLUMN "deliveryAreaId" SET NOT NULL   
        ');
        DB::statement('
            DROP INDEX IF EXISTS order_created_at
        ');
        DB::statement('
            CREATE INDEX IF NOT EXISTS order_created_at ON orders("createdAt")
        ');
        DB::statement('
            DROP INDEX IF EXISTS order_delivery_date
        ');
        DB::statement('
            CREATE INDEX IF NOT EXISTS order_delivery_date ON orders("deliveryDate")
        ');
        DB::statement('
            DROP INDEX IF EXISTS order_finished_at
        ');
        DB::statement('
            CREATE INDEX IF NOT EXISTS order_finished_at ON orders("finishedAt") 
            WHERE "finishedAt" IS NOT NULL 
        ');
        DB::statement('
            ALTER TABLE orders ADD COLUMN IF NOT EXISTS "primaryPaymentType" VARCHAR(5)
        ');
        DB::statement('
            UPDATE orders SET "primaryPaymentType" = ?
        ', [ \App\Models\PaymentModel::METHOD_CASH]);
        DB::statement('
            ALTER TABLE orders ALTER COLUMN "primaryPaymentType" SET NOT NULL   
        ');
        DB::statement('
            CREATE INDEX IF NOT EXISTS order_client_id ON orders("clientId")
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
