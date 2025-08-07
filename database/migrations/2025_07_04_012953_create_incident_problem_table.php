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
        Schema::create('incident_problem', function (Blueprint $table) {
        $table->foreignId('incident_id')->constrained()->onDelete('cascade');
        $table->foreignId('problem_id')->constrained()->onDelete('cascade');
        $table->primary(['incident_id', 'problem_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_problem');
    }
};
