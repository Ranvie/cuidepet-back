<?php

namespace App\Services\Interfaces;

use Illuminate\Support\Collection;

interface IFormService extends IService {

  /**
   * Lista os formulários de um usuário específico.
   * @param  int $userId ID do usuário.
   * @return Collection  Coleção de formulários do usuário.
   */
  public function listFormByUser(int $userId) :Collection;
}
