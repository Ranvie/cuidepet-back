<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest {  

  /**
   * Regras de validação para o formulário do usuário.
   * @return array
   */
  public function rules() :array {
    $rules = [];

    $rules = match ($this->method()) {
      'POST'  => $this->postRules(),
      'PUT'   => $this->putRules(),
      default => [],
    };

    return $rules;
  }

  /**
   * Regras de validação para o formulário do usuário.
   * @return array
   */
  public function postRules() :array {
    return [
      'title'   => 'required|string|max:255',
      'payload' => 'required|string|max:65000'
    ];
  }

  /**
   * Regras de validação para o formulário do usuário.
   * @return array
   */
  public function putRules() :array {
    return [
      'title'   => 'nullable|string|max:255',
      'payload' => 'nullable|string|max:65000',
      'active'  => 'nullable|boolean'
    ];
  }
  
}
