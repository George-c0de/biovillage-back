<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenamePolygonColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE "deliveryArea" RENAME poly TO "polygon"
        ');
        DB::statement('
            ALTER TABLE ONLY "orders" ALTER COLUMN "adminsComment" SET NOT NULL
        ');
        DB::statement('
            ALTER TABLE ONLY "orders" ALTER COLUMN "commentForClient" SET NOT NULL
        ');
        DB::statement('
            ALTER TABLE ONLY "orders" ALTER COLUMN "clientsComment" SET NOT NULL
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
