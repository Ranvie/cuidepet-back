<?php

namespace App\Services;

use App\DTO\AnnouncementMedia\AnnouncementMediaDTO;
use App\Models\AnnouncementMediaModel;
use App\Services\Interfaces\IAnnouncementMediaService;

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
    return $this->announcementMediaModel->create($data);
  }

  /**
   * Edita um registro existente.
   * @param  int    $id                                  ID do registro a ser editado.
   * @param  array  $data                                Dados atualizados do registro.
   * @return AnnouncementMediaDTO|AnnouncementMediaModel Objeto com os detalhes do registro atualizado.
   */
  public function edit(int $id, array $data) :AnnouncementMediaDTO|AnnouncementMediaModel {
    return $this->announcementMediaModel->edit($id, $data);
  }

  /**
   * Remove um registro.
   * @param  ?int $id ID do registro a ser removido.
   * @return bool     Indica se a remoção foi bem-sucedida.
   */
  public function remove(?int $id = null) :bool {
    return $this->announcementMediaModel->remove($id);
  }

  /**
   * Inicia nova instância de Mídia do anúncio
   * @return AnnouncementMediaModel
   */
  public function newInstance() :AnnouncementMediaModel {
    return $this->announcementMediaModel->newModel();
  }
}
