<?php

namespace App\Models;

use App\DTO\Announcement\AnnouncementDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public $fillable = [
        'type', 'description', 'main_image', 'address', 'contact_phone', 'contact_email',
        'last_seen_latitude', 'last_seen_longitude', 'user_id', 'form_id'
    ];

    public function getUserAnnouncement($userId, $announcementId) {
        return $this->where('id', $announcementId)->where('user_id', $userId)->first();
    }

    public function create($data, $relations = [], $parse = true)
    {
        parent::create($data, []);
        return parent::getById($this->original['id'], $relations, $parse);
    }

    public function user() :BelongsTo {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function animal() :HasOne {
        return $this->hasOne(AnimalModel::class, 'announcement_id', 'id');
    }

    public function announcementMedia() :HasMany {
        return $this->hasMany(AnnouncementMediaModel::class, 'announcement_id', 'id');
    }

    public function form() :BelongsTo {
        return $this->belongsTo(FormModel::class, 'form_id', 'id');
    }

    public function favorites () :HasMany {
        return $this->hasMany(FavoriteModel::class, 'announcement_id', 'id');
    }

    public function reports () :HasMany {
        return $this->hasMany(ReportModel::class, 'announcement_id', 'id');
    }

    public function formResponses () :HasMany {
        return $this->hasMany(FormResponseModel::class, 'announcement_id', 'id');
    }

}
