<?php

namespace App\Models;

use App\DTO\Form\FormDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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
   * @return null|object
   */
  public function getUserForm(int $userId, int $formId) :?object {
    return $this->where('id', $formId)->where('user_id', $userId)->first();
  }

  /**
   * Lista todos os formulários de um usuário
   * @param  int $userId
   * @return Collection
   */
  public function listFormByUser(int $userId) :Collection {
    $registers = $this->where('user_id', $userId)->get();
    return $this->parser($registers);
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
