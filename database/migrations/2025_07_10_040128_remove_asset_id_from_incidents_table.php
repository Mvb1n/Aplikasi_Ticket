<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu dengan menyebutkan nama constraint-nya secara eksplisit
            $table->dropForeign('incidents_asset_id_foreign');
            // Setelah constraint dihapus, baru hapus kolomnya
            $table->dropColumn('asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            //
        });
    }
};
