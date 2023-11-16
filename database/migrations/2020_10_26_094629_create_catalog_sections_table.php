<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogSections', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
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
        Schema::dropIfExists('catalogSections');
    }
}
