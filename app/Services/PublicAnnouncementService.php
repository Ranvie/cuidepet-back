<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\Announcement\AnnouncementDTO;
use App\Exceptions\BusinessException;
use App\Http\Enums\AnnouncementTypes;
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
   * @param  int $limit        Quantidade de anúncios por página.
   * @param  int $page         Número da página.
   * @param  array $filters    Filtros para a consulta dos anúncios.
   * @throws BusinessException Caso o tipo de anúncio seja inválido.
   * @return array             Lista de anúncios públicos.
   */
  public function getList(int $limit, int $page, array $filters = [], array $orders = []) :array {
    return $this->obPublicAnnouncementModel->list($limit, $page, relations: ['user', 'animal', 'animal.breed', 'animal.breed.specie'], filters: $filters, orders: $orders);
  }

  /**
   * Obtém um anúncio público por ID.
   * @param  int $id              ID do anúncio.
   * @param  array $relations     Relações a serem carregadas junto com o anúncio.
   * @return AnnouncementDTO|null Anúncio público ou null se não encontrado.
   */
  public function getById(int $id, array $relations = ['user', 'animal.breed', 'animal.breed.specie', 'form', 'announcementMedia']): ?AnnouncementDTO {
    $obAnnouncementDTO = $this->obPublicAnnouncementModel->getById($id, $relations, true);

    $this->validateAnnouncementExists($obAnnouncementDTO);
    return $obAnnouncementDTO;
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
