<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentence extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','sentence', 'kana'];

    public function randomSentences(int $num): Sentence
    {
        return Sentence::inRandomOrder()->limit($num)->get();
    }
}
