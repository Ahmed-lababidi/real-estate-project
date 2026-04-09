<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('name_en')->nullable();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->decimal('area', 10, 2);
            $table->string('location_within_project')->nullable();
            $table->string('location_within_project_en')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('type', [
                'garden',
                'pool',
                'farm',
                'court',
                'land',
                'hospital',
                'school',
                'office',
                'other',
            ])->default('other')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
