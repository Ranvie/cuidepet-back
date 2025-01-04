<?php

namespace App\Models;

use App\DTO\Preference\PreferenceDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferenceModel extends BusinessModel {

    /**
     * Define a classe de saída dos objetos. (Formato: Classe::class)
     * @var string
     */
    protected $class = PreferenceDTO::class;

    /**
     * Aponta a entidade do banco de dados
     * @var string
     */
    public $table = 'tb_preference';

    /**
     * Aponta a chave primária no banco de dados
     * @var string
     */
    public $primaryKey = 'user_id';

    /**
     * Define a chave primária como auto incremento
     * @var bool
     */
    public $incrementing = false;

    /**
     * Define campos created_at e updated_at gerenciados pelo láravel
     * @var bool
     */
    public $timestamps = false;

    public $fillable = ['receive_region_alarms', 'receive_alarms_on_email', 'receive_news'];

    public function user(): BelongsTo {
        return $this->belongsTo(UserModel::class);
    }

}
