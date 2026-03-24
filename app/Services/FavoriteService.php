<?php

namespace App\Services;

use App\Models\FavoriteModel;
use App\Services\Interfaces\IFavoriteService;

class FavoriteService implements IFavoriteService {

  /**
   * Método Construtor
   * @param FavoriteModel $favoriteModel
   */
  public function __construct(
    private FavoriteModel $favoriteModel
  ) {}

  /**
   * Lista os registros com paginação.
   * @param  int $limit Número de registros por página.
   * @param  int $page  Número da página.
   * @return array      Lista de registros paginada.
   */
  public function getList(int $limit, int $page) :array {
    // TODO: Implement getList() method.

    return [];
  }

  /**
   * Obtém um registro por ID.
   * @param  int   $id        ID do registro.
   * @param  array $relations Relacionamentos a serem carregados.
   * @return object           Objeto com os detalhes do registro.
   */
  public function getById(int $id, array $relations = []) :object {
    // TODO: Implement getById() method.

    return $this->favoriteModel;
  }

  /**
   * Cria um novo registro.
   * @param  array  $data Dados do registro a ser criado.
   * @return object       Objeto com os detalhes do registro criado.
   */
  public function create(array $data) :object {
    // TODO: Implement create() method.

    return $this->favoriteModel;
  }

  /**
   * Edita um registro existente.
   * @param  int    $id   ID do registro a ser editado.
   * @param  array  $data Dados atualizados do registro.
   * @return object       Objeto com os detalhes do registro atualizado.
   */
  public function edit(int $id, array $data) :object {
    // TODO: Implement edit() method.
    
    return $this->favoriteModel;
  }

  public function remove(?int $id = null) :bool {
    // TODO: Implement remove() method.

    return false;
  }
}
