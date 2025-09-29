<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('call_records', function (Blueprint $table) {
            $table->unsignedBigInteger('status_changed_by')->nullable()->after('status');
            $table->timestamp('status_changed_at')->nullable()->after('status_changed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('call_records', function (Blueprint $table) {
            $table->dropColumn(['status_changed_by', 'status_changed_at']);
        });
    }
};
