<?php

namespace App\Services;

use App\DTO\Announcement\AnnouncementDTO;
use App\Exceptions\BusinessException;
use App\Models\AnnouncementModel;
use App\Services\Interfaces\IPublicAnnouncementService;

class PublicAnnouncementService implements IPublicAnnouncementService {

  /**
   * Método Construtor
   * @param AnnouncementModel $obPublicAnnouncementModel
   */
  public function __construct(
    private AnnouncementModel $obPublicAnnouncementModel
  ) {}

  /**
   * Lista os anúncios públicos.
   * @param  int      $limit   Quantidade de anúncios por página.
   * @param  int      $page    Número da página.
   * @param  null|int $userId  ID do usuário que solicitou a lista
   * @param  array    $filters Filtros para a consulta dos anúncios.
   * @param  array    $orders  Ordenações para a consulta dos anúncios.
   * @throws BusinessException Caso o tipo de anúncio seja inválido.
   * @return array             Lista de anúncios públicos.
   */
  public function getList(int $limit, int $page, ?int $userId = null, array $filters = [], array $orders = []) :array {
    $listRelations          = ['user', 'animal', 'animal.breed', 'animal.breed.specie', 'favorites', 'address.cacheAddress'];
    $publicAnnouncementList = $this->obPublicAnnouncementModel->list($limit, $page, relations: $listRelations, filters: $filters, orders: $orders);

    array_map(function($obAnnouncementDTO) use($userId){
      $this->applyFavorites($obAnnouncementDTO, $userId);
    }, $publicAnnouncementList['registers']);

    return $publicAnnouncementList;
  }

  /**
   * Obtém um anúncio público por ID.
   * @param  int $id              ID do anúncio.
   * @param  null|int $userId     ID do usuário que solicitou a lista
   * @param  array $relations     Relações a serem carregadas junto com o anúncio.
   * @return AnnouncementDTO|null Anúncio público ou null se não encontrado.
   */
  public function getById(int $id, ?int $userId = null, array $relations = ['user', 'animal.breed', 'animal.breed.specie', 'form', 'announcementMedia', 'favorites', 'address.cacheAddress']): ?AnnouncementDTO {
    $obAnnouncementDTO = $this->obPublicAnnouncementModel->getById($id, $relations, true);
    $this->validateAnnouncementExists($obAnnouncementDTO);

    $this->applyFavorites($obAnnouncementDTO, $userId);
    return $obAnnouncementDTO;
  }

  /**
   * Manipula objeto de anúncio publico para inserir status de favoritos
   * @param  AnnouncementDTO $obPublicAnnouncementDTO Objeto DTO do anúncio para inserir os favoritos
   * @param  int|null        $userId                  ID do usuário que deve ser validado status de favorito
   * @return void
   */
  public function applyFavorites(AnnouncementDTO $obPublicAnnouncementDTO, ?int $userId) :void {
    $obPublicAnnouncementDTO->favoritesCount = $obPublicAnnouncementDTO->favorites->count() ?? 0;
    
    $obPublicAnnouncementDTO->isFavorited = $userId === null
      ? $obPublicAnnouncementDTO->favorites->where('userId', $userId)->count() > 0
      : false;
      
    unset($obPublicAnnouncementDTO->favorites);
  }

  /**
   * Valida objeto de anúncio 
   * @param  AnnouncementDTO|AnnouncementModel|null $obAnnouncement
   * @throws BusinessException
   * @return void
   */
  private function validateAnnouncementExists(AnnouncementDTO|AnnouncementModel|null $obAnnouncement) :void {
    if (!$obAnnouncement)
      throw new BusinessException('O anúncio não foi encontrado', 404);
  }
}
