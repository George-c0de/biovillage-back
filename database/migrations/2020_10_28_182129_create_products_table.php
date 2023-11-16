<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->boolean('visible')->default(true);
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('imageId');
            $table->integer('unitId');
            $table->integer('min')->default(1);
            $table->integer('price');
            $table->integer('bonusesPercentage')->default(0);
            $table->string('flag', 2)->nullable();
            $table->integer('categorySectionId');
            $table->string('certificates')->nullable();
            $table->softDeletes('deletedAt');
        });

        DB::statement('ALTER TABLE products ADD COLUMN tags integer[];');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
