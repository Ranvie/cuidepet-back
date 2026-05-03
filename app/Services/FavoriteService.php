<?php

namespace App\Services;

use App\Classes\Filter;
use App\Exceptions\BusinessException;
use App\Models\FavoriteModel;
use App\Services\Interfaces\IFavoriteService;

/**
 * Serviço de gerenciamento de favoritos.
 * Fornece métodos para listar, adicionar e remover favoritos de anúncios por parte dos usuários.
 */
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
   * @param int      $limit   Limite máximo de registros
   * @param int      $page    Página atual da lista
   * @param int|null $userId  ID do usuário cujos favoritos serão listados
   * @param array    $filters Lista de filtros aplicados à listagem
   * @param array    $orders  Lista de ordenação aplicados à listagem
   * @return array            Lista de anúncios favoritados
   */
  public function listFavorites(int $limit, int $page, ?int $userId, array $filters = [], array $orders = []) :array {
    return $this->obPublicAnnouncementService->getList($limit, $page, $userId, $filters, $orders);
  }

  /**
   * Responsável por possibilitar um usuário favoritar um anúncio
   * @param int $userId         ID do usuário que deseja favoritar um anúncio
   * @param int $announcementId ID do anúncio que deve ser favoritado
   * @return bool               Valor booleano indicando se o anúncio foi favoritado
   */
  public function addFavorite(int $userId, int $announcementId) :bool {
    $obAnnouncementDTO = $this->obPublicAnnouncementService->getById($announcementId);

    $favorited = $this->obFavoriteModel->getByQuery([
      new Filter('user_id', '=', $userId),
      new Filter('announcement_id', '=', $announcementId),
    ], parse: false);

    if($obAnnouncementDTO->userId === $userId)
      throw new BusinessException('Não é possível favoritar seu próprio anúncio.', 400);

    if(!$favorited instanceof FavoriteModel)
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
