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
        Schema::create('field_inspections', function (Blueprint $table) {
            $table->id();

            // SECTION 1
            $table->string('document_number')->nullable();
            $table->date('inspection_date');
            $table->string('location_name');
            $table->text('location_detail')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->enum('location_type', ['jpo','jpm','flyover','underpass','pedestrian','rth']);
            $table->decimal('observation_distance', 8, 2)->nullable();
            $table->enum('tower_type', ['pole','rangka'])->nullable();

            // SECTION 2
            $table->enum('bolt_count', ['tidak_lengkap','lengkap','tidak_terlihat'])->nullable();
            $table->enum('bolt_condition', ['berkarat','tidak_berkarat','tidak_terlihat'])->nullable();
            $table->enum('bolt_position', ['longgar','tidak_longgar','tidak_terlihat'])->nullable();

            // SECTION 3
            $table->enum('frame_condition', ['miring','tegak'])->nullable();
            $table->enum('frame_maintenance', ['not_maintained','maintained'])->nullable();
            $table->enum('frame_rust', ['rusted','not_rusted'])->nullable();
            $table->enum('frame_porous', ['porous','not_porous'])->nullable();

            $table->enum('joint_maintenance', ['not_maintained','maintained','not_visible'])->nullable();
            $table->enum('joint_rust', ['rusted','not_rusted','not_visible'])->nullable();
            $table->enum('joint_porous', ['porous','not_porous','not_visible'])->nullable();

            // SECTION 4
            $table->enum('panel_structure', ['not_connected_well','connected_well'])->nullable();
            $table->enum('panel_status', ['loose','no_loose','no_panel'])->nullable();
            $table->enum('lamp_frame', ['not_connected_well','connected_well','no_lamp'])->nullable();

            // SECTION 5
            $table->text('notes')->nullable();

            // SECTION 6
            $table->enum('construction_feasibility', ['dangerous','not_dangerous'])->nullable();
            $table->enum('follow_up_action', ['enforcement_proposal','periodic_monitoring'])->nullable();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_inspections');
    }
};
