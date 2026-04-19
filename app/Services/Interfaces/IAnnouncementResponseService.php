<?php

namespace App\Services\Interfaces;

use App\DTO\FormResponse\FormResponseDTO;

/**
 * Interface para o serviço de resposta de anúncio.
 * Define os métodos que devem ser implementados por qualquer classe que gerencie as respostas dos anúncios.
 */
interface IAnnouncementResponseService {

  /**
   * Lista paginada de respostas que um anúncio possui.
   * @param  int $limit          Número de formulários por página.
   * @param  int $page           Número da página atual.
   * @param  int $announcementId ID do anúncio associado.
   * @return array               Lista paginada de resposta de um anúncio.
   */
  public function listAnnouncementResponses(int $limit, int $page, int $announcementId) :array;

  /**
   * Busca uma resposta de um anúncio.
   * @param  int $responseId     ID da resposta.
   * @param  int $announcementId ID do anúncio associado.
   * @return FormResponseDTO     Coleção de formulários do usuário.
   */
  public function getAnnouncementResponseById(int $responseId, int $announcementId) :FormResponseDTO;

  /**
   * Verifica se o usuário já respondeu ao form anteriormente
   * @param int $announcementId ID do anúncio associado
   * @param int $userId         ID do usuário que deseja responder ao anúncio
   * @return bool               Retorno booleano indicando sim ou não para resposta
   */
  public function checkIfUserResponded(int $announcementId, int $userId) :bool;

  /**
   * Cadastra formulários de resposta a um anúncio.
   * @param  array $data     Dados do formulário a ser cadastrado.
   * @return FormResponseDTO DTO do formulário cadastrado.
   */
  public function create(array $data) :FormResponseDTO;

  /**
   * Exclui formulários de resposta de um anúncio.
   * @param int  $announcementId ID do anúncio associado
   * @param  int $responseId     ID do formulário a ser excluído.
   * @return bool                True se a exclusão foi bem-sucedida, false caso contrário.
   */
  public function remove(int $announcementId, int $responseId) :bool;

}
