<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {

            $table->id();

            $table->float('face_match_threshold')
                ->default(0.5);

            $table->integer('scan_interval')
                ->default(1000);

            $table->time('attendance_start_time')
                ->default('06:30:00');

            $table->time('late_after')
                ->default('07:00:00');

            $table->integer('max_descriptors')
                ->default(10);

            $table->boolean('auto_alpha')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};
