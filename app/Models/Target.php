<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = [
     'user_id',
     'judul',
     'target_nominal',
     'status',
    ];

    public function progress() {
        return $this->hasMany(Progress::class);
    }

    public function totalProgress() {
        return $this->progress()->sum('jumlah');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
