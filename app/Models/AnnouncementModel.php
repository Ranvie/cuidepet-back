<?php

namespace App\Models;

use App\DTO\Announcement\AnnouncementDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AnnouncementModel extends BusinessModel {

  /**
   * Define a classe de saída dos objetos. (Formato: Classe::class)
   * @var string
   */
  protected $class = AnnouncementDTO::class;

  /**
   * Aponta a entidade do banco de dados
   * @var string
   */
  public $table = 'tb_announcement';

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
   * Define os campos que podem ser preenchidos em massa
   * @var array
   */
  public $fillable = [
    'type',
    'description',
    'main_image',
    'contact_phone',
    'contact_email',
    'active',
    'status',
    'blocked',
    'address_id',
    'user_id',
    'form_id'
  ];

  /**
   * Retorna um anúncio específico de um usuário
   * @param int $userId
   * @param int $announcementId
   * @return AnnouncementModel|null
   */
  public function getUserAnnouncement($userId, $announcementId) :?AnnouncementModel {
    return $this->where('id', $announcementId)->where('user_id', $userId)->first();
  }

  /**
   * Cria um novo anúncio
   * @param array $data
   * @param array $relations
   * @param bool  $parse
   * @return AnnouncementModel
   */
  public function create($data, $relations = [], $parse = true) :AnnouncementModel {
    parent::create($data, []);
    return parent::getById($this->original['id'], $relations, $parse);
  }

  /**
   * Define o relacionamento entre anúncio e usuário. Um anúncio pertence a um usuário.
   * @return BelongsTo
   */
  public function user() :BelongsTo {
    return $this->belongsTo(UserModel::class, 'user_id', 'id');
  }

  /**
   * Define o relacionamento entre anúncio e animal. Um anúncio tem um animal.
   * @return HasOne
   */
  public function animal() :HasOne {
    return $this->hasOne(AnimalModel::class, 'announcement_id', 'id');
  }

  /**
   * Define o relacionamento entre anúncio e mídias. Um anúncio pode ter várias mídias.
   * @return HasMany
   */
  public function announcementMedia() :HasMany {
    return $this->hasMany(AnnouncementMediaModel::class, 'announcement_id', 'id');
  }

  /**
   * Define o relacionamento entre anúncio e formulário. Um anúncio pertence a um formulário.
   * @return BelongsTo
   */
  public function form() :BelongsTo {
    return $this->belongsTo(FormModel::class, 'form_id', 'id');
  }

  /**
   * Define o relacionamento entre anúncio e favoritos. Um anúncio pode ter muitos usuários que o favoritaram.
   * @return BelongsToMany
   */
  public function favorites() :BelongsToMany {
    return $this->belongsToMany(UserModel::class, FavoriteModel::class, 'announcement_id', 'user_id');
  }

  /**
   * Define o relacionamento entre anúncio e denúncias. Um anúncio pode ter várias denúncias.
   * @return HasMany
   */
  public function reports() :HasMany {
    return $this->hasMany(ReportModel::class, 'announcement_id', 'id');
  }

  /**
   * Define o relacionamento entre anúncio e respostas de formulário. Um anúncio pode ter várias respostas de formulário.
   * @return HasMany
   */
  public function formResponses() :HasMany {
    return $this->hasMany(FormResponseModel::class, 'announcement_id', 'id');
  }

  /**
   * Define o relacionamento entre anúncio e histórico de respostas. Um anúncio pode ter várias entradas no histórico de respostas.
   * @return HasMany
   */
  public function responseHistory() :HasMany {
    return $this->hasMany(UserResponseHistoryModel::class, 'announcement_id', 'id');
  }

  /**
   * Define o relacionamento entre anúncio e endereço. Um anúncio tem um endereço.
   * @return BelongsTo
   */
  public function address() :BelongsTo {
    return $this->belongsTo(AddressModel::class, 'address_id', 'id');
  }

}
