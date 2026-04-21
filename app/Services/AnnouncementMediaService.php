<?php

namespace App\Services;

use App\DTO\AnnouncementMedia\AnnouncementMediaDTO;
use App\Exceptions\BusinessException;
use App\Models\AnnouncementMediaModel;
use App\Services\Interfaces\IAnnouncementMediaService;
use App\Utils\File;

/**
 * Serviço de gerenciamento de mídias de anúncios.
 * Fornece métodos para criar, editar, listar e remover mídias associadas a anúncios.
 */
class AnnouncementMediaService implements IAnnouncementMediaService {
  
  /**
   * Método Construtor
   * @param AnnouncementMediaModel $announcementMediaModel
   */
  public function __construct(
    private AnnouncementMediaModel $announcementMediaModel
  ) {}

  /**
   * Lista os registros com paginação.
   * @param  int $limit Número de registros por página.
   * @param  int $page  Número da página.
   * @return array      Lista de registros paginada.
   */
  public function getList(int $limit, int $page): array {
    return $this->announcementMediaModel->list($limit, $page);
  }

  /**
   * Obtém os IDs de todas as mídias associadas a um anúncio específico.
   * @param  int $announcementId ID do anúncio.
   * @return array               Lista de IDs das mídias associadas ao anúncio.
   */
  public function getAllMediaIds(int $announcementId): array {
    return $this->announcementMediaModel->where('announcement_id', $announcementId)->pluck('id')->toArray();
  }

  /**
   * Obtém um registro por ID.
   * @param  int   $id                                   ID do registro.
   * @param  array $relations                            Relacionamentos a serem carregados.
   * @return AnnouncementMediaDTO|AnnouncementMediaModel Objeto com os detalhes do registro.
   */
  public function getById(int $id, array $relations = [], bool $parse = true) :AnnouncementMediaDTO|AnnouncementMediaModel {
    return $this->announcementMediaModel->getById($id, $relations, $parse);
  }

  /**
   * Cria um novo registro.
   * @param  array $data                                 Dados do registro a ser criado.
   * @return AnnouncementMediaDTO|AnnouncementMediaModel Objeto com os detalhes do registro criado.
   */
  public function create(array $data) :AnnouncementMediaDTO|AnnouncementMediaModel {
    $data['url'] = $this->handleMedia($data);
    return $this->announcementMediaModel->create($data);
  }

  /**
   * Edita um registro existente.
   * @param  int    $id                                  ID do registro a ser editado.
   * @param  array  $data                                Dados atualizados do registro.
   * @return AnnouncementMediaDTO|AnnouncementMediaModel Objeto com os detalhes do registro atualizado.
   */
  public function edit(int $id, array $data) :AnnouncementMediaDTO|AnnouncementMediaModel {
    $data['url'] = $this->handleMedia($data, $id);
    return $this->announcementMediaModel->edit($id, $data);
  }

  /**
   * Método responsável por lidar com o upload e armazenamento da mídia, bem como a remoção da mídia antiga, se aplicável.
   * @param  array    $data       Dados relacionados à mídia, incluindo o arquivo, ID do usuário e ID do anúncio.
   * @param  int|null $resourceId ID do recurso existente para o qual a mídia está sendo editada (opcional).
   * @return string               Caminho da mídia salva.
   * @throws BusinessException    Exceção lançada se os dados necessários não forem fornecidos.
   */
  private function handleMedia(array $data, ?int $resourceId = null) :string {
    $userId         = $data['userId'];
    $announcementId = $data['announcementId'];
    
    if(!isset($data['file']) | !$userId | !$announcementId)
      throw new BusinessException("O arquivo, usuário e anúncio são obrigatórios para criar uma mídia de anúncio.", 400);

    if($resourceId) $this->removeOldMedia($resourceId);
    return (new File("user/{$userId}/announcement/{$announcementId}/media/"))->save($data['file'], width: 300, height: 300);
  }

  /**
   * Remove a mídia antiga associada a um registro, caso exista.
   * @param  int  $idMedia
   * @return void
   */
  private function removeOldMedia(int $idMedia) :void {
    $media = $this->getById($idMedia, relations: ['announcement']);

    if($media){
      $announcementId = $media->announcement->id;
      $userId         = $media->announcement->userId;

      (new File("user/{$userId}/announcement/{$announcementId}/media/"))->remove($media->url);
    }
  }

  /**
   * Remove um registro.
   * @param  ?int $id ID do registro a ser removido.
   * @return bool     Indica se a remoção foi bem-sucedida.
   */
  public function remove(?int $id = null) :bool {
    $this->removeOldMedia($id);
    return $this->announcementMediaModel->remove($id);
  }

}
