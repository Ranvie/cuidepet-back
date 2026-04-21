<?php

namespace App\Models;

use App\Classes\Filter;
use App\DTO\Form\FormDTO;
use App\Exceptions\BusinessException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = FormDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_form';

  /**
   * Aponta a chave primária no banco de dados
   * @var string
   */
  public $primaryKey = 'id';

  /**
   * Define a chave primária como auto incremento
   * @var bool
   */
  public $incrementing = true;

  /**
   * Define campos created_at e updated_at gerenciados pelo láravel
   * @var bool
   */
  public $timestamps = true;

  /**
   * Desativa o campo updated_at, já que não é necessário para a entidade
   */
  const UPDATED_AT = null;

  /**
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public $fillable = [
    'user_id',
    'title',
    'payload',
    'active'
  ];

  /**
   * Recupera um formulário específico de um usuário
   * @param  int $userId
   * @param  int $formId
   * @return null|FormModel
   */
  public function getUserForm(int $userId, int $formId) :?FormModel {
    return $this->getById($formId, [], false, [new Filter('user_id', '=', $userId)]);
  }

  //TODO: ver de implementar um DTO
  /**
   * Lista todos os formulários de um usuário sem paginação
   * @param  int $userId ID do usuário
   * @return array       Lista de formulários do usuário
   */
  public function listAll(int $userId) :array {
    $listForms = $this->query()->where('user_id', $userId)->get();

    return array_combine(
      $listForms->pluck('id')->toArray(),
      $listForms->pluck('title')->toArray()
    );
  }

  /**
   * Busca um formulário a partir de um anúncio
   * @param int $announcementId ID do anúncio associado ao formulário
   * @return FormDTO|null
   */
  public function getFormByAnnouncement(int $announcementId, bool $parse = true) :?FormDTO {
    $form = $this->getByQuery([
      new Filter('announcements.id', '=', $announcementId),
      new Filter('announcements.active', '=', true),
      new Filter('announcements.blocked', '=', false),
      new Filter('announcements.status', '=', false),
    ], ['user'], $parse);

    return $form;
  }

  /**
   * Cria um novo formulário
   * @param  array $data
   * @param  array $relations
   * @param  bool  $parse
   * @return FormDTO
   */
  public function create(array $data, array $relations = [], bool $parse = true) :FormDTO {
    return parent::create($data, $relations, $parse);
  }

  /**
   * Exclui formulário de um usuário
   * @param  int|null $formId
   * @return bool
   */
  public function remove(?int $formId = null) :bool {
    $obFormModel = $this->getById($formId ?? 0, ['announcements'], false);

    if (!$obFormModel instanceof FormModel)
      throw new BusinessException('O formulário solicitado não foi encontrado.');

    $hasAnnouncements = $obFormModel->announcements->count() > 0;

    if($hasAnnouncements)
      throw new BusinessException('Não é possível excluir um formulário que possui anúncios relacionados.');

    return $obFormModel->delete();
  }

  /**
   * Recupera os anúncios relacionados ao formulário. Um formulário pode ter vários anúncios relacionados a ele.
   * @return HasMany
   */
  public function announcements() :HasMany {
    return $this->hasMany(AnnouncementModel::class, 'form_id', 'id');
  }

  /**
   * Recupera o usuário relacionado ao formulário. Um formulário pertence a um usuário.
   * @return BelongsTo
   */
  public function user() :BelongsTo {
    return $this->belongsTo(UserModel::class, 'user_id', 'id');
  }

}
