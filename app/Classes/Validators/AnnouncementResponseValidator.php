<?php

namespace App\Classes\Validators;

use App\Classes\FilterFieldDefinition;
use App\Classes\Validators\Base\ListingValidator;

/**
 * Classe de validação de filtros para consultas de respostas de anúncio.
 */
class AnnouncementResponseValidator extends ListingValidator {

  /**
   * Método Construtor
   */
  public function __construct() {
    $filterRules = [
      new FilterFieldDefinition(
        field: 'user.username',
        name: 'Nome do usuário que respondeu',
        description: 'Filtra os usuários por nome.',
        operators: ['LIKE'],
        booleanOperators: ['AND', 'OR'],
        valueType: 'string',
        acceptedValues: null
      ),
    ];

    parent::__construct($filterRules);
  }
}