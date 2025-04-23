<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function anggarans()
{
    return $this->hasMany(Anggaran::class);
}
}
