<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AnggaranHistory;

class Anggaran extends Model
{
    protected $fillable = ['project_id', 'current_amount'];

    // Relasi dengan history anggaran
    public function histories(): HasMany
    {
        return $this->hasMany(AnggaranHistory::class);
    }

    // Relasi dengan project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Menyimpan history setiap kali nilai anggaran berubah
    protected static function booted()
    {
        static::updating(function ($anggaran) {
            // Cek jika 'current_amount' berubah
            if ($anggaran->isDirty('current_amount')) {
                // Simpan perubahan di tabel anggaran_histories
                AnggaranHistory::create([
                    'anggaran_id' => $anggaran->id,
                    'previous_amount' => $anggaran->getOriginal('current_amount'),
                    'current_amount' => $anggaran->current_amount,
                    'changed_at' => now(),
                ]);
            }
        });
    }
}
