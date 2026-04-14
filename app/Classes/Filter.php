<?php

namespace App\Classes;

/**
 * Classe de filtro para consultas no banco de dados.
 */
class Filter {

  /**
   * @param string $column   Coluna a ser filtrada
   * @param string $operator Operador de comparação (ex: '=', '>', '<', 'LIKE', 'IN', 'NOT IN', etc.)
   * @param mixed  $value    Valor a ser comparado (string, int, bool, array)
   * @param string $boolean  Operador lógico para combinar com outros filtros (ex: 'AND', 'OR')
   */
  public function __construct(
    public string $column   = '',
    public string $operator = '=',
    public mixed  $value    = '',
    public string $boolean  = 'AND'
  ) {}
}
