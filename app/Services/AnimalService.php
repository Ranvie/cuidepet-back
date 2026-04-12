<?php

namespace App\Services;

use App\DTO\Animal\AnimalDTO;
use App\Models\AnimalModel;
use App\Utils\File;

/**
 * Serviço para manipulação de dados relacionados a animais vinculados aos anúncios.
 * Fornece métodos para criar e editar animais, bem como lidar com o upload e remoção de mídia associada.
 */
class AnimalService {
  
  /**
   * Construtor da classe AnimalService.
   * @param AnimalModel $obAnimalModel Modelo de animal para interagir com os dados de animais.
   */
  public function __construct(
    private AnimalModel $obAnimalModel,
  ) {}

  /**
   * Cria um novo animal com os dados fornecidos.
   * @param array $data            Dados do animal a ser criado.
   * @return AnimalModel|AnimalDTO O modelo do animal criado.
   */
  public function create(array $data) :AnimalModel|AnimalDTO {
    $data['imageProfile'] = $this->handleMedia($data);
    return $this->obAnimalModel->create($data);
  }

  /**
   * Edita um animal existente com os dados fornecidos.
   * @param int $id                ID do animal a ser editado.
   * @param array $data            Dados do animal a serem atualizados.
   * @return AnimalModel|AnimalDTO O modelo do animal atualizado.
   */
  public function edit(int $id, array $data) :AnimalModel|AnimalDTO {
    $imagePath = $this->handleMedia($data, $id);

    $data['imageProfile'] = $imagePath;
    return $this->obAnimalModel->edit($id, $data);
  }

  /**
   * Método responsável por lidar com o upload e armazenamento da mídia, bem como a remoção da mídia antiga, se aplicável.
   * @param  array    $data       Dados relacionados à mídia, incluindo o arquivo, ID do usuário e ID do anúncio.
   * @param  int|null $resourceId ID do recurso existente para o qual a mídia está sendo editada (opcional).
   * @return string|null          Caminho da mídia salva ou null se não houver mídia.
   */
  private function handleMedia(array $data, ?int $resourceId = null) :?string {
    $userId         = $data['userId'];
    $announcementId = $data['announcementId'];
    
    if(!isset($data['imageProfile']) | !$userId | !$announcementId)
      return null;

    if($resourceId) $this->removeOldMedia($resourceId);
    return (new File("user/{$userId}/announcement/{$announcementId}/media/"))->save($data['imageProfile'], width: 256, height: 256);
  }

  /**
   * Remove a mídia antiga associada a um registro, caso exista.
   * @param  int  $idMedia
   * @return void
   */
  private function removeOldMedia(int $idMedia) :void {
    $media = $this->obAnimalModel->getById($idMedia, relations: ['announcement']);

    if($media){
      $announcementId = $media->announcement->id;
      $userId         = $media->announcement->userId;

      (new File("user/{$userId}/announcement/{$announcementId}/media/"))->remove($media->imageProfile);
    }
  }
}