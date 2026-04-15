<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Http\Requests\ListingRequest;
use App\Http\Response\BusinessResponse;
use App\Models\BusinessModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Bus;

/**
 * Controller para descoberta de filtros disponíveis por rota.
 */
class FilterDiscoveryController extends Controller {

  /**
   * Mapeamento de rotas para seus respectivos validators.
   * @var array<string, string>
   */
  protected array $routeValidators = [
    'announcements' => \App\Classes\Validators\PublicAnnouncementFilterValidator::class
  ];

  /**
   * Retorna os filtros disponíveis para uma rota específica.
   * @param  Request $request
   * @return JsonResponse
   */
  public function get(Request $request): JsonResponse {
    $route = $request->query('route');
    $route = preg_replace('/^\/?api\//', '', $route);

    if (!$route)
      throw new BusinessException('O parâmetro "route" é obrigatório. Exemplo: /api/filters?route=announcements', 400);

    $route = ltrim($route, '/');

    if (!isset($this->routeValidators[$route]))
      throw new BusinessException('Não existem filtros para a rota informada.', 404);

    $validatorClass = $this->routeValidators[$route];
    $validator      = new $validatorClass();
    
    // Obtém tanto filtros quanto ordenações (se o validator suportar)
    $filterDefinitions = method_exists($validator, 'getFieldDefinitions') 
      ? $validator->getFieldDefinitions() 
      : [];
    
    $ordenationDefinitions = method_exists($validator, 'getOrdenationDefinitions')
      ? $validator->getOrdenationDefinitions()
      : [];

    $response = [
      'route'   => $route,
      'filters' => $filterDefinitions,
      'orders'  => $ordenationDefinitions
    ];

    return (new BusinessResponse(200, $response))->build();
  }

  /**
   * Lista todas as rotas com filtros disponíveis.
   * @param  ListingRequest $request
   * @return JsonResponse
   */
  public function list(ListingRequest $request): JsonResponse {
    $routes = [];
    $page   = $request->query('page');
    $limit  = $request->query('limit');

    foreach ($this->routeValidators as $route => $validatorClass) {
      $validator   = new $validatorClass();
      
      $filterDefinitions = method_exists($validator, 'getFieldDefinitions') 
        ? $validator->getFieldDefinitions() 
        : [];
      
      $ordenationDefinitions = method_exists($validator, 'getOrdenationDefinitions')
        ? $validator->getOrdenationDefinitions()
        : [];

      $routes[] = [
        'route'           => "api/$route",
        'filterCount'     => \count($filterDefinitions),
        'ordenationCount' => \count($ordenationDefinitions)
      ];
    }

    $totalRoutes = \count($routes);
    $routes      = \array_slice($routes, ($page - 1) * $limit, $limit);

    $response = BusinessModel::getListResponse(
      $routes,
      $limit,
      $page,
      ceil($totalRoutes / $limit),
      $totalRoutes,
      BusinessModel::MAX_ITEMS_PER_PAGE
    );

    return (new BusinessResponse(200, $response))->build();
  }
}
