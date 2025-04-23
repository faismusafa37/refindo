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
        Schema::create('anggarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->nullable(); // Kalau mau dikaitkan ke project
            $table->bigInteger('current_amount')->default(0); // Anggaran saat ini
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggarans');
    }
};
