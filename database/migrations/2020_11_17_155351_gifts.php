<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Gifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE TABLE gifts (
                id SERIAL NOT NULL PRIMARY KEY,
                active BOOL NOT NULL DEFAULT true,
                "order" INT NOT NULL DEFAULT 500,
                "createdAt" TIMESTAMP NOT NULL,
                "updatedAt" TIMESTAMP NOT NULL,
                "deletedAt" TIMESTAMP DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                description VARCHAR(5000) NOT NULL DEFAULT \'\',
                price INT NOT NULL DEFAULT 0,
                composition VARCHAR(255) NOT NULL DEFAULT \'\',
                "shelfLife" VARCHAR(128) NOT NULL DEFAULT \'\'
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
        DB::statement('DROP TABLE IF EXISTS gifts');
    }
}
