<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetDefaultToReal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE ONLY "orderItems" ALTER COLUMN "realUnits" SET DEFAULT 0;
        ');
        DB::statement('
            ALTER TABLE ONLY "orderItems" ALTER COLUMN "realTotal" SET DEFAULT 0;
        ');
        DB::statement('
            ALTER TABLE ONLY "orderItems" ALTER COLUMN "realPrice" SET DEFAULT 0;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('real', function (Blueprint $table) {
            //
        });
    }
}
