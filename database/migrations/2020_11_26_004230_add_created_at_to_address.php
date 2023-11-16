<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCreatedAtToAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE addresses ADD COLUMN "createdAt" TIMESTAMP 
        ');
        DB::statement('
            UPDATE addresses SET "createdAt" = CURRENT_TIMESTAMP;
        ');
        DB::statement('
            ALTER TABLE addresses ALTER "createdAt" SET NOT NULL; 
        ');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('address', function (Blueprint $table) {
            //
        });
    }
}
