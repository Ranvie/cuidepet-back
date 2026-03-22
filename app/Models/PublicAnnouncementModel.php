<?php

namespace App\Models;

use App\DTO\PublicAnnouncement\PublicAnnouncementDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PublicAnnouncementModel extends BusinessModel {

	/**
	 * Define a classe de saída dos objetos. (Formato: Classe::class)
	 * @var string
	 */
	protected string $class = PublicAnnouncementDTO::class;

	/**
	 * Aponta a entidade do banco de dados
	 * @var string
	 */
	public string $table = 'tb_announcement';

	/**
	 * Aponta a chave primária no banco de dados
	 * @var string
	 */
	public string $primaryKey = 'id';

	/**
	 * Define a chave primária como auto incremento
	 * @var bool
	 */
	public bool $incrementing = true;

	/**
	 * Define campos created_at e updated_at gerenciados pelo láravel
	 * @var bool
	 */
	public bool $timestamps = true;

	/**
	 * Define os campos que podem ser preenchidos em massa
	 * @var array
	 */
	public array $fillable = [
		'type',
		'description',
		'main_image',
		'contact_phone',
		'contact_email',
		'user_id',
		'form_id',
		'address_id'
	];

	/**
	 * Define o relacionamento com o usuário. Um anúncio pertence a um usuário.
	 * @return BelongsTo
	 */
	public function user() :BelongsTo {
		return $this->belongsTo(UserModel::class, 'user_id', 'id');
	}

	/**
	 * Define o relacionamento com o animal. Um anúncio possui um animal.
	 * @return HasOne
	 */
	public function animal() :HasOne {
		return $this->hasOne(AnimalModel::class, 'announcement_id', 'id');
	}

	/**
	 * Define o relacionamento com os arquivos de mídia. Um anúncio pode ter várias mídias.
	 * @return HasMany
	 */
	public function announcementMedia() :HasMany {
		return $this->hasMany(AnnouncementMediaModel::class, 'announcement_id', 'id');
	}

	/**
	 * Define o relacionamento com o formulário. Um anúncio pertence a um formulário.
	 * @return BelongsTo
	 */
	public function form() :BelongsTo {
		return $this->belongsTo(FormModel::class, 'form_id', 'id');
	}

	/**
	 * Define o relacionamento com os favoritos. Um anúncio pode ter vários favoritos.
	 * @return HasMany
	 */
	public function favorites () :HasMany {
		return $this->hasMany(FavoriteModel::class, 'announcement_id', 'id');
	}

	/**
	 * Define o relacionamento com as denúncias. Um anúncio pode ter várias denúncias.
	 * @return HasMany
	 */
	public function reports () :HasMany {
		return $this->hasMany(ReportModel::class, 'announcement_id', 'id');
	}

	/**
	 * Define o relacionamento com as respostas de formulário. Um anúncio pode ter várias respostas de formulário.
	 * @return HasMany
	 */
	public function formResponses () :HasMany {
		return $this->hasMany(FormResponseModel::class, 'announcement_id', 'id');
	}

	/**
   * Define o relacionamento entre anúncio e endereço. Um anúncio tem um endereço.
   * @return HasOne
   */
  public function address() :HasOne {
    return $this->hasOne(AddressModel::class, 'announcement_id', 'id');
  }

}
