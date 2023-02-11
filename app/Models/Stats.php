<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    use HasFactory;

    protected $table = 'sentence_stats';

    public function stat() {
        return $this->hasOne(Sentence::class);
    }
}
