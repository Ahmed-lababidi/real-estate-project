<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartment_orientations', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // شرقي - غربي - قبلي - بحري ...
            $table->string('name_en')->nullable(); // شرقي - غربي - قبلي - بحري ...
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartment_orientations');
    }
};
