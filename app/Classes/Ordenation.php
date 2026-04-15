<?php

namespace App\Classes;

/**
 * Classe de ordenação para consultas no banco de dados.
 */
class Ordenation {

  /**
   * Método Construtor
   * @param  string $field     Campo pelo qual ordenar (ex: 'created_at', 'name')
   * @param  string $direction Direção da ordenação ('asc' ou 'desc')
   */
  public function __construct(
    public string $field,
    public string $direction = 'asc'
  ) {}

}