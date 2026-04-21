<?php

namespace App\Services;

use App\DTO\ReportMessage\ReportMessageDTO;
use App\Exceptions\BusinessException;
use App\Models\AnnouncementModel;
use App\Models\FormModel;
use App\Models\ReportMessageModel;
use App\Models\ReportModel;
use App\Services\Interfaces\IReportService;

class ReportService implements IReportService {

  /**
   * Limite máximo de denúncias até que o anúncio seja bloqueado
   * @var int
   */
  private const int MAX_REPORT_COUNT = 50;

  /**
   * Método Construtor
   * @param ReportModel        $obReportModel
   * @param ReportMessageModel $reportMessageModel
   * @param AnnouncementModel  $obAnnouncementModel
   * @param FormModel          $obFormModel
   */
  public function __construct(
    private ReportModel        $obReportModel,
    private ReportMessageModel $reportMessageModel,
    private AnnouncementModel  $obAnnouncementModel,
    private FormModel          $obFormModel
  ) {}

  /**
   * Cria um novo registro.
   * @param  array $data Dados do registro a ser criado.
   * @return bool  Valor booleano indicando resultado da operação.
   */
  public function create(array $data) :bool {
    $obReportMessageDTO = $this->reportMessageModel->getById($data['reportMessageId']);
    $announcementId     = $data['announcementId'];

    if(!$obReportMessageDTO instanceof ReportMessageDTO || $obReportMessageDTO->type != $data['type'])
      throw new BusinessException("Mensagem não foi encontrada ou com o tipo inválido", 404);

    $obReportedEntity = match($data['type']){
      'announcement' => $this->obAnnouncementModel->getById($announcementId, parse: false),
      'form'         => $this->obFormModel->getFormByAnnouncement($announcementId, parse: false),
      default        => null
    };

    if(!$obReportedEntity instanceof AnnouncementModel && !$obReportedEntity instanceof FormModel)
      throw new BusinessException("Entidade solicitada não foi encontrada", 404);

    $this->obReportModel->insertOrIgnore([
      'user_id'           => $data['userId'],
      'report_message_id' => $data['reportMessageId'],
      'announcement_id'   => $data['type'] === 'announcement' ? $announcementId       : null,
      'form_id'           => $data['type'] === 'form'         ? $obReportedEntity->id : null,
      'description'       => $data['description']
    ]);

    if($obReportedEntity->reports->count() >= self::MAX_REPORT_COUNT)
      $obReportedEntity->edit($obReportedEntity->id, ['blocked' => true]);

    return true;
  }

}
