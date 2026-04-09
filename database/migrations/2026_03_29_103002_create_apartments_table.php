<?php

use App\Enums\ApartmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tower_id')->constrained()->cascadeOnDelete();
            $table->foreignId('apartment_orientation_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name')->nullable();
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->integer('floor_number');
            $table->string('unit_number')->nullable();

            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('rooms_number')->nullable();

            $table->decimal('area', 10, 2);
            $table->decimal('price', 12, 2);

            $table->string('status')->default(ApartmentStatus::AVAILABLE->value);
            // $table->timestamp('reservation_expires_at')->nullable();

            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('cover_image')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tower_id', 'status']);
            $table->index(['price']);
            $table->index(['area']);
            $table->index(['floor_number']);
            $table->index(['apartment_orientation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
