<?php

namespace App\Http\Requests;

use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest {
  
  /**
   * Define se o usuário tem permissão na requisição
   * @return bool
   */
  public function authorize() :bool {
    return true;
  }

  /**
   * Regras de validação para os dados da requisição, a partir do método usado.
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

  /** Regras de validação para o método POST
   * @return array
   */
  private function postRules() :array {
    return [
      'username'             => 'required|string',
      'email'                => 'required|string|email|unique:tb_user,email',
      'password'             => 'required|string|min:6|confirmed:passwordConfirmation',
      'passwordConfirmation' => 'required|string',
      'imageProfile'         => 'string|nullable|string',
      'phone'                => ['string', 'nullable', new PhoneRule()],
    ];
  }

  /** Regras de validação para o método PUT
   * @return array
   */
  private function putRules() :array {
    return [
      'username'             => 'nullable|string',
      'password'             => 'nullable|string|min:6|confirmed:passwordConfirmation',
      'passwordConfirmation' => 'nullable|string',
      'imageProfile'         => 'string|nullable|string',
      'phone'                => ['string', 'nullable', new PhoneRule()]
    ];
  }
}
