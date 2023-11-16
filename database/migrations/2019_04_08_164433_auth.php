<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Auth extends Migration
{
    public function up()
    {
        // Таблица для клиентов
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invitedBy')->nullable();
            $table->string('avatarUrl')->nullable();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('patronymic')->nullable();
            $table->timestamp('birthday')->nullable();
            $table->unsignedTinyInteger('allowMailing')->default(1);
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('lastPlatform')->nullable();
            $table->string('referralCode')->unique();
            $table->unsignedBigInteger('bonuses')->default(0);
            $table->timestamp('lastLogin')->nullable();
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
            $table->softDeletes('deletedAt');
        });

        // Таблица для админов
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fullName');
            $table->string('phone')->unique();
            $table->string('password');
            $table->text('roles');
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
            $table->softDeletes('deletedAt');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
        Schema::dropIfExists('admins');
    }
}
