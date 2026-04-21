<?php

namespace App\Services;

use App\Classes\Filter;
use App\Models\FavoriteModel;
use App\Services\Interfaces\IFavoriteService;

class FavoriteService implements IFavoriteService {

  /**
   * Método Construtor
   * @param FavoriteModel             $obFavoriteModel
   * @param PublicAnnouncementService $obPublicAnnouncementService
   */
  public function __construct(
    private FavoriteModel             $obFavoriteModel,
    private PublicAnnouncementService $obPublicAnnouncementService
  ) {}
  
  /**
   * Responsável por listar todos os anúncios favoritos do usuário
   * @param int    $limit   Limite máximo de registros
   * @param int    $page    Página atual da lista
   * @param array  $filters Lista de filtros aplicados à listagem
   * @param array  $orders  Lista de ordenação aplicados à listagem
   * @return array          Lista de anúncios favoritados
   */
  public function listFavorites(int $limit, int $page, array $filters = [], array $orders = []) :array {
    return $this->obPublicAnnouncementService->getList($limit, $page, filters: $filters, orders: $orders);
  }

  /**
   * Responsável por possibilitar um usuário favoritar um anúncio
   * @param int $userId         ID do usuário que deseja favoritar um anúncio
   * @param int $announcementId ID do anúncio que deve ser favoritado
   * @return bool               Valor booleano indicando se o anúncio foi favoritado
   */
  public function addFavorite(int $userId, int $announcementId) :bool {
    $this->obFavoriteModel->create(['user_id' => $userId, 'announcement_id' => $announcementId], parse: false);

    return true;
  }

  /**
   * Responsável por remover um favorito do usuário
   * @param  int $userId         ID do usuário que deseja desfavoritar um anúncio
   * @param  int $announcementId ID do anúncio que deve ser desfavoritado
   * @return bool                Valor booleano indicando se o anúncio foi desfavoritado
   */
  public function removeFavorite(int $userId, int $announcementId) :bool {
    return $this->obFavoriteModel->getByQuery([
      new Filter('user_id', '=', $userId),
      new Filter('announcement_id', '=', $announcementId)
    ], parse: false)?->delete() ?? false;
  }
  
}
