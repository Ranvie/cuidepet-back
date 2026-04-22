<?php

namespace App\Services;

use App\Classes\Filter;
use App\DTO\Form\FormDTO;
use App\DTO\FormResponse\FormResponseDTO;
use App\Exceptions\BusinessException;
use App\FormValidator\FormResponseValidator;
use App\Http\Enums\NotificationTypes;
use App\MessageDispatcher\Builders\NotificationBuilder;
use App\MessageDispatcher\Orchestrator\MessageDispatcher;
use App\Models\AnimalModel;
use App\Models\FormResponseModel;
use App\Models\UserResponseHistoryModel;
use App\Services\Interfaces\IAnnouncementResponseService;

/**
 * Serviço de gerenciamento de resposta do usuário.
 * Fornece métodos para criar, editar, listar e remover formulários, além de validações relacionadas a formulários.
 */
class AnnouncementResponseService implements IAnnouncementResponseService {

  /**
   * Método Construtor
   * @param FormResponseModel        $obFormResponseModel
   * @param UserResponseHistoryModel $obUserResponseHistoryModel
   * @param AnimalModel              $obAnimalModel
   * @param FormService              $obFormService
   */
  public function __construct(
    private FormResponseModel        $obFormResponseModel,
    private UserResponseHistoryModel $obUserResponseHistoryModel,
    private AnimalModel              $obAnimalModel,
    private FormService              $obFormService
  ) {}

  /**
   * Lista paginada de respostas que um anúncio possui.
   * @param  int $limit          Número de formulários por página.
   * @param  int $page           Número da página atual.
   * @param  int $announcementId ID do anúncio associado.
   * @return array               Lista paginada de resposta de um anúncio.
   */
  public function listAnnouncementResponses(int $limit, int $page, int $announcementId) :array {
    return $this->obFormResponseModel->list($limit, $page, filters: [new Filter('announcement_id', '=', $announcementId)]);
  }

  /**
   * Busca uma resposta de um anúncio.
   * @param  int $announcementId ID do anúncio associado.
   * @param  int $responseId     ID da resposta.
   * @return FormResponseDTO     Coleção de formulários do usuário.
   */
  public function getAnnouncementResponseById(int $announcementId, int $responseId) :FormResponseDTO {
    $obFormResponseDTO = $this->obFormResponseModel->getByQuery([new Filter('id', $responseId), new Filter('announcement_id', '=', $announcementId)]);

    if(!$obFormResponseDTO instanceof FormResponseDTO)
      throw new BusinessException("A resposta do anúncio não foi encontrada", 404);

    return $obFormResponseDTO;
  }

  /**
   * Verifica se o usuário já respondeu ao form anteriormente
   * @param int $announcementId ID do anúncio associado
   * @param int $userId         ID do usuário que deseja responder ao anúncio
   * @return bool               Retorno booleano indicando sim ou não para resposta
   */
  public function checkIfUserResponded(int $announcementId, int $userId) :bool {
    $obFormResponseModel = $this->obFormResponseModel->getByQuery([
      new Filter('announcement_id', '=', $announcementId),
      new Filter('user_id', '=', $userId)
    ], parse: false);

    return $obFormResponseModel instanceof FormResponseModel;
  }

  /**
   * Cadastra formulários de resposta a um anúncio.
   * @param  array $data     Dados do formulário a ser cadastrado.
   * @return FormResponseDTO DTO do formulário cadastrado.
   */
  public function create(array $data) :FormResponseDTO {
    $obFormDTO            = $this->obFormService->getFormByAnnouncement($data['announcement_id']);
    $obAnimalDTO          = $this->obAnimalModel->getByQuery([new Filter('announcement_id', '=', $data['announcement_id'])]); 
    $obResponseHistoryDTO = $this->obUserResponseHistoryModel->getByQuery([
      new Filter('announcement_id', '=', $data['announcement_id']), 
      new Filter('user_id', '=', $data['user_id'])
    ]);

    if(!$obFormDTO instanceof FormDTO)
      throw new BusinessException("Formulário original do anúncio {$data['announcement_id']} não encontrado.", 404);

    if($obFormDTO->user->id === $data['user_id'])
      throw new BusinessException("Você não pode responder ao próprio anúncio.", 403);

    new FormResponseValidator(
      $this->convertJsonStringToArray($data['payload'] ?? '[]'),
      $this->convertJsonStringToArray($obFormDTO->payload)
    )->resolve();

    $this->obFormResponseModel->upsert([$data], ['user_id', 'announcement_id'], ['payload']);

    new MessageDispatcher(new NotificationBuilder([$obFormDTO->user->id], NotificationTypes::NEW_RESPONSE, ['announcementId' => $data['announcement_id'], 'petName' => $obAnimalDTO->name]))->dispatch();

    return $this->obFormResponseModel->getByQuery([
      new Filter('user_id', '=', $data['user_id']),
      new Filter('announcement_id', '=', $data['announcement_id'])
    ]);
  }

  /**
   * Exclui formulários de resposta de um anúncio.
   * @param int  $announcementId ID do anúncio associado
   * @param  int $responseId     ID do formulário a ser excluído.
   * @return bool                True se a exclusão foi bem-sucedida, false caso contrário.
   */
  public function remove(int $announcementId, int $responseId) :bool {
    return $this->obFormResponseModel->getByQuery([
      new Filter('id', '=', $responseId),
      new Filter('announcement_id', '=', $announcementId)
    ], parse: false)?->delete() ?? false;
  }

  /**
   * Método responsável por converter uma string em JSON
   * @param string $jsonString String a ser convertida
   * @return array             Array resultante da conversão
   */
  private function convertJsonStringToArray(string $jsonString) :array {
    return json_decode($jsonString, true) ?? [];
  }

}
