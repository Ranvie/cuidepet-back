<?php

namespace App\Models;

use App\DTO\AnnouncementMedia\AnnouncementMediaDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementMediaModel extends BusinessModel {

    /**
     * Define a classe de saída dos objetos. (Formato: Classe::class)
     * @var string
     */
    protected $class = AnnouncementMediaDTO::class;

    /**
     * Aponta a entidade do banco de dados
     * @var string
     */
    public $table = 'tb_announcement_media';

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
    public $timestamps = false;

    public $fillable = ['announcement_id', 'url'];

    public function announcement() :BelongsTo {
        return $this->belongsTo(AnnouncementModel::class, 'announcement_id', 'id');
    }

}
