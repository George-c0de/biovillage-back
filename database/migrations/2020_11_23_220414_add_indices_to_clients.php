<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIndicesToClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP INDEX IF EXISTS  client_phone');
        DB::statement('
            CREATE INDEX IF NOT EXISTS client_phone ON clients(phone text_pattern_ops);
        ');

        DB::statement('DROP INDEX IF EXISTS  client_name;');
        DB::statement('
            CREATE INDEX IF NOT EXISTS client_name ON clients(name);        
        ');

        DB::statement(' DROP INDEX IF EXISTS client_dtcreate;');
        DB::statement('
            CREATE INDEX IF NOT EXISTS client_dtadd ON clients("createdAt");
        ');
        DB::statement('DROP INDEX IF EXISTS client_lastlogin;');
        DB::statement('
            CREATE INDEX IF NOT EXISTS client_last_login ON clients("lastLogin");
        ');

        DB::statement('DROP INDEX IF EXISTS client_platform;');
        DB::statement('
            CREATE INDEX IF NOT EXISTS client_platform ON clients("lastPlatform");
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX IF EXISTS  client_phone');
        DB::statement('DROP INDEX IF EXISTS  client_name;');
        DB::statement('DROP INDEX IF EXISTS client_dtcreate;');
        DB::statement('DROP INDEX IF EXISTS client_lastlogin;');
        DB::statement('DROP INDEX IF EXISTS client_platform;');
    }
}
