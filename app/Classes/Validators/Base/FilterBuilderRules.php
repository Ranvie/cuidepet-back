<?php

namespace App\Classes\Validators\Base;

use App\Classes\Filter;
use App\Classes\FilterFieldDefinition;

/**
 * Classe de regras para construção de filtros em consultas no banco de dados.
 */
class FilterBuilderRules {

  /**
   * Filtros já validados.
   * @var Filter[]
   */
  protected array $filters = [];

  /** 
   * Regras de filtragem.
   * @var FilterFieldDefinition[]
   */
  protected array $rules = [];

  /**
   * Método Construtor
   * @param FilterFieldDefinition[] $rules Regras de filtragem
   */
  public function __construct(array $rules) {
    foreach ($rules as $rule) {
      if ($rule instanceof FilterFieldDefinition)
        $this->rules[$rule->field] = $rule;
    }
  }
  
  /**
   * Adiciona um filtro à lista de filtros.
   * Caso o filtro seja inválido de acordo com as regras definidas, ele não será adicionado.
   * @param  Filter $filter Filtro a ser adicionado.
   * @return self
   */
  public function addFilter(Filter $filter) :self {
    if (!$this->validateFilter($filter))
      return $this;

    $this->filters[] = $filter;
    return $this;
  }

  /**
   * Retorna os filtros validados.
   * @return Filter[]
   */
  public function build() :array {
    return $this->filters;
  }

  /**
   * Retorna as definições dos campos em formato de array (para API).
   * @return array
   */
  public function getFieldDefinitionsArray(): array {
    if (empty($this->rules)) {
      return [];
    }

    $result = [];
    foreach ($this->rules as $definition) {
      $result[$definition->field] = $definition->toArray();
    }

    return $result;
  }

  /**
   * Valida um filtro com base nas regras definidas.
   * @param  Filter $filter Filtro a ser validado.
   * @return bool
   */
  protected function validateFilter(Filter $filter) :bool {
    $definition = $this->getFieldDefinition($filter->column);
    
    if (!$definition)
      return false;

    // Valida operador específico do campo
    if (!$definition->isOperatorAllowed($filter->operator))
      return false;

    // Valida tipo do valor
    if (!$definition->isValueValid($filter->value))
      return false;

    // Valida valores aceitos (enums)
    if (!$definition->isAcceptedValue($filter->value))
      return false;

    // Valida arrays para IN/NOT IN
    if (\in_array($filter->operator, ['IN', 'NOT IN']) && !\is_array($filter->value))
      return false;

    return true;
  }

  /**
   * Busca a definição de um campo.
   * @param  string $field
   * @return FilterFieldDefinition|null
   */
  protected function getFieldDefinition(string $field): ?FilterFieldDefinition {
    return $this->rules[$field] ?? null;
  }

}