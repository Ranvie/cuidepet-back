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
    private AnnouncementModel $obPublicAnnouncementModel,
  ) {}

  /**
   * Lista os anúncios públicos.
   * @param  int $limit   Quantidade de anúncios por página.
   * @param  int $page    Número da página.
   * @param  string $type Tipo de anúncio.
   * @return array        Lista de anúncios públicos.
   */
  public function getList(int $limit, int $page, string $type) :array {
    $this->validateAnnouncementTypeExists($type);
    return $this->obPublicAnnouncementModel->list($limit, $page, relations: ['user', 'animal'], filters: [new Filter('type', '=', $type)]);
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
   * Validação do tipo do anúncio (Perdido ou Doação)
   * @param string $type
   * @throws BusinessException
   * @return void
   */
  private function validateAnnouncementTypeExists(string $type) :void {
    if (!AnnouncementTypes::tryFrom($type))
      throw new BusinessException('Atualmente, as únicas opções de listagem de anúncios são: perdidos (lost) ou doações (donations).', 404);
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
