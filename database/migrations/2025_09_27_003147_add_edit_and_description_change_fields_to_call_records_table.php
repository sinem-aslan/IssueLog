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
            $table->unsignedBigInteger('edited_by')->nullable()->after('status_changed_at');
            $table->timestamp('edited_at')->nullable()->after('edited_by');
            $table->unsignedBigInteger('description_changed_by')->nullable()->after('edited_at');
            $table->timestamp('description_changed_at')->nullable()->after('description_changed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('call_records', function (Blueprint $table) {
            $table->dropColumn([
                'edited_by',
                'edited_at',
                'description_changed_by',
                'description_changed_at'
            ]);
        });
    }
};
