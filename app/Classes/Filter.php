<?php

namespace App\Classes;

class Filter
{
    public function __construct(
        public string $column = '',
        public string $operator = '=',
        public string $value = '',
        public string $boolean = 'AND'
    )
    {
    }
}
