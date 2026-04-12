<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\Announcement\AnnouncementDTO;
use App\DTO\Form\FormDTO;
use App\Exceptions\BusinessException;
use App\Http\Response\BusinessResponse;
use App\Models\AnnouncementModel;
use App\Services\AddressService;
use App\Services\Interfaces\IAnnouncementService;
use App\Utils\File;

/**
 * Serviço de gerenciamento de anúncios.
 * Fornece métodos para criar, editar, listar e remover anúncios, além de validações relacionadas a anúncios.
 */
class AnnouncementService implements IAnnouncementService {

  /**
   * Construtor do serviço de anúncios.
   * @param AnnouncementModel        $obAnnouncementModel        Modelo de anúncio.
   * @param UserService              $obUserService              Serviço de usuários.
   * @param AnnouncementMediaService $obAnnouncementMediaService Serviço de mídia de anúncios.
   * @param FormService              $obFormService              Serviço de formulários.
   * @param AddressService           $obAddressService           Serviço de endereços.
   * @param AnimalService            $obAnimalService            Serviço de animais.
   */
  public function __construct(
    private AnnouncementModel        $obAnnouncementModel,
    private UserService              $obUserService,
    private AnnouncementMediaService $obAnnouncementMediaService,
    private FormService              $obFormService,
    private AddressService           $obAddressService,
    private AnimalService            $obAnimalService
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
   * @param  int $userId ID do usuário.
   * @return array       Lista de anúncios do usuário paginada.
   */
  public function getListByUser(int $limit, int $page, int $userId) :array {
    return $this->obAnnouncementModel->list($limit, $page, relations: ['animal'], filters: [new Filter('user_id', '=', $userId)]);
  }

  /**
   * Busca um anúncio específico por ID.
   * @param  int $id     ID do anúncio.
   * @return AnnouncementDTO Objeto de transferência de dados do anúncio.
   */
  public function getById(int $id, array $relations = ['animal.breed', 'animal.breed.specie', 'form', 'announcementMedia', 'address', 'address.cacheAddress']) :AnnouncementDTO {
    $obAnnouncementDTO = $this->obAnnouncementModel->getById($id, $relations);

    return $obAnnouncementDTO;
  }

  /**
   * Obtém um anúncio por ID.
   * @param  int    $id        ID do anúncio.
   * @param  array  $relations Relações a serem carregadas com o anúncio.
   * @return AnnouncementDTO   Objeto de transferência de dados do anúncio.
   * @throws BusinessException Se o anúncio não for encontrado.
   */
  public function getUserAnnouncement(int $id, int $userId, array $relations = ['animal.breed', 'animal.breed.specie', 'form', 'announcementMedia', 'address', 'address.cacheAddress']) :AnnouncementDTO {
    $obAnnouncementDTO = $this->obAnnouncementModel->getByQuery([new Filter('id', '=', $id), new Filter('user_id', '=', $userId)], $relations);

    $this->validateAnnouncementExists($obAnnouncementDTO);
    return $obAnnouncementDTO;
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
    $address           = $this->obAddressService->create($addressData);
    $data['addressId'] = $address->id;

    $mainImage = $data['mainImage'] ?? null;
    unset($data['mainImage']);
    
    $announcementModel = $this->obAnnouncementModel->create($data, [], false);
    $announcementId    = $announcementModel->getOriginal()['id'];

    if(isset($mainImage)) {
      $mainImagePath = (new File("user/{$data['userId']}/announcement/{$announcementId}/media/"))->save($mainImage, width: 1200, height: 700);
      $announcementModel->update(['main_image' => $mainImagePath]);
    }

    $animalData                   = $data['animal'];
    $animalData['announcementId'] = $announcementId;
    $animalData['userId']         = $data['userId'];
    $this->obAnimalService->create($animalData);

    $announcementModel->form()->associate($data['formId']);

    $announcementMediaData = $data['announcementMedia'];
    foreach ($announcementMediaData as $announcementMedia) {
      $announcementMedia['announcementId'] = $announcementId;
      $announcementMedia['userId']         = $data['userId'];
      $this->obAnnouncementMediaService->create($announcementMedia);
    }

    return $this->getById($announcementId, ['animal.breed', 'animal.breed.specie', 'form', 'announcementMedia', 'address', 'address.cacheAddress']);
  }

  /**
   * Valida se um usuário existe.
   * @param  int  $userId      ID do usuário a ser verificado.
   * @return void
   * @throws BusinessException Se o usuário não for encontrado.
   */
  private function validateIfUserExists(int $userId) :void {
    $this->obUserService->getById($userId);
  }

  /**
   * Valida se um formulário pertence a um usuário.
   * @param  int  $userId      ID do usuário.
   * @param  int  $formId      ID do formulário a ser verificado.
   * @return void
   * @throws BusinessException Se o formulário não pertencer ao usuário.
   */
  private function validateIfFormBelongsToUser(int $userId, int $formId) :void {
    $userForm = $this->obFormService->getUserFormById($formId, $userId);

    if (!$userForm instanceof FormDTO)
      throw new BusinessException("O formulário de ID $formId não foi encontrado.", 404);
  }

  /**
   * Edita um anúncio existente.
   * @param  int   $id       ID do anúncio a ser editado.
   * @param  array $data     Dados do anúncio a ser editado.
   * @return AnnouncementDTO Objeto de transferência de dados do anúncio editado.
   */
  public function edit(int $id, array $data) :AnnouncementDTO {
    $this->validateIfUserExists($data['userId']);
    
    if(isset($data['formId']))  
      $this->validateIfFormBelongsToUser($data['userId'], $data['formId']);

    if(isset($data['mainImage'])) {
      $obFile            = (new File("user/{$data['userId']}/announcement/{$id}/media/"));
      $obAnnouncementDTO = $this->getById($id, ['announcementMedia']);
      $obFile->remove($obAnnouncementDTO->mainImage);
      
      $data['mainImage'] = $obFile->save($data['mainImage'], width: 1200, height: 700);
    }

    $announcementModel = $this->obAnnouncementModel->edit($id, $data, true, false);

    if(isset($data['animal'])) {
      $data['animal']['userId']         = $data['userId'];
      $data['animal']['announcementId'] = $id;
      $this->obAnimalService->edit($id, $data['animal']);
    }

    if(isset($data['formId'])) 
      $announcementModel->form()->associate($data['formId']);

    if(isset($data['address']))
      $this->obAddressService->edit($announcementModel->address->id, $data['address']);

    if (isset($data['announcementMedia'])) {
      $announcementMediaData = $data['announcementMedia'];
      $announcementMediaIds  = $this->obAnnouncementMediaService->getAllMediaIds($id);
      $errors = [];
      foreach ($announcementMediaData as $announcementMedia) {
        if($announcementMedia['action'] === 'ADD' && !$this->validateMediaLimit(\count($announcementMediaIds))){
          $errors[] = "O anúncio não pode conter mais do que 4 mídias.";
          break;
        }

        if($announcementMedia['action'] !== 'ADD' && !$this->validateIfMediaBelongsToAnnouncement($announcementMedia['id'] ?? null, $announcementMediaIds, $errors))
          continue;

        $announcementMedia['announcementId'] = $id;
        $announcementMedia['userId']         = $data['userId'];
        $this->changeMediaData($announcementMedia);
      }

      if(\count($errors) > 0)
        BusinessResponse::addErrors($errors);
    }

    return $this->getById($id, ['animal.breed', 'animal.breed.specie', 'form', 'announcementMedia', 'address', 'address.cacheAddress']);
  }

  /**
   * Valida se uma mídia de anúncio pertence a um anúncio específico.
   * @param  int|null $mediaId              ID da mídia do anúncio a ser verificada.
   * @param  array    $announcementMediaIds Lista de IDs das mídias associadas ao anúncio.
   * @return string                         Mensagem de erro, ou string vazia se a mídia for válida.
   */
  private function validateIfMediaBelongsToAnnouncement(?int $mediaId, array $announcementMediaIds, array &$errors = []) :string {
    if (!$mediaId) 
      return true;

    if (!\in_array($mediaId, $announcementMediaIds)) {
      $errors[] = "A mídia de ID {$mediaId} não foi encontrada.";
      return false;
    }

    return true;
  }

  /**
   * Valida se o limite de mídias associadas a um anúncio foi atingido.
   * @param  int $currentMediaCount Quantidade atual de mídias associadas ao anúncio.
   * @return bool                   Indica se o limite de mídias foi atingido ou não.
   */
  private function validateMediaLimit(int $currentMediaCount) :bool {
    if($currentMediaCount > 4)
      return false;

    return true;
  }

  /**
   * Altera os dados de uma mídia de anúncio com base na ação especificada.
   * @param  array $announcementMedia Dados da mídia do anúncio, incluindo a ação a ser realizada.
   * @return void
   */
  private function changeMediaData(array $announcementMedia) :void {
    $mediaId = $announcementMedia['id'] ?? null;
    $option  = $announcementMedia['action'];

    match ($option) {
      'UPD'   => $this->obAnnouncementMediaService->edit($mediaId, $announcementMedia),
      'DEL'   => $this->obAnnouncementMediaService->remove($mediaId),
      'ADD'   => $this->obAnnouncementMediaService->create($announcementMedia),
      default => null,
    };
  }

  /**
   * Remove um anúncio.
   * @param  int|null $id ID do anúncio a ser removido.
   * @return bool         Indica se a remoção foi bem-sucedida.
   */
  public function remove(?int $id = null): bool {
    $obAnnouncement = $this->obAnnouncementModel->getById($id);
    $userId         = $obAnnouncement->userId;

    (new File("user/$userId/announcement/{$id}/"))->removeAll();
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
