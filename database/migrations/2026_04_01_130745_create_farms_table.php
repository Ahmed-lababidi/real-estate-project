<?php

use App\Enums\FarmStatus;
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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('name_en')->nullable();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->decimal('area', 10, 2);
            $table->unsignedTinyInteger('rooms_number')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->boolean('has_parking')->default(false);
            $table->boolean('has_pool')->default(false);
            $table->boolean('has_garden')->default(false);
            $table->decimal('price', 12, 2);
            $table->string('location_within_project')->nullable();
            $table->string('location_within_project_en')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('status')->default(FarmStatus::AVAILABLE->value);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
