<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE payments ADD COLUMN "extId" TEXT DEFAULT \'\'
        ');
        DB::statement('
            CREATE INDEX IF NOT EXISTS payments_ext_id ON payments("extId")
        ');
        DB::statement('
            ALTER TABLE payments ADD COLUMN "confirmation" TEXT DEFAULT \'\'
        ');
        DB::statement('
            ALTER TABLE payments ADD COLUMN "paymentToken" TEXT DEFAULT \'\'
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
            DROP INDEX IF EXISTS payments_ext_id
        ');
        DB::statement('
            ALTER TABLE payments DROP COLUMN "extId" CASCADE
        ');
        DB::statement('
            ALTER TABLE payments DROP COLUMN "confirmation"
        ');
        DB::statement('
            ALTER TABLE payments DROP COLUMN "paymentToken"
        ');
    }
}
