<?php

namespace App\Models;

use App\DTO\Form\FormDTO;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public $fillable = ['user_id', 'title', 'url', 'payload'];

    public function getUserForm($userId, $formId){
        return $this->where('id', $formId)->where('user_id', $userId)->first();
    }

    public function listFormByUser($userId){
        $registers = $this->where('user_id', $userId)->get();
        return $this->parser($registers);
    }

    public function create($data, $relations = [], $parse = true)
    {
        return parent::create($data, $relations);
    }

    public function announcements() :HasMany {
        return $this->hasMany(AnnouncementModel::class, 'form_id', 'id');
    }

    public function user() :BelongsTo {
        return $this->belongsTo(UserModel::class, 'id', 'user_id');
    }

}
