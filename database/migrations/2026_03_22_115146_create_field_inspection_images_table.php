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
        Schema::create('field_inspection_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('field_inspection_id')
                ->constrained('field_inspections')
                ->cascadeOnDelete();

            $table->string('image_path');

            $table->string('caption')->nullable();

            $table->timestamp('taken_at')->nullable();

            $table->timestamps();

            $table->index('field_inspection_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_inspection_images');
    }
};
