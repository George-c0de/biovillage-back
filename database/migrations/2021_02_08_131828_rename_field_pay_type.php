<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameFieldPayType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE payments RENAME COLUMN type TO method
        ');
        DB::statement('
            ALTER TABLE orders RENAME COLUMN "primaryPaymentType" TO "primaryPaymentMethod"
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
            ALTER TABLE payments RENAME COLUMN method TO type
        ');
        DB::statement('
            ALTER TABLE orders RENAME COLUMN "primaryPaymentMethod" TO "primaryPaymentType"
        ');
    }
}
