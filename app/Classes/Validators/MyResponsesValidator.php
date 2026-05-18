<?php

namespace App\Classes\Validators;

use App\Classes\FilterFieldDefinition;
use App\Classes\Validators\Base\ListingValidator;

/**
 * Classe de validação de filtros para consultas de minhas respostas.
 */
class MyResponsesValidator extends ListingValidator {

  /**
   * Método Construtor
   */
  public function __construct() {
    $filterRules = [
      new FilterFieldDefinition(
        field: 'announcement.animal.name',
        name: 'Nome do pet associado ao anúncio',
        description: 'Filtra as respostas do usuário pelo nome do pet associado ao anúncio.',
        operators: ['LIKE'],
        booleanOperators: ['AND', 'OR'],
        valueType: 'string',
        acceptedValues: null
      ),
    ];

    parent::__construct($filterRules);
  }
}