<?php

namespace App\Http\Requests;

use App\Rules\PhoneRule;
use App\Rules\ZipcodeRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de validação para os detalhes do usuário, usada na atualização do perfil.
 * Define regras específicas para os campos que podem ser atualizados no perfil do usuário.
 */
class UserDetailRequest extends FormRequest {
  
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
      'username'                            => 'nullable|string',
      'phone'                               => ['nullable', 'string', new PhoneRule()],
      'imageProfile'                        => 'nullable|mimes:jpg,jpeg,png,webp|dimensions:min_width=300,max_width=5000,min_height=300,max_height=5000|max:5120',
      'addresses'                           => 'nullable|array',
      'addresses.*.action'                  => 'required|string|in:ADD,DEL',
      'addresses.*.cep'                     => ['required', new ZipcodeRule()],
      'preference'                          => 'nullable|array',
      'preference.receiveRegionAlarms'      => 'nullable|boolean',
      'preference.receiveAlarmsOnEmail'     => 'nullable|boolean',
    ];
  }
}
