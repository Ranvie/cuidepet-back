<?php

namespace App\Services\Interfaces;

interface IReportService {

  /**
   * Cria um novo registro.
   * @param  array  $data Dados do registro a ser criado.
   * @return bool   Valor booleano indicando resultado da operação.
   */
  public function create(array $data) :bool;

}
