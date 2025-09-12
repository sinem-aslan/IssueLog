<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('call_records', function (Blueprint $table) {
            $table->id();
            $table->dateTime('call_date'); // Çağrı gelme tarihi
            $table->unsignedBigInteger('user_id'); // Çağrıyı kaydeden temsilci
            $table->string('student_tc'); // Arayan öğrencinin T.C. kimlik numarası
            $table->string('student_phone'); // Arayan öğrencinin telefon numarası
            $table->text('reason'); // Arama gerekçesi
            $table->unsignedBigInteger('department_id'); // Yönlendirilen ilgili birim
            $table->enum('status', ['pending', 'in_progress', 'resolved'])->default('pending'); // Çağrı durumu
            $table->dateTime('solution_date')->nullable(); // Çözüm tarihi
            $table->boolean('is_urgent')->default(false); // Acil mi değil mi
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_records');
    }
};
