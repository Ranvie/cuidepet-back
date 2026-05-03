<?php

namespace App\Http\Controllers;

use App\Classes\Filter;
use App\Classes\Validators\PublicAnnouncementFilterValidator;
use App\Http\Requests\ListingRequest;
use App\Http\Response\BusinessResponse;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;

/**
 * Controlador responsável por gerenciar as operações relacionadas aos favoritos dos usuários.
 * Este controlador lida com a listagem, adição e remoção de anúncios favoritos para os usuários autenticados.
 */
class FavoriteController {

  /**
   * Método Construtor
   * @param FavoriteService $obFavoriteService Serviço de favoritos para processamento
   */
  public function __construct(
    private FavoriteService $obFavoriteService
  ){}

  /**
   * Método responsável por listar anúncios favoritados do usuário
   * @param  ListingRequest $request Corpo de listagem para filtros, ordenação e paginação
   * @return JsonResponse            Resposta JSON contendo a lista de anúncios públicos por favoritos
   */
  public function list(ListingRequest $request) :JsonResponse {
    $validated         = $request->validated();
    $preDefinedFilters = $this->getAnnouncementFilters();

    [$filters, $orders] = (new PublicAnnouncementFilterValidator())->build($request);
    $filters            = array_merge($filters, $preDefinedFilters);
    $userId             = auth()->id() ?? null;
    $registers          = $this->obFavoriteService->listFavorites($validated['limit'], $validated['page'], $userId, $filters, $orders);

    return new BusinessResponse(200, $registers)->build();
  }

  /**
   * Obtém regras pré definidas de filtros os favoritos.
   * @return Filter[] Array de objetos Filter contendo as regras pré definidas para listagem de favoritos
   */
  private function getAnnouncementFilters() :array {
    return [
      new Filter('active', '=', '1', 'AND'),
      new Filter('blocked', '=', '0', 'AND'),
      new Filter('favorites.user_id', '=', auth()->id(), 'AND'),
    ];
  }

  /**
   * Favorita um anúncio
   * @param  int $announcementId ID do anúncio a ser favoritado
   * @return JsonResponse        Resposta JSON para cadastro de favoritos
   */
  public function create(int $announcementId) :JsonResponse {
    $this->obFavoriteService->addFavorite(auth()->id(), $announcementId);

    return new BusinessResponse(200, "Anúncio favoritado com sucesso!")->build();
  }

  /**
   * Remove um anúncio dos favoritos
   * @param  int $announcementId ID do anúncio a ser removido dos favoritos
   * @return JsonResponse        Resposta JSON para exclusão de favoritos
   */
  public function delete(int $announcementId) :JsonResponse {
    $this->obFavoriteService->removeFavorite(auth()->id(), $announcementId);

    return new BusinessResponse(200, "Favorito removido com sucesso!")->build();
  }
}
