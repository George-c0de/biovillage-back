<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Logs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::connection('log')->dropIfExists('clientLogs');
        Schema::connection('log')->create('clientLogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->jsonb('data');
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::connection('log')->dropIfExists('clientLogs');
    }
}
