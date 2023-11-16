<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class Adddeliveryareas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE TABLE "deliveryArea"(
                id SERIAL PRIMARY KEY,
                "deletedAt" TIMESTAMP DEFAULT NULL,
                name varchar(255) NOT NULL DEFAULT \'\',
                price int NOT NULL,
                color varchar(10) NOT NULL DEFAULT \'#FFFFFF\',
                poly polygon
            );
        ');
        DB::statement('
            CREATE INDEX "deliveryAreaDeletedAt" ON "deliveryArea"(("deletedAt" IS NULL))
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
            DROP TABLE IF EXISTS "deliveryArea" CASCADE 
        ');
    }
}
