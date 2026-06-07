<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('students', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('students', 'birth_date')) {
                $table->dropColumn('birth_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'phone')) {
                $table->string('phone')->nullable()->after('gender');
            }

            if (! Schema::hasColumn('students', 'address')) {
                $table->text('address')->nullable()->after('gender');
            }

            if (! Schema::hasColumn('students', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('address');
            }
        });
    }
};
