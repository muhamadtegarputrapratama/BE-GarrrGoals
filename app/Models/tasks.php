<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tasks extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'status',
        'file,'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
