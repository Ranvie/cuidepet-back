<?php

namespace App\Classes\Validators;

use App\Classes\FilterFieldDefinition;
use App\Classes\OrdenationFieldDefinition;
use App\Classes\Validators\Base\ListingValidator;

/**
 * Classe de validação de filtros para consultas de anúncios do usuário.
 */
class MyAnnouncementsFilterValidator extends ListingValidator {

  /**
   * Método Construtor
   */
  public function __construct() {
    $filterRules = [
      new FilterFieldDefinition(
        field: 'animal.name',
        name: 'Nome do animal',
        description: 'Filtra pelo nome do animal',
        operators: ['=', '!=', 'LIKE'],
        booleanOperators: ['AND', 'OR'],
        valueType: 'string',
        acceptedValues: null
      )
    ];

    $orderRules = [
      new OrdenationFieldDefinition(
        field: 'id',
        name: 'ID do anúncio',
        description: 'Ordena os anúncios pelo ID do anúncio',
        directions: ['asc', 'desc']
      ),

      new OrdenationFieldDefinition(
        field: 'created_at',
        name: 'Data de criação',
        description: 'Ordena os anúncios por data de criação',
        directions: ['asc', 'desc']
      )
    ];

    parent::__construct($filterRules, $orderRules);
  }
}