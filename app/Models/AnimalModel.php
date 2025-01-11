<?php

namespace App\Models;

use App\DTO\Animal\AnimalDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimalModel extends BusinessModel {

    /**
     * Define a classe de saída dos objetos. (Formato: Classe::class)
     * @var string
     */
    protected $class = AnimalDTO::class;

    /**
     * Aponta a entidade do banco de dados
     * @var string
     */
    public $table = 'tb_animal';

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

    public function create($data, $relations = [])
    {
        //parent::create($data, []);
    }


    public function announcement() :BelongsTo {
        return $this->belongsTo(AnnouncementModel::class, 'id_announcement', 'id');
    }

    public function breed() :BelongsTo {
        return $this->belongsTo(BreedModel::class, 'id_breed', 'id');
    }

    public function species() :BelongsTo {
        return $this->belongsTo(SpecieModel::class, 'id_specie', 'id');
    }

}
