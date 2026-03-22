<?php

namespace App\Classes;

class Filter
{

    /**
     * @param string $column   Coluna a ser filtrada
     * @param string $operator Operador de comparação (ex: '=', '>', '<', 'LIKE', etc.)
     * @param string $value    Valor a ser comparado
     * @param string $boolean  Operador lógico para combinar com outros filtros (ex: 'AND', 'OR')
     */
    public function __construct(
        public string $column = '',
        public string $operator = '=',
        public string $value = '',
        public string $boolean = 'AND'
    ){}
}
