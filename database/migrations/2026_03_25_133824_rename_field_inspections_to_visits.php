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
        Schema::rename('field_inspections', 'visits');

        Schema::table('visits', function (Blueprint $table) {
            $table->foreignId('tower_id')->after('id')->nullable()->constrained('towers')->nullOnDelete();
            
            $table->dropColumn([
                'document_number',
                'location_name',
                'location_detail',
                'kelurahan',
                'kecamatan',
                'latitude',
                'longitude'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign(['tower_id']);
            $table->dropColumn('tower_id');

            $table->string('document_number')->nullable();
            $table->string('location_name')->nullable();
            $table->text('location_detail')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });

        Schema::rename('visits', 'field_inspections');
    }
};
