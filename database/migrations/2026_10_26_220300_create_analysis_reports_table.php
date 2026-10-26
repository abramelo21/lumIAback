<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained()->cascadeOnDelete();
            $table->text('summary');
            $table->json('identified_skills');
            $table->json('strengths');
            $table->json('improvement_points');
            $table->json('project_recommendations');
            $table->json('experience_recommendations');
            $table->json('action_plan');
            $table->json('structure_notes')->nullable();
            $table->json('area_fit')->nullable();
            $table->timestamps();

            $table->unique('analysis_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_reports');
    }
};
