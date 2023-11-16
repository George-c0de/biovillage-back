<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddParentIdForPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE payments ADD COLUMN "parentId" INT DEFAULT NULL
        ');
        DB::statement('
            CREATE INDEX payments_parent_id ON payments("parentId");
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
            DROP INDEX IF EXISTS payments_parent_id
        ');
        DB::statement('
            ALTER TABLE payments DROP COLUMN "parentId" 
        ');
    }
}
