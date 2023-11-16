<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickpoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickPoints', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('address');
            $table->unsignedDecimal('lat', $precision = 10, $scale = 5);
            $table->unsignedDecimal('lon', $precision = 10, $scale = 5);
            $table->text('workDays');
            $table->text('contacts');
            $table->boolean('isActive');
            $table->timestamps();
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
        Schema::dropIfExists('pickPoints');
    }
}
