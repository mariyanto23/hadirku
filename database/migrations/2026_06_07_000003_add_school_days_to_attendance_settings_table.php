<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->json('school_days')
                ->nullable()
                ->after('auto_alpha');
        });

        DB::table('attendance_settings')
            ->whereNull('school_days')
            ->update([
                'school_days' => json_encode([1, 2, 3, 4, 5, 6]),
            ]);
    }

    public function down(): void
    {
        Schema::table('attendance_settings', function (Blueprint $table) {
            $table->dropColumn('school_days');
        });
    }
};
