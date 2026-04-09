<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tower_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('apartment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('farm_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('land_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('phone')->index();
            $table->string('email')->nullable()->index();

            $table->enum('type', [
                'general',
                'project_inquiry',
                'tower_inquiry',
                'apartment_inquiry',
                'farm_inquiry',
                'land_inquiry',
                'reservation_followup',
            ])->default('general')->index();

            $table->text('message')->nullable();

            $table->enum('status', [
                'new',
                'contacted',
                'closed',
                'spam',
            ])->default('new')->index();

            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
    }
};
