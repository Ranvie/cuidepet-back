<?php

namespace App\Services;

use App\Models\AnnouncementModel;
use App\Models\BreedSpecieModel;
use App\Services\Interfaces\IAnnouncementService;

class BreedSpecieService implements IAnnouncementService {

  /**
   * Construtor do serviço de raças e espécies.
   * @param BreedSpecieModel $breedSpecieModel Modelo de dados para raças e espécies.
   */
  public function __construct(
    private BreedSpecieModel $breedSpecieModel
  ) {}

  /**
   * Obtém uma lista de raças e espécies com base nos parâmetros de limite e página.
   * @param  int $limit Número máximo de registros a serem retornados.
   * @param  int $page  Número da página para paginação dos resultados.
   * @return array      Lista de raças e espécies.
   */
  public function getList(int $limit, int $page) :array {
    return $this->breedSpecieModel->list($limit, $page, relations: ['breed']);
  }

  /**
   * Obtém uma raça ou espécie pelo ID.
   * @param int   $id        ID da raça ou espécie.
   * @param array $relations Relações a serem carregadas junto com o modelo.
   * @return object|null     Modelo da raça ou espécie encontrado ou null se não existir.
   */
  public function getById(int $id, array $relations = []) :object {
    return (object) null;
  }

  /**
   * Cria uma nova raça ou espécie.
   * @param  array $data Dados da raça ou espécie a ser criada.
   * @return object      Modelo da raça ou espécie criada.
   */
  public function create(array $data) :object {
    return (object) null;
  }

  /**
   * Edita uma raça ou espécie existente.
   * @notimplementeded
   * @param  int   $id   ID da raça ou espécie a ser editada.
   * @param  array $data Dados atualizados da raça ou espécie.
   * @return object      Modelo da raça ou espécie editada.
   */
  public function edit(int $id, array $data) :object {
    return (object) null;
  }

  /**
   * Remove uma raça ou espécie.
   * @notimplementeded
   * @param  ?int $id ID da raça ou espécie a ser removida. Se null, nenhuma ação é realizada.
   * @return bool     Indica se a remoção foi bem-sucedida. Sempre retorna false neste caso.
   */
  public function remove(?int $id = null) :bool {
    return false;
  }
}
