<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('clientId');
            $table->string('city');
            $table->string('street')->nullable();
            $table->string('house');
            $table->string('building')->nullable();
            $table->string('entrance')->nullable();
            $table->string('floor')->nullable();
            $table->string('doorphone')->nullable();
            $table->string('appt')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
