<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AddressRule implements ValidationRule {

  /**
   * Valida um endereço no formato 'CEP|RUA|NUMERO|UF|CIDADE|BAIRRO|COMPLEMENTO'.
   * @param  string  $attribute Nome do atributo sendo validado
   * @param  mixed   $value     Valor do atributo sendo validado
   * @param  Closure $fail      Função de callback para falha de validação
   * @return void
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void {
    $regex = '/^(\d{5}-\d{3})\|([^|]+)\|(\d+)\|([A-Z]{2})\|([^|]+)\|([^|]+)\|([^|]*)$/';

    if (!preg_match($regex, $value)) {
      $fail('O endereço enviado é inválido. Formato: \'CEP|RUA|NUMERO|UF|CIDADE|BAIRRO|COMPLEMENTO\'');
    }
  }
}
