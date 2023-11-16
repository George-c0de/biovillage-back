<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('storeOperations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('storeId');
            $table->string('type');
            $table->string('status');
            $table->bigInteger('adminId')->nullable();
            $table->bigInteger('clientId')->nullable();
            $table->bigInteger('orderId')->nullable();
            $table->text('comment')->nullable();

            $table->timestamp('createdAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storeOperations');
    }
}
