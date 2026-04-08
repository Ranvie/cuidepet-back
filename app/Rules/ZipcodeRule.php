<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Regra de validação personalizada para validar um CEP no formato 'XXXXX-XXX' ou 'XXXXXXXX'.
 */
class ZipcodeRule implements ValidationRule {

  /**
   * Valida um CEP.
   * @param  string  $attribute Nome do atributo sendo validado
   * @param  mixed   $value     Valor do atributo sendo validado
   * @param  Closure $fail      Função de callback para falha de validação
   * @return void
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void {
    $regex = '/^\d{5}-?\d{3}$/';

    if(!preg_match($regex, $value)) {
      $fail("O campo {$attribute} deve ser um CEP válido no formato XXXXX-XXX ou XXXXXXXX.");
    }
  }
}
