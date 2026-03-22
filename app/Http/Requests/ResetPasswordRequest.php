<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest {
  
  /**
   * Regras de validação para a redefinição de senha.
   * @return array
   */
  public function rules() :array {
    return [
      'password'             => 'required|string|min:6|confirmed:passwordConfirmation',
      'passwordConfirmation' => 'required|string'
    ];
  }
}
