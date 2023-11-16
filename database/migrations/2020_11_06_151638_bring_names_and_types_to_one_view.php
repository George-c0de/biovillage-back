<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BringNamesAndTypesToOneView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE "catalogSections" RENAME COLUMN "isVisible" TO active;');
        DB::statement('ALTER TABLE products RENAME COLUMN visible TO active;');
        DB::statement('ALTER TABLE sliders RENAME COLUMN title TO name;');
        DB::statement('ALTER TABLE sliders RENAME COLUMN subtitle TO description;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
