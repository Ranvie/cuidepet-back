<?php

namespace App\Classes\Validators;

use App\Classes\FilterFieldDefinition;
use App\Classes\OrdenationFieldDefinition;
use App\Classes\Validators\Base\ListingValidator;

/**
 * Classe de validação de filtros para consultas de anúncios públicos.
 */
class PublicAnnouncementFilterValidator extends ListingValidator {

  /**
   * Método Construtor
   */
  public function __construct() {
    $filterRules = [
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
        field: 'animal.gender',
        name: 'Gênero do animal',
        description: 'Filtra pelo gênero do animal',
        operators: ['=', '!=', 'IN', 'NOT IN'],
        booleanOperators: ['AND', 'OR'],
        valueType: 'string',
        acceptedValues: ['male', 'female']
      ),

      new FilterFieldDefinition(
        field: 'animal.disability',
        name: 'Animal com deficiência',
        description: 'Filtra se o animal possui alguma deficiência (1) ou não (0)',
        operators: ['=', '!='],
        booleanOperators: ['AND', 'OR'],
        valueType: 'boolean',
        acceptedValues: [0, 1]
      ),

      new FilterFieldDefinition(
        field: 'animal.vaccinated',
        name: 'Animal vacinado',
        description: 'Filtra se o animal está vacinado (1) ou não (0)',
        operators: ['=', '!='],
        booleanOperators: ['AND', 'OR'],
        valueType: 'boolean',
        acceptedValues: [0, 1]
      ),

      new FilterFieldDefinition(
        field: 'animal.dewormed',
        name: 'Animal desverminado',
        description: 'Filtra se o animal está desverminado (1) ou não (0)',
        operators: ['=', '!='],
        booleanOperators: ['AND', 'OR'],
        valueType: 'boolean',
        acceptedValues: [0, 1]
      ),

      new FilterFieldDefinition(
        field: 'animal.castrated',
        name: 'Animal castrado',
        description: 'Filtra se o animal está castrado (1) ou não (0)',
        operators: ['=', '!='],
        booleanOperators: ['AND', 'OR'],
        valueType: 'boolean',
        acceptedValues: [0, 1]
      ),

      new FilterFieldDefinition(
        field: 'animal.breed.name',
        name: 'Nome da raça',
        description: 'Filtra pelo nome da raça do animal',
        operators: ['=', '!=', 'LIKE', 'IN', 'NOT IN'],
        booleanOperators: ['AND', 'OR'],
        valueType: 'string',
        acceptedValues: null
      ),

      new FilterFieldDefinition(
        field: 'animal.breed.specie.name',
        name: 'Nome da espécie',
        description: 'Filtra pela espécie do animal (ex: Cachorro, Gato)',
        operators: ['=', '!=', 'LIKE', 'IN', 'NOT IN'],
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