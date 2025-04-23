<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggaranHistory extends Model
{
    protected $fillable = ['anggaran_id', 'previous_amount', 'current_amount', 'changed_at'];

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class);
    }
}
