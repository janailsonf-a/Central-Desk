<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('slas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('priority_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('first_response_minutes');
            $table->unsignedInteger('resolution_minutes');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'priority_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slas');
    }
};