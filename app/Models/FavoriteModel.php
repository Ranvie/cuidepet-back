<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteModel extends BusinessModel {

    /**
     * Aponta a entidade do banco de dados
     * @var string
     */
    public $table = 'tb_favorite';

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

    public function announcement() :BelongsTo {
        return $this->belongsTo(AnnouncementModel::class, 'announcement_id', 'id');
    }

    public function user() :BelongsTo {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

}
