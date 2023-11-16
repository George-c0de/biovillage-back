<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreOperationContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storeOperationContents', function (Blueprint $table) {
            $table->bigInteger('storeOperationId');
            $table->bigInteger('productId');
            $table->integer('storePlaceId');
            $table->integer('quantity');
            $table->smallInteger('condition')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storeOperationContents');
    }
}
