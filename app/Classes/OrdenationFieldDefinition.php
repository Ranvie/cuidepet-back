<?php

namespace App\Classes;

/**
 * Define as regras e metadados de um campo ordenável.
 */
class OrdenationFieldDefinition {

  /**
   * @param string   $field       Nome do campo (ex: 'created_at', 'animal.name')
   * @param string   $name        Nome legível para exibição (ex: 'Data de criação', 'Nome do Animal')
   * @param string   $description Descrição do que a ordenação faz
   * @param string[] $directions  Direções permitidas (geralmente ['asc', 'desc'])
   */
  public function __construct(
    public string $field,
    public string $name,
    public string $description,
    public array  $directions = ['asc', 'desc']
  ) {}

  /**
   * Valida se uma direção é permitida para este campo.
   * @param  string $direction
   * @return bool
   */
  public function isDirectionAllowed(string $direction): bool {
    return \in_array(strtolower($direction), array_map('strtolower', $this->directions));
  }

  /**
   * Converte a definição para array (para API response).
   * @return array
   */
  public function toArray(): array {
    return [
      'field'       => $this->field,
      'name'        => $this->name,
      'description' => $this->description,
      'directions'  => $this->directions
    ];
  }
}
