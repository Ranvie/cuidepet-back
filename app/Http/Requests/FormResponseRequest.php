<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormResponseRequest extends FormRequest {
  
  /**
   * Regras de validação para o formulário de resposta do usuário.
   * @return array
   */
  public function rules() :array {
    return [
      'payload' => 'required|string|max:65000'
    ];
  }
}
