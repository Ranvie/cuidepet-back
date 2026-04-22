<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de requisição para validação de dados relacionados a denúncias.
 * Define as regras de validação para o registro de denúncias, incluindo tipo, descrição e identificação do anúncio ou formulário relacionado.
 */
class ReportRequest extends FormRequest {
  
  /**
   * Define se o usuário tem permissão na requisição
   * @return bool
   */
  public function authorize() :bool {
    return true;
  }

  /**
   * Regras de validação para o registro de denúncias.
   * @return array
   */
  public function rules() :array {
    return [
      'type'            => 'required|string|in:form,announcement',
      'announcementId'  => 'required|integer',
      'reportMessageId' => 'required|integer',
      'description'     => 'required|string|max:300'
    ];
  }
}
