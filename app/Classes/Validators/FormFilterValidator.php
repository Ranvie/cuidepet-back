<?php

namespace App\Classes\Validators;

use App\Classes\FilterFieldDefinition;
use App\Classes\Validators\Base\ListingValidator;

/**
 * Classe de validação de filtros para consultas de meus formulários.
 */
class FormFilterValidator extends ListingValidator {

  /**
   * Método Construtor
   */
  public function __construct() {
    $filterRules = [
      new FilterFieldDefinition(
        field: 'title',
        name: 'Título do formulário',
        description: 'Filtra os formulários a partir do título.',
        operators: ['LIKE'],
        booleanOperators: ['AND', 'OR'],
        valueType: 'string',
        acceptedValues: null
      ),
    ];

    parent::__construct($filterRules);
  }
}