<?php

namespace App\Classes\Validators\Base;

use App\Classes\Filter;
use App\Classes\FilterFieldDefinition;
use App\Classes\OrdenationFieldDefinition;
use App\Classes\Ordenation;
use App\Http\Requests\ListingRequest;

/**
 * Classe de validação que engloba filtros E ordenações.
 * Esta é a classe que deve ser estendida quando você precisa de ambos.
 */
abstract class ListingValidator {

  /**
   * Validador de filtros.
   * @var FilterValidator
   */
  protected FilterValidator $filterValidator;

  /**
   * Builder de regras de ordenação.
   * @var OrdenationBuilderRules|null
   */
  protected ?OrdenationBuilderRules $ordenationBuilderRules = null;

  /** 
   * Definições de campos filtráveis.
   * @var FilterFieldDefinition[]
   */
  protected array $filterRules = [];

  /** 
   * Definições de campos ordenáveis.
   * @var OrdenationFieldDefinition[]
   */
  protected array $ordenationRules = [];

  /**
   * Método Construtor
   * @param FilterFieldDefinition[]     $filterRules     Regras de filtragem
   * @param OrdenationFieldDefinition[] $ordenationRules Regras de ordenação
   */
  public function __construct(array $filterRules = [], array $ordenationRules = []) {
    $this->filterRules     = $filterRules;
    $this->ordenationRules = $ordenationRules;

    $this->filterValidator = new class($filterRules) extends FilterValidator {};

    if (!empty($ordenationRules))
      $this->ordenationBuilderRules = new OrdenationBuilderRules($ordenationRules);
  }

  /**
   * Constrói filtros e ordenações a partir de uma requisição de listagem.
   * @param  ListingRequest $request Requisição contendo filtros e ordenações.
   * @return array [Filter[], Ordenation[]]
   */
  public function build(ListingRequest $request) :array {
    $filters = $this->buildFilters($request);
    $orders  = $this->buildOrdenations($request);

    return [$filters, $orders];
  }

  /**
   * Constrói os filtros a partir de uma requisição.
   * @param  ListingRequest $request
   * @return Filter[]
   */
  protected function buildFilters(ListingRequest $request) :array {
    return $this->filterValidator->build($request);
  }

  /**
   * Constrói as ordenações a partir de uma requisição.
   * @param  ListingRequest $request
   * @return Ordenation[]
   */
  protected function buildOrdenations(ListingRequest $request) :array {
    if (empty($this->ordenationBuilderRules))
      return [];

    $validated = $request->validated();
    $orders    = $validated['orders'] ?? [];

    foreach($orders as $order) {
      $obOrdenation = new Ordenation(
        field:     $order['field']     ?? '',
        direction: $order['direction'] ?? 'asc'
      );

      $this->ordenationBuilderRules->addOrdenation($obOrdenation);
    }

    return $this->ordenationBuilderRules->build();
  }

  /**
   * Retorna as definições dos campos filtráveis (para API discovery).
   * @return array
   */
  public function getFieldDefinitions(): array {
    return $this->filterValidator->getFieldDefinitions();
  }

  /**
   * Retorna as definições dos campos ordenáveis (para API discovery).
   * @return array
   */
  public function getOrdenationDefinitions(): array {
    if (!$this->ordenationBuilderRules)
      return [];

    return $this->ordenationBuilderRules->getFieldDefinitionsArray();
  }

}
