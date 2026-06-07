<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->text('notes')
                ->nullable()
                ->after('match_threshold_used');

            $table->index([
                'student_id',
                'attendance_date',
            ], 'attendances_student_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_student_date_index');
            $table->dropColumn('notes');
        });
    }
};
