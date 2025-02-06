<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AddressRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $regex = '/^(\d{5}-\d{3})\|([^|]+)\|(\d+)\|([A-Z]{2})\|([^|]+)\|([^|]+)\|([^|]*)$/';

        if(!preg_match($regex, $value)){
            $fail('O endereço enviado é inválido. Formato: \'CEP|RUA|NUMERO|UF|CIDADE|BAIRRO|COMPLEMENTO\'');
        }
    }
}
