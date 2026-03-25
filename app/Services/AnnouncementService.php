<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\Announcement\AnnouncementDTO;
use App\Exceptions\BusinessException;
use App\Models\AnnouncementModel;
use App\Services\AddressService;
use App\Services\Interfaces\IAnnouncementService;

class AnnouncementService implements IAnnouncementService {

  /**
   * Construtor do serviço de anúncios.
   * @param AnnouncementModel        $obAnnouncementModel      Modelo de anúncio.
   * @param UserService              $userService              Serviço de usuários.
   * @param AnnouncementMediaService $announcementMediaService Serviço de mídia de anúncios.
   * @param FormService              $formService              Serviço de formulários.
   * @param AddressService           $addressService           Serviço de endereços.
   */
  public function __construct(
    private AnnouncementModel $obAnnouncementModel,
    private UserService $userService,
    private AnnouncementMediaService $announcementMediaService,
    private FormService $formService,
    private AddressService $addressService
  ) {}

  /**
   * Lista os anúncios com paginação.
   * @param  int $limit Número de anúncios por página.
   * @param  int $page  Número da página.
   * @return array      Lista de anúncios paginada.
   */
  public function getList(int $limit, int $page) :array {
    return $this->obAnnouncementModel->list($limit, $page);
  }

  /**
   * Lista os anúncios de um usuário específico com paginação.
   * @param  int $limit  Número de anúncios por página.
   * @param  int $page   Número da página.
   * @param  int $idUser ID do usuário.
   * @return array       Lista de anúncios do usuário paginada.
   */
  public function getListByUser(int $limit, int $page, int $idUser) :array {
    return $this->obAnnouncementModel->list($limit, $page, relations: ['animal'], filters: [new Filter('user_id', '=', $idUser)]);
  }

  /**
   * Obtém um anúncio por ID.
   * @param  int    $id        ID do anúncio.
   * @param  array  $relations Relações a serem carregadas com o anúncio.
   * @return AnnouncementDTO   Objeto de transferência de dados do anúncio.
   */
  public function getById(int $id, array $relations = ['animal.breed', 'animal.specie', 'form', 'announcementMedia']) :AnnouncementDTO {
    $obAnnouncementDTO = $this->obAnnouncementModel->getById($id, $relations, true);

    $this->validateAnnouncementExists($obAnnouncementDTO);
    return $obAnnouncementDTO;
  }

  /**
   * Obtém um anúncio específico de um usuário.
   * @param  int $userId            ID do usuário.
   * @param  int $announcementId    ID do anúncio.
   * @return AnnouncementModel|null Objeto de modelo de dados do anúncio, ou null se não encontrado.
   */
  public function getUserAnnouncement(int $userId, int $announcementId) :?AnnouncementModel {
    return $this->obAnnouncementModel->getUserAnnouncement($userId, $announcementId);
  }

  /**
   * Cria um novo anúncio.
   * @param  array $data     Dados do anúncio a ser criado.
   * @return AnnouncementDTO Objeto de transferência de dados do anúncio criado.
   */
  public function create(array $data) :AnnouncementDTO {
    $this->validateIfUserExists($data['userId']);
    $this->validateIfFormBelongsToUser($data['userId'], $data['formId']);

    $addressData       = $data['address'];
    $address           = $this->addressService->create($addressData);
    $data['addressId'] = $address->id;

    $announcementModel = $this->obAnnouncementModel->create($data, [], false);
    $announcementId    = $announcementModel->getOriginal()['id'];

    $animalData                   = $data['animal'];
    $animalData['announcementId'] = $announcementId;
    $announcementModel->animal()->getModel()->create($animalData, [], false);

    $announcementModel->form()->associate($data['formId']);

    $announcementMediaData = $data['announcementMedia'];
    foreach ($announcementMediaData as $announcementMedia) {
      $announcementMedia['announcementId'] = $announcementId;
      $this->announcementMediaService->newInstance()->create($announcementMedia);
    }

    return $announcementModel->getById($announcementId, ['animal.breed', 'animal.breed.specie', 'form', 'announcementMedia', 'address', 'address.cacheAddress']);
  }

  /**
   * Valida se um usuário existe.
   * @param  int  $userId      ID do usuário a ser verificado.
   * @return void
   * @throws BusinessException Se o usuário não for encontrado.
   */
  private function validateIfUserExists(int $userId) :void {
    $this->userService->getById($userId);
  }

  /**
   * Valida se um formulário pertence a um usuário.
   * @param  int  $userId      ID do usuário.
   * @param  int  $formId      ID do formulário a ser verificado.
   * @return void
   * @throws BusinessException Se o formulário não pertencer ao usuário.
   */
  private function validateIfFormBelongsToUser(int $userId, int $formId) :void {
    $userForm = $this->formService->getUserForm($userId, $formId);

    if (!$userForm)
      throw new BusinessException('O usuário não possui o formulário requisitado.', 404);
  }

  /**
   * Edita um anúncio existente.
   * @param  int   $id       ID do anúncio a ser editado.
   * @param  array $data     Dados do anúncio a ser editado.
   * @return AnnouncementDTO Objeto de transferência de dados do anúncio editado.
   */
  public function edit(int $id, array $data) :AnnouncementDTO {
    $this->validateIfUserExists($data['userId']);
    $this->validateIfAnnouncementBelongsToUser($data['userId'], $id);
    if (isset($data['formId'])) $this->validateIfFormBelongsToUser($data['userId'], $data['formId']);

    $announcementModel = $this->obAnnouncementModel->edit($id, $data, true, false);

    if (isset($data['animal'])) {
      $animalData = $data['animal'];
      $announcementModel->animal()->getModel()->edit($id, $animalData);
    }

    if (isset($data['formId'])) $announcementModel->form()->associate($data['formId']);

    if (isset($data['announcementMedia'])) {
      $announcementMediaData = $data['announcementMedia'];
      foreach ($announcementMediaData as $announcementMedia) {
        $announcementMedia['announcementId'] = $id;
        $this->changeMediaData($announcementMedia);
      }
    }

    return $announcementModel->getById($id, ['animal.breed', 'animal.specie', 'form', 'announcementMedia']);
  }

  /**
   * Valida se um anúncio pertence a um usuário.
   * @param  int $userId         ID do usuário.
   * @param  int $announcementId ID do anúncio a ser verificado.
   * @return void
   * @throws BusinessException   Se o anúncio não pertencer ao usuário.
   */
  private function validateIfAnnouncementBelongsToUser(int $userId, int $announcementId) :void {
    $announcement = $this->getUserAnnouncement($userId, $announcementId);

    if (!$announcement instanceof AnnouncementModel)
      throw new BusinessException('O anúncio requisitado não pertence ao usuário', 404);
  }

  /**
   * Altera os dados de uma mídia de anúncio com base na ação especificada.
   * @param  array $announcementMedia Dados da mídia do anúncio, incluindo a ação a ser realizada.
   * @return void
   */
  private function changeMediaData(array $announcementMedia) :void {
    $mediaId = $announcementMedia['id'];
    $option  = $announcementMedia['action'];

    match ($option) {
      'UPD'   => $this->announcementMediaService->newInstance()->edit($mediaId, $announcementMedia),
      'DEL'   => $this->announcementMediaService->remove($mediaId),
      'ADD'   => $this->announcementMediaService->newInstance()->create($announcementMedia),
      default => null,
    };
  }

  /**
   * Remove um anúncio.
   * @param  int|null $id ID do anúncio a ser removido.
   * @return bool         Indica se a remoção foi bem-sucedida.
   */
  public function remove(?int $id = null): bool {
    return $this->obAnnouncementModel->remove($id);
  }

  /**
   * Valida se um anúncio existe.
   * @param  AnnouncementDTO|null $obAnnouncement Objeto de transferência de dados do anúncio a ser verificado.
   * @return void
   * @throws BusinessException Se o anúncio não for encontrado.
   */
  private function validateAnnouncementExists(?AnnouncementDTO $obAnnouncement) :void {
    if (!$obAnnouncement instanceof AnnouncementDTO)
      throw new BusinessException('O anúncio não foi encontrado', 404);
  }
}
