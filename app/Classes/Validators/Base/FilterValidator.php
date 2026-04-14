<?php

namespace App\Classes\Validators\Base;

use App\Classes\Filter;
use App\Classes\FilterFieldDefinition;
use App\Http\Requests\ListingRequest;

/**
 * Classe de validação de filtros para consultas no banco de dados.
 */
abstract class FilterValidator {

  /**
   * Regras de validação para os filtros.
   * @var FilterBuilderRules
   */
  protected FilterBuilderRules $filterBuilderRules;

  /**
   * Método Construtor
   * @param FilterFieldDefinition[] $rules Regras de validação para os filtros (colunas permitidas, operadores, etc.)
   */
  public function __construct(array $rules) {
    $this->filterBuilderRules = new FilterBuilderRules($rules);
  }

  /**
   * Constrói os filtros a partir de uma requisição de listagem.
   * @param  ListingRequest $request Requisição contendo os filtros a serem construídos.
   * @return array                   Filtros construídos a partir da requisição.
   */
  public function build(ListingRequest $request) :array {
    $filters = $request->validated();
    $filters = $filters['filters'] ?? [];

    foreach($filters as $filter) {
      $value = $filter['value'] ?? '';

      if (\in_array($filter['operator'], ['IN', 'NOT IN']) && \is_string($value)) {
        $value = array_map('trim', explode(',', $value));
      }

      $obFilter = new Filter(
        column:   $filter['field']    ?? '',
        operator: $filter['operator'] ?? '',
        value:    $value,
      );

      $this->filterBuilderRules->addFilter($obFilter);
    }

    return $this->filterBuilderRules->build();
  }

  /**
   * Retorna as definições dos campos filtráveis (para API discovery).
   * @return array
   */
  public function getFieldDefinitions(): array {
    return $this->filterBuilderRules->getFieldDefinitionsArray();
  }

}