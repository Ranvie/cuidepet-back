<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de validação para a senha do usuário, usada na atualização da senha.
 * Define regras específicas para os campos relacionados à senha do usuário.
 */
class UserPasswordRequest extends FormRequest {
  
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
    return [
      'currentPassword'      => 'required|string|min:6',
      'newPassword'          => 'required|string|min:6|confirmed:passwordConfirmation',
      'passwordConfirmation' => 'required|string',
    ];
  }
}
