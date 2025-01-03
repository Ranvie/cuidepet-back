<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $regex = '/^\+(\d{2}) \(\d{2}\)\d{4,5}-\d{4}$/';

        if(!preg_match($regex, $value)){
            $fail('O telefone enviado é inválido. Formatos: \'+XX (XX)XXXXX-XXXX\' ou \'+XX (XX)XXXX-XXXX\'');
        }
    }
}
