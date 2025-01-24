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
    protected $class = PublicAnnouncementDTO::class;

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

    //TODO: Pensar melhor em como fazer isso funcionar...
    //A ideia é que dê para colocar filtros do where ao listar os registros, mas ficou a dúvida de se devemos colocar no BusinessModel.
    public function list($limit = 10, $page = 1, $hardCodedMaxItems = 50, $relations = [], $type = 'lost') {
        if($limit > $hardCodedMaxItems) $limit = $hardCodedMaxItems;

        $registers = $this
            ->with($relations)
            ->where('type', $type)
            ->paginate($limit, ['*'], 'page', $page);

        foreach ($registers->getCollection() as $register) {
            $parsed[] = $this->parser($register);
        }

        $parsed['perPage']     = $registers->perPage();
        $parsed['lastPage']    = $registers->lastPage();
        $parsed['currentPage'] = $registers->currentPage();
        $parsed['maxItems']    = $hardCodedMaxItems;

        return $parsed;
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
