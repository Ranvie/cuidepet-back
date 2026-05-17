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
      ),

      new FilterFieldDefinition(
        field: 'type',
        name: 'Tipo de anúncio',
        description: 'Permite filtrar os anúncios pelo tipo',
        operators: ['=', '!=', 'IN', 'NOT IN'],
        booleanOperators: ['AND', 'OR'],
        valueType: 'string',
        acceptedValues: ['donation', 'lost']
      ),

      new FilterFieldDefinition(
        field: 'status',
        name: 'Status do anúncio',
        description: 'Filtra os anúncios com base em se estão em aberto (1) ou fechados (0).',
        operators: ['=', '!=', 'IN', 'NOT IN'],
        booleanOperators: ['AND', 'OR'],
        valueType: 'boolean',
        acceptedValues: [0, 1]
      ),

      new FilterFieldDefinition(
        field: 'created_at',
        name: 'Data de criação',
        description: 'Permite filtrar os anúncios por data de criação',
        operators: ['=', '!=', '>', '<', '>=', '<='],
        booleanOperators: ['AND', 'OR'],
        valueType: 'date',
        acceptedValues: 'YYYY-MM-DD ou YYYY-MM-DDTH:i:sZ'
      ),
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