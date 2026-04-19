<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE visits MODIFY COLUMN panel_structure ENUM('not_connected_well','connected_well','no_panel_structure') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE visits MODIFY COLUMN panel_structure ENUM('not_connected_well','connected_well') NULL");
    }
};
