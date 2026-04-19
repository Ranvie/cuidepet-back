<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest {  
  /**
   * Regras de validação para o formulário do usuário.
   * @return array
   */
  public function rules() :array {
    return [
      'title'   => 'required|string|max:255',
      'payload' => 'required|string|max:65000'
    ];
  }
}
