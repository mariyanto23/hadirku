<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 40)->index();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->boolean('allow_attendance')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_holidays');
    }
};
