<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected',
            ])
                ->default('approved')
                ->after('notes');

            $table->foreignId('requested_by_user_id')
                ->nullable()
                ->after('approval_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->after('requested_by_user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by_user_id');

            $table->text('review_notes')
                ->nullable()
                ->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['requested_by_user_id']);
            $table->dropForeign(['reviewed_by_user_id']);

            $table->dropColumn([
                'approval_status',
                'requested_by_user_id',
                'reviewed_by_user_id',
                'reviewed_at',
                'review_notes',
            ]);
        });
    }
};
