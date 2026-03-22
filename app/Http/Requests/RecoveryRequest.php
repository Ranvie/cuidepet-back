<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecoveryRequest extends FormRequest {
  
  /**
   * Regras de validação para a recuperação de senha.
   * @return array
   */
  public function rules() :array {
    return [
      'email' => 'required|string|email'
    ];
  }
}
