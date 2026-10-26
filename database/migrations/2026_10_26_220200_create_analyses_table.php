<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('queued');
            $table->string('provider', 80)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('prompt_version', 30)->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['resume_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
