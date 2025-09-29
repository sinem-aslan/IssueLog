<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::table('call_records', function (Blueprint $table) {
            $table->text('admin_description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('call_records', function (Blueprint $table) {
            $table->dropColumn('admin_description');
        });
    }

};
