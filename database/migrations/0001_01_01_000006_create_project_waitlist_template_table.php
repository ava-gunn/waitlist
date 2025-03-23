<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_waitlist_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('waitlist_template_id')->constrained()->cascadeOnDelete();
            $table->json('customizations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['project_id', 'waitlist_template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_waitlist_template');
    }
};
