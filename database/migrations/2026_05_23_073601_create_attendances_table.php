<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->time('attendance_time');

            $table->enum('status', [
                'hadir',
                'terlambat',
                'izin',
                'sakit',
                'alpha',
            ]);

            $table->float('confidence_score')
                ->nullable();

            $table->float('match_threshold_used')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
