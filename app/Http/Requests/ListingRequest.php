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

  /**
   * Define mensagens de erro personalizadas para os erros de validação.
   * @return array As mensagens de erro personalizadas.
   */
  public function messages() :array {
    return [
      'page.integer'                => "O campo 'page' deve ser um número inteiro.",
      'page.min'                    => "O campo 'page' deve ser no mínimo 1.",
      'limit.integer'               => "O campo 'limit' deve ser um número inteiro.",
      'limit.min'                   => "O campo 'limit' deve ser no mínimo 1.",
      'limit.max'                   => "O campo 'limit' deve ser no máximo 100.",
      'filters.array'               => "O campo 'filters' deve ser um array.",
      'filters.*.field.required'    => "O campo 'field' é obrigatório para cada filtro.",
      'filters.*.field.string'      => "O campo 'field' deve ser uma string para cada filtro.",
      'filters.*.operator.required' => "O campo 'operator' é obrigatório para cada filtro.",
      'filters.*.operator.string'   => "O campo 'operator' deve ser uma string para cada filtro.",
      'filters.*.operator.in'       => "O campo 'operator' deve ser um dos seguintes: =, >, <, >=, <=, !=, LIKE, IN, NOT IN.",
      'filters.*.value.required'    => "O campo 'value' é obrigatório para cada filtro.",
      'filters.*.boolean.string'    => "O campo 'boolean' deve ser uma string para cada filtro.",
      'filters.*.boolean.in'        => "O campo 'boolean' deve ser um dos seguintes: AND, OR.",
      'orders.array'                => "O campo 'orders' deve ser um array.",
      'orders.*.field.required'     => "O campo 'field' é obrigatório para cada ordenação.",
      'orders.*.field.string'       => "O campo 'field' deve ser uma string para cada ordenação.",
      'orders.*.direction.required' => "O campo 'direction' é obrigatório para cada ordenação.",
      'orders.*.direction.string'   => "O campo 'direction' deve ser uma string para cada ordenação.",
      'orders.*.direction.in'       => "O campo 'direction' deve ser um dos seguintes: asc, desc."
    ];
  }
}
