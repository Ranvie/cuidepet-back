<?php

namespace App\Services\Interfaces;

interface IService {

  /**
   * Lista os registros com paginação.
   * @param  int $limit Número de registros por página.
   * @param  int $page  Número da página.
   * @return array      Lista de registros paginada.
   */
  public function getList(int $limit, int $page): array;

  /**
   * Obtém um registro por ID.
   * @param  int   $id        ID do registro.
   * @param  array $relations Relacionamentos a serem carregados.
   * @return object           Objeto com os detalhes do registro.
   */
  public function getById(int $id, array $relations): object;

  /**
   * Cria um novo registro.
   * @param  array  $data Dados do registro a ser criado.
   * @return object       Objeto com os detalhes do registro criado.
   */
  public function create(array $data): object;

  /**
   * Edita um registro existente.
   * @param  int    $id   ID do registro a ser editado.
   * @param  array  $data Dados atualizados do registro.
   * @return object       Objeto com os detalhes do registro atualizado.
   */
  public function edit(int $id, array $data): object;

  /**
   * Remove um registro.
   * @param  ?int $id ID do registro a ser removido.
   * @return bool     Indica se a remoção foi bem-sucedida.
   */
  public function remove(?int $id = null): bool;
}
