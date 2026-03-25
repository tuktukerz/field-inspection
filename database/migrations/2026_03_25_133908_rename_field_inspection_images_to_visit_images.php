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
        Schema::rename('field_inspection_images', 'visit_images');

        Schema::table('visit_images', function (Blueprint $table) {
            $table->renameColumn('field_inspection_id', 'visit_id');
            $table->foreignId('tower_id')->after('id')->nullable()->constrained('towers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_images', function (Blueprint $table) {
            $table->dropForeign(['tower_id']);
            $table->dropColumn('tower_id');
            $table->renameColumn('visit_id', 'field_inspection_id');
        });

        Schema::rename('visit_images', 'field_inspection_images');
    }
};
