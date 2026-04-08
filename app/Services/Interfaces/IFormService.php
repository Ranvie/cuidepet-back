<?php

namespace App\Services\Interfaces;

use App\DTO\Form\FormDTO;

interface IFormService {

  /**
   * Lista paginada de formulários de um usuário específico.
   * @param  int $limit   Número de formulários por página.
   * @param  int $page    Número da página atual.
   * @param  int $userId ID do usuário.
   */
  public function listFormsByUser(int $limit, int $page, int $userId) :array;

  /**
   * Lista todos os formulários de um usuário específico, sem paginação. (para select de formulário)
   * @param  int $userId ID do usuário.
   * @return array Lista de todos os formulários do usuário.
   */
  public function listAllUserForms(int $userId) :array;

  /**
   * Busca formulário de um usuário por ID.
   * @param  int $formId ID do formulário.
   * @param  int $userId ID do usuário.
   * @return FormDTO     Coleção de formulários do usuário.
   */
  public function getUserFormById(int $formId, int $userId) :FormDTO;

  /**
   * Busca formulário associado a um anúncio por ID.
   * @param  int $announcementId ID do anúncio.
   * @return FormDTO Coleção de formulários do usuário.
   */
  public function getFormByAnnouncement(int $announcementId) :FormDTO;

  /**
   * Cadastra formulários de anúncios.
   * @param  array $data Dados do formulário a ser cadastrado.
   * @return FormDTO DTO do formulário cadastrado.
   */
  public function create(array $data) :FormDTO;

  /**
   * Atualiza formulários de anúncios.
   * @param  int $formId ID do formulário a ser atualizado.
   * @param  array $data Dados do formulário a ser atualizado.
   * @return FormDTO DTO do formulário atualizado.
   */
  public function edit(int $formId, array $data) :FormDTO;

  /**
   * Exclui formulários de anúncios.
   * @param  int $formId ID do formulário a ser excluído.
   * @return bool True se a exclusão foi bem-sucedida, false caso contrário.
   */
  public function remove(int $formId) :bool;
}
