<?php

namespace App\Classes;

/**
 * Define as regras e metadados de um campo filtrável.
 */
class FilterFieldDefinition {

  /**
   * @param string   $field          Nome do campo (ex: 'status', 'animal.name')
   * @param string   $name           Nome amigável para exibição
   * @param string   $description    Descrição do que o filtro faz
   * @param string[] $operators      Operadores permitidos para este campo
   * @param string   $valueType      Tipo do valor: 'string', 'number', 'boolean', 'date', 'array'
   * @param mixed    $acceptedValues Valores aceitos (array para enums, string para formato, null para livre)
   */
  public function __construct(
    public string $field,
    public string $name,
    public string $description,
    public array  $operators,
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
    if ($this->acceptedValues === null || !\is_array($this->acceptedValues))
      return true;

    return \in_array($value, $this->acceptedValues);
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
      'field'          => $this->field,
      'name'           => $this->name,
      'description'    => $this->description,
      'operators'      => $this->operators,
      'valueType'      => $this->valueType,
      'acceptedValues' => $this->acceptedValues
    ];
  }
}
