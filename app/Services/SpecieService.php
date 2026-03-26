<?php

namespace App\Services;

use App\Models\SpecieModel;
use App\Services\Interfaces\IAnnouncementService;

class SpecieService implements IAnnouncementService {

  /**
   * Construtor do serviço de espécies.
   * @param SpecieModel $specieModel Modelo de dados para espécie
   */
  public function __construct(
    private SpecieModel $specieModel
  ) {}

  /**
   * Obtém uma lista de espécies com base nos parâmetros de limite e página.
   * @param  int $limit Número máximo de registros a serem retornados.
   * @param  int $page  Número da página para paginação dos resultados.
   * @return array      Lista de espécies.
   */
  public function getList(int $limit, int $page) :array {
    return $this->specieModel->list($limit, $page, relations: ['breeds']);
  }

  /**
   * Obtém uma espécie pelo ID.
   * @param int   $id        ID da espécie.
   * @param array $relations Relações a serem carregadas junto com o modelo.
   * @return object|null     Modelo da espécie encontrado ou null se não existir.
   */
  public function getById(int $id, array $relations = []) :object {
    return (object) null;
  }

  /**
   * Cria uma nova espécie.
   * @param  array $data Dados da espécie a ser criada.
   * @return object      Modelo da espécie criada.
   */
  public function create(array $data) :object {
    return (object) null;
  }

  /**
   * Edita uma espécie existente.
   * @notimplementeded
   * @param  int   $id   ID da espécie a ser editada.
   * @param  array $data Dados atualizados da espécie.
   * @return object      Modelo da espécie editada.
   */
  public function edit(int $id, array $data) :object {
    return (object) null;
  }

  /**
   * Remove uma espécie.
   * @notimplementeded
   * @param  ?int $id ID da espécie a ser removida. Se null, nenhuma ação é realizada.
   * @return bool     Indica se a remoção foi bem-sucedida. Sempre retorna false neste caso.
   */
  public function remove(?int $id = null) :bool {
    return false;
  }
}
