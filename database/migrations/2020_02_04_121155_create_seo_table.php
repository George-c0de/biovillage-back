<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('seo', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('entityId')->index();
            $table->string('groupName')->index();

            $table->string('seoTitle')->default('');
            $table->text('seoDescription')->default('');

            $table->string('ogLocale', 5)->default('');
            $table->string('ogType')->default('');
            $table->text('ogSiteName')->default('');
            $table->text('ogTitle')->default('');
            $table->text('ogDescription')->default('');

            $table->text('ogUrl')->default('');
            $table->text('ogImage')->default('');
            $table->text('ogVideo')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('seo');
    }
}
