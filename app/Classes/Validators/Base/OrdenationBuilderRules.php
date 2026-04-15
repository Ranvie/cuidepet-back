<?php

namespace App\Classes\Validators\Base;

use App\Classes\Ordenation;
use App\Classes\OrdenationFieldDefinition;

/**
 * Classe de regras para construção de ordenações em consultas no banco de dados.
 */
class OrdenationBuilderRules {

  /**
   * Ordenações já validadas.
   * @var Ordenation[]
   */
  protected array $ordenations = [];

  /** 
   * Regras de ordenação.
   * @var OrdenationFieldDefinition[]
   */
  protected array $rules = [];

  /**
   * Método Construtor
   * @param OrdenationFieldDefinition[] $rules Regras de ordenação
   */
  public function __construct(array $rules) {
    foreach ($rules as $rule) {
      if ($rule instanceof OrdenationFieldDefinition)
        $this->rules[$rule->field] = $rule;
    }
  }
  
  /**
   * Adiciona uma ordenação à lista de ordenações.
   * Caso a ordenação seja inválida de acordo com as regras definidas, ela não será adicionada.
   * @param  Ordenation $ordenation Ordenação a ser adicionada.
   * @return self
   */
  public function addOrdenation(Ordenation $ordenation) :self {
    if (!$this->validateOrdenation($ordenation))
      return $this;

    $this->ordenations[] = $ordenation;
    return $this;
  }

  /**
   * Retorna as ordenações validadas.
   * @return Ordenation[]
   */
  public function build() :array {
    return $this->ordenations;
  }

  /**
   * Retorna as definições dos campos em formato de array (para API).
   * @return array
   */
  public function getFieldDefinitionsArray(): array {
    if (empty($this->rules))
      return [];

    $result = [];
    foreach ($this->rules as $definition)
      $result[$definition->field] = $definition->toArray();

    return $result;
  }

  /**
   * Valida uma ordenação com base nas regras definidas.
   * @param  Ordenation $ordenation Ordenação a ser validada.
   * @return bool
   */
  protected function validateOrdenation(Ordenation $ordenation) :bool {
    $definition = $this->getFieldDefinition($ordenation->field);
    
    if (!$definition)
      return false;

    // Valida direção (asc/desc)
    if (!$definition->isDirectionAllowed($ordenation->direction))
      return false;

    return true;
  }

  /**
   * Busca a definição de um campo.
   * @param  string $field
   * @return OrdenationFieldDefinition|null
   */
  protected function getFieldDefinition(string $field): ?OrdenationFieldDefinition {
    return $this->rules[$field] ?? null;
  }

}
