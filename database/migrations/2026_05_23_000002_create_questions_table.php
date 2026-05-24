<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->longText('body');
            $table->string('image_path')->nullable();
            $table->string('youtube_url', 500)->nullable();
            $table->unsignedTinyInteger('marks')->default(1);
            $table->unsignedSmallInteger('order')->default(0);
            $table->string('correct_value', 1000)->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
