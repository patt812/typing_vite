<?php

namespace App\Providers;

use Faker\Generator as FakerGenerator;
use Faker\Provider\Base as BaseProvider;

class CustomFakerProvider extends BaseProvider
{
    protected $sentences = [];

    protected $kanas = [];

    protected $sentenceCounter = 0;

    protected $kanaCounter = 0;

    public function __construct(FakerGenerator $faker)
    {
        parent::__construct($faker);
        $this->sentences = json_decode(file_get_contents(resource_path('sentence/template.json')), true);
        $this->kanas = array_map(function ($sentence) {
            return $sentence['kana'];
        }, $this->sentences);
    }

    public function sentence(FakerGenerator $faker)
    {
        if ($this->sentenceCounter >= count($this->sentences)) {
            $this->sentenceCounter = 0;
        }
        $sentence = $this->sentences[$this->sentenceCounter]['sentence'];
        $this->sentenceCounter++;
        return $sentence;
    }

    public function kanaSentence(FakerGenerator $faker)
    {
        if ($this->kanaCounter >= count($this->kanas)) {
            $this->kanaCounter = 0;
        }
        $kana = $this->kanas[$this->kanaCounter];
        $this->kanaCounter++;
        return $kana;
    }
}
