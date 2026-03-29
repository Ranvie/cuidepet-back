<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest {
  
  /**
   * Regras de validação para o registro de usuário.
   * @return array
   */
  public function rules() :array {
    return [
      'username'             => 'required|string',
      'email'                => 'required|string|email|unique:tb_user,email',
      'password'             => 'required|string|min:6|confirmed:passwordConfirmation',
      'passwordConfirmation' => 'required|string',
      'useTerms'             => 'required|boolean|accepted'
    ];
  }
}
