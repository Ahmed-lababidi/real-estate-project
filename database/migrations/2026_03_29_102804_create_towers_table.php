<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('towers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tower_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->unsignedInteger('number_of_floors')->nullable();
            $table->string('location_within_project')->nullable();
            $table->string('location_within_project_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('cover_image')->nullable();
            $table->string('model_3d_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'tower_category_id']);
            $table->index(['project_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('towers');
    }
};
