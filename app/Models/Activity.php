<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'user_id',
    ];

    // Definisi relasi ke tabel users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
