<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDefaultToAdminsComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE ONLY "orders" ALTER COLUMN "adminsComment" SET DEFAULT \'\';
        ');
        DB::statement('
            ALTER TABLE ONLY "orders" ALTER COLUMN "commentForClient" SET DEFAULT \'\';
        ');
        DB::statement('
            ALTER TABLE ONLY "orders" ALTER COLUMN "clientsComment" SET DEFAULT \'\';
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins_comment', function (Blueprint $table) {
            //
        });
    }
}
