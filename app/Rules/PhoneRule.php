<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneRule implements ValidationRule {
  
  /**
   * Valida o formato do telefone. O número deve seguir um dos seguintes formatos:
   * - +XX (XX)XXXXX-XXXX
   * - +XX (XX)XXXX-XXXX
   * @param string  $attribute Nome do atributo sendo validado
   * @param mixed   $value     Valor do atributo sendo validado
   * @param Closure $fail      Função de callback para falha de validação
   * @return void
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void {
    $regex = '/^\+(\d{2}) \(\d{2}\)\d{4,5}-\d{4}$/';

    if (!preg_match($regex, $value)) {
      $fail('O telefone enviado é inválido. Formatos: \'+XX (XX)XXXXX-XXXX\' ou \'+XX (XX)XXXX-XXXX\'');
    }
  }
}
