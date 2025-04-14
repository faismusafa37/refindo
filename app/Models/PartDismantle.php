<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartDismantle extends Model
{
    use HasFactory;

    // Menambahkan kolom-kolom yang boleh diisi massal
    protected $fillable = [
        'part_number',
        'description',
        'stock_dismantle',
        'no_tiket',
        'desc',
    ];
}
