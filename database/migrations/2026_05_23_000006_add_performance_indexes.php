<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->index('type');
        });

        Schema::table('attempts', function (Blueprint $table) {
            $table->index(['quiz_id', 'submitted_at']);
        });

        Schema::table('options', function (Blueprint $table) {
            $table->index('is_correct');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['type']);
        });

        Schema::table('attempts', function (Blueprint $table) {
            $table->dropIndex(['quiz_id', 'submitted_at']);
        });

        Schema::table('options', function (Blueprint $table) {
            $table->dropIndex(['is_correct']);
        });
    }
};
