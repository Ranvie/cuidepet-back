<?php

namespace App\Classes;

use Illuminate\Support\Arr;

/**
 * Define as regras e metadados de um campo filtrável.
 */
class FilterFieldDefinition {

  /**
   * @param string   $field            Nome do campo (ex: 'status', 'animal.name')
   * @param string   $name             Nome legível para exibição (ex: 'Status', 'Nome do Animal')
   * @param string   $description      Descrição do que o filtro faz
   * @param string[] $operators        Operadores permitidos para este campo
   * @param string[] $booleanOperators Operadores lógicos permitidos para combinar este filtro com outros (ex: 'AND', 'OR')
   * @param string   $valueType        Tipo do valor: 'string', 'number', 'boolean', 'date', 'array'
   * @param mixed    $acceptedValues   Valores aceitos (array para enums, string para formato, null para livre)
   */
  public function __construct(
    public string $field,
    public string $name,
    public string $description,
    public array  $operators,
    public array  $booleanOperators = ['AND', 'OR'],
    public string $valueType = 'string',
    public mixed  $acceptedValues = null
  ) {}

  /**
   * Valida se um operador é permitido para este campo.
   * @param  string $operator
   * @return bool
   */
  public function isOperatorAllowed(string $operator): bool {
    return \in_array($operator, $this->operators);
  }

  /**
   * Valida se um operador lógico é permitido para este campo.
   * @param  string $booleanOperator
   * @return bool
   */
  public function isBooleanOperatorAllowed(string $booleanOperator): bool {
    return \in_array($booleanOperator, $this->booleanOperators);
  }

  /**
   * Valida se o valor está de acordo com o tipo esperado.
   * @param  mixed $value
   * @return bool
   */
  public function isValueValid(mixed $value): bool {
    return match($this->valueType) {
      'string'  => \is_string($value),
      'number'  => \is_numeric($value),
      'boolean' => \is_bool($value) || \in_array($value, [0, 1, '0', '1', 'true', 'false'], true),
      'date'    => $this->isValidDate($value),
      'array'   => \is_array($value),
      default   => true
    };
  }

  /**
   * Valida se o valor está na lista de valores aceitos (para enums).
   * @param  mixed $value
   * @return bool
   */
  public function isAcceptedValue(mixed $value): bool {
    if($this->acceptedValues === null)
      return true;
  
    $values = \is_array($value) ? $value : [$value];
    $values = Arr::flatten($values);
    foreach($values as $value) {
      if (!\in_array($value, $this->acceptedValues))
        return false;
    }

    return true;
  }

  /**
   * Valida se uma string é uma data válida.
   * @param  mixed $value
   * @return bool
   */
  private function isValidDate(mixed $value): bool {
    if (!\is_string($value)) return false;
    
    $date = \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $value);
    if ($date && $date->format('Y-m-d\TH:i:s\Z') === $value) return true;
    
    $date = \DateTime::createFromFormat('Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value;
  }

  /**
   * Converte a definição para array (para API response).
   * @return array
   */
  public function toArray(): array {
    return [
      'field'            => $this->field,
      'name'             => $this->name,
      'description'      => $this->description,
      'operators'        => $this->operators,
      'booleanOperators' => $this->booleanOperators,
      'valueType'        => $this->valueType,
      'acceptedValues'   => $this->acceptedValues
    ];
  }
}
