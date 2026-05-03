<?php

namespace App\Services\Interfaces;

interface IReportService {

  /**
   * Lista templates de denúncias
   * @param  string $reportType Determina o tipo de denúncia cujo templates devem ser consultados
   * @return array              Retorna uma lista de templates de denúncia
   */
  public function listReportTemplates(string $reportType) :array;

  /**
   * Cria um novo registro.
   * @param  array  $data Dados do registro a ser criado.
   * @return bool   Valor booleano indicando resultado da operação.
   */
  public function create(array $data) :bool;

}
