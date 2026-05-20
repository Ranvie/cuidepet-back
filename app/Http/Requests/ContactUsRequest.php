<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest {
  
  /**
   * Regras de validação para o formulário de resposta do usuário.
   * @return array
   */
  public function rules() :array {
    return [
      'name'    => 'required|string|max:255',
      'email'   => 'required|email|max:255',
      'message' => 'required|string'
    ];
  }
}