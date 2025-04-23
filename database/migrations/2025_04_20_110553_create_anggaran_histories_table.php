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
        Schema::create('anggaran_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anggaran_id');
            $table->bigInteger('previous_amount')->default(0);
            $table->bigInteger('current_amount')->default(0);
            $table->timestamp('changed_at')->nullable(); // Tanggal perubahan
            $table->timestamps();

            $table->foreign('anggaran_id')->references('id')->on('anggarans')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggaran_histories');
    }
};
