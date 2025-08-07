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
        Schema::create('asset_incident', function (Blueprint $table) {
            $table->primary(['asset_id', 'incident_id']);
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_incident');
    }
};
