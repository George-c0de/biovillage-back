<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNewFieldsToProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE products ADD COLUMN composition VARCHAR(255) NOT NULL DEFAULT \'\'
        ');
        DB::statement('
            ALTER TABLE products ADD COLUMN "shelfLife" VARCHAR(128) NOT NULL DEFAULT \'\'
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
            ALTER TABLE products DROP COLUMN IF EXISTS composition;
        ');
        DB::statement('
            ALTER TABLE products DROP COLUMN IF EXISTS "shelfLife";
        ');
    }
}
