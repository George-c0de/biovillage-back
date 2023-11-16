<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('entityId')->index();
            $table->string('groupName')->index();
            $table->text('src');
            $table->text('srcThumb')->nullable();
            $table->boolean('isDeleted')->default(false);
            $table->integer('order')->default(0);
            $table->timestamp('deletedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('images');
    }
}
