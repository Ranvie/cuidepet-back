<?php

namespace App\Http\Requests;

use App\Rules\ZipcodeRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de requisição para validação de dados relacionados à newsletter.
 * Define as regras de validação para inscrição e cancelamento de inscrição na newsletter, incluindo email e CEP.
 */
class NewsletterRequest extends FormRequest {
  
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
      'email'   => 'required|string|email',
      'zipcode' => ['required', 'string', new ZipcodeRule()],
    ];
  }
}
