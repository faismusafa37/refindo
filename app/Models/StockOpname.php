<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number',
        'part_name',
        'stock_in',
        'stock_out',
        'final_stock',
        'user_id', // Pastikan user_id ada di fillable
    ];

    /**
     * Relasi dengan model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menyimpan data StockOpname dan mengisi user_id otomatis sesuai user yang login
     * Sebaiknya dilakukan di controller atau resource terkait
     */
}
