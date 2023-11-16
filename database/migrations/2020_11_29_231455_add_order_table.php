<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE TABLE orders(
                id SERIAL PRIMARY KEY,
                "createdAt" TIMESTAMP NOT NULL,
                "updatedAt" TIMESTAMP NOT NULL,
                "finishedAt" TIMESTAMP,
                "status" VARCHAR(8) NOT NULL,
                "clientsComment" VARCHAR(1024) NOT NULL DEFAULT \'\',
                "adminsComment" VARCHAR(1024) NOT NULL DEFAULT \'\',
                "commentForClient" VARCHAR(1024) NOT NULL DEFAULT \'\',
                "promoCode" VARCHAR(64) NOT NULL DEFAULT \'\',
                "platform" VARCHAR(255) NOT NULL DEFAULT \'\',
                "clientId" INT NOT NULL,
                "addressId" INT NOT NULL,
                "deliveryIntervalId" INT NOT NULL,
                "productsSum" INT NOT NULL,
                "deliverySum" INT NOT NULL,
                "bonuses" INT NOT NULL,
                "total" INT NOT NULL
            );
        ');
        DB::statement('
            CREATE TABLE "orderItems" (
                id SERIAL PRIMARY KEY,
                "createdAt" TIMESTAMP NOT NULL,
                "orderId" INT NOT NULL,
                "productId" INT NOT NULL,
                "unitId" INT NOT NULL,
                "qty" INT NOT NULL DEFAULT 1,
                "price" INT NOT NULL,
                "total" INT NOT NULL 
            );
        ');
        DB::statement('
            CREATE TABLE "orderGifts" (
                id SERIAL PRIMARY KEY,
                "createdAt" TIMESTAMP NOT NULL,
                "orderId" INT NOT NULL,
                "giftId" INT NOT NULL,
                "qty" INT NOT NULL DEFAULT 1,
                "bonuses" INT NOT NULL,
                "totalBonuses" INT NOT NULL
            );
        ');

        DB::statement('
            CREATE TABLE "payments" (
                id SERIAL PRIMARY KEY,
                "createdAt" TIMESTAMP NOT NULL,
                "madeAt" TIMESTAMP DEFAULT NULL,
                "refundedAt" TIMESTAMP DEFAULT NULL,
                "canceledAt" TIMESTAMP DEFAULT NULL,
                "productId" INT NOT NULL,
                "transaction" VARCHAR(6),
                "type" VARCHAR(5),
                "total" INT NOT NULL,
                "data" TEXT NOT NULL DEFAULT \'\',
                "orderId" INT NOT NULL
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
            DROP TABLE IF EXISTS payments CASCADE 
        ');
        DB::statement('
            DROP TABLE IF EXISTS "orderGifts" CASCADE 
        ');
        DB::statement('
            DROP TABLE IF EXISTS "orderItems" CASCADE 
        ');
        DB::statement('
            DROP TABLE IF EXISTS "orders" CASCADE 
        ');
    }
}
