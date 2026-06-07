<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('answers')->nullable();
            $table->integer('score')->nullable();
            $table->integer('time_spent')->nullable(); // in seconds
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->integer('extra_time')->default(0); // extra minutes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_user');
    }
};
