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
    public $primaryKey = 'announcement_id';

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

    public $fillable = [
        'announcement_id', 'name', 'gender', 'color', 'size', 'age', 'disability', 'vaccinated', 'dewormed',
        'castrated', 'image_profile', 'last_seen_date', 'breed_id', 'specie_id'
    ];

    public function create($data, $relations = [], $parse = true)
    {
        parent::create($data, []);
        return parent::getById($this->original['announcement_id'], ['breed', 'species']);
    }


    public function announcement() :BelongsTo {
        return $this->belongsTo(AnnouncementModel::class, 'announcement_id', 'id');
    }

    public function breed() :BelongsTo {
        return $this->belongsTo(BreedModel::class, 'breed_id', 'id');
    }

    public function specie() :BelongsTo {
        return $this->belongsTo(SpecieModel::class, 'specie_id', 'id');
    }

}
