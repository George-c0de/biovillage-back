<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('slug');
            $table->integer('price')->nullable();

            $table->integer('parentId')->nullable();
            $table->boolean('isGroup')->defalut(false);

            $table->boolean('isVisible')->default(true);
            $table->integer('order')->default(500);
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
            $table->softDeletes('deletedAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalog');
    }
}
