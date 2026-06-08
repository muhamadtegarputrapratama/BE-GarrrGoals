<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $fillable = [
        'target_id',
        'jumlah'
    ];

    public function target() {
        return $this->belongsTo(Target::class);
    }
}
