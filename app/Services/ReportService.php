<?php

namespace App\Services;

use App\Models\ReportModel;
use App\Services\Interfaces\IReportService;

class ReportService implements IReportService {

  /**
   * Método Construtor
   * @param ReportModel $reportModel
   */
  public function __construct(
    private ReportModel $reportModel
  ) {}

  /**
   * Lista os registros com paginação.
   * @param  int $limit Número de registros por página.
   * @param  int $page  Número da página.
   * @return array      Lista de registros paginada.
   */
  public function getList($limit, $page) :array {
    // TODO: Implement getList() method.

    return [];
  }

  /**
   * Obtém um registro por ID.
   * @param  int   $id        ID do registro.
   * @param  array $relations Relacionamentos a serem carregados.
   * @return object           Objeto com os detalhes do registro.
   */
  public function getById($id, $relations) :object {
    // TODO: Implement getById() method.

    return $this->reportModel;
  }

  /**
   * Cria um novo registro.
   * @param  array  $data Dados do registro a ser criado.
   * @return object       Objeto com os detalhes do registro criado.
   */
  public function create($data) :object {
    // TODO: Implement create() method.

    return $this->reportModel;
  }

  /**
   * Edita um registro existente.
   * @param  int    $id   ID do registro a ser editado.
   * @param  array  $data Dados atualizados do registro.
   * @return object       Objeto com os detalhes do registro atualizado.
   */
  public function edit($id, $data) :object {
    // TODO: Implement edit() method.

    return $this->reportModel;
  }

  /**
   * Remove um registro.
   * @param  ?int $id ID do registro a ser removido.
   * @return bool     Indica se a remoção foi bem-sucedida.
   */
  public function remove($id = null) :bool {
    // TODO: Implement remove() method.

    return false;
  }

}
