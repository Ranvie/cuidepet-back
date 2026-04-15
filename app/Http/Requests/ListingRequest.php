<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Classe de requisição para paginação e filtragem de resultados.
 */
class ListingRequest extends FormRequest {

  /**
   * Define as regras de validação para os parâmetros de paginação e filtragem.
   * @return array As regras de validação.
   */
  public function rules(): array {
    return [
      'page'               => 'nullable|integer|min:1',
      'limit'              => 'nullable|integer|min:1|max:100',
      'filters'            => 'nullable|array',
      'filters.*.field'    => 'required|string',
      'filters.*.operator' => 'required|string|in:=,>,<,>=,<=,!=,LIKE,IN,NOT IN',
      'filters.*.value'    => 'required',
      'filters.*.boolean'  => 'nullable|string|in:AND,OR',
      'orders'             => 'nullable|array',
      'orders.*.field'     => 'required|string',
      'orders.*.direction' => 'required|string|in:asc,desc',
    ];
  }

  /**
   * Prepara os dados para validação, definindo valores padrão para os parâmetros de paginação.
   */
  protected function prepareForValidation() {
    $this->merge([
      'page'  => $this->input('page', 1),
      'limit' => $this->input('limit', 10)
    ]);
  }
}
