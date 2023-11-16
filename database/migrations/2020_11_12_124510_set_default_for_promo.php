<?php

use App\Service\PromotionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetDefaultForPromo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(sprintf('
            ALTER TABLE products ALTER COLUMN promotion SET DEFAULT \'%s\';
        ', \App\Models\ProductModel::PROMO_NO));
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
