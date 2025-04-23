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
        'project_id', 
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
        'total_price', 
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Hitung total_price secara otomatis saat menyimpan
    protected static function booted()
    {
        static::saving(function ($activity) {
            $activity->total_price = ($activity->price ?? 0) + ($activity->price_stock ?? 0);
        });
    }
}
