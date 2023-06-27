<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoDuplicateKana implements Rule
{
    protected $sentences;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($sentences)
    {
        $this->sentences = $sentences;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($this->sentences as $sentence) {
            if ($sentence['kana'] === $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '同じ文章が既に登録されています。';
    }
}
