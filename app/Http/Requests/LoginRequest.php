<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest {

  /**
   * Regras de validação para o login.
   * @return array
   */
  public function rules() :array {
    return [
      'email'    => 'required|string|email',
      'password' => 'required|string|min:6'
    ];
  }
}
