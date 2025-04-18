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
        Schema::table('activities', function (Blueprint $table) {
            // Drop column lama
            $table->dropColumn(['title', 'description']);

            // Kolom baru untuk activity
            $table->string('no_unit_tiket');
            $table->text('job_description');
            $table->text('task_description');
            $table->string('category_issues');
            $table->string('priority');
            $table->string('pic_assignee');
            $table->dateTime('time_in');
            $table->dateTime('time_out');
            $table->string('status');
            $table->decimal('price', 15, 2);

            // Data stock
            $table->string('part_number')->nullable();
            $table->string('part_name')->nullable();
            $table->text('part_description')->nullable();
            $table->integer('stock_in')->default(0);
            $table->integer('stock_out')->default(0);
            $table->decimal('price_stock', 15, 2)->default(0);
            $table->integer('final_stock')->default(0);

            // Upload fields
            // $table->string('photo_1')->nullable();
            // $table->string('photo_2')->nullable();
            // $table->string('photo_3')->nullable();
            // $table->string('bast_document')->nullable();

            // Relasi user
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->dropColumn([
                'no_unit_tiket',
                'job_description',
                'hour_meter',
                'task_description',
                'category_issues',
                'priority',
                'pic_assignee',
                'time_in',
                'time_out',
                'status',
                'price',
                'part_number',
                'part_name',
                'part_description',
                'stock_in',
                'stock_out',
                'price_stock',
                'final_stock',
                'photo_1',
                'photo_2',
                'photo_3',
                'bast_document',
            ]);

            // Drop foreign key & user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
