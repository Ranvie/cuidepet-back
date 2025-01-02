<?php

namespace App\Models;

use App\Utils\PARSE_MODE;
use Illuminate\Database\Eloquent\Model;
use App\Utils\ParseConvention;
use App\Utils\Objectfy;

class BusinessModel extends Model{

    /**
     * Define a classe de saída dos objetos. (Formato: Classe::class)
     * @var string
     */
    protected $class = '';

    /**
     * Define campos created_at e updated_at gerenciados pelo láravel
     * @var bool
     */
    public $timestamps = false;

    /**
     * Retorna todos os registros da tabela
     * @return array<object>
     * @throws \Exception
     */
    public function getAll(){
        $parsed = [];
        $registers = $this->all()->toArray();

        foreach($registers as $register){
            $parsed[] = ParseConvention::parse($register, PARSE_MODE::snakeToCamel, $this->class);
        }

        return $parsed;
    }

    /**
     * Procura um registro por ID
     * @param $id
     * @return null|object
     * @throws \Exception
     */
    public function getById($id){
        $register = parent::find($id);
        if(!$register instanceof $this) return null;

        $parsed = ParseConvention::parse($register->original, PARSE_MODE::snakeToCamel, $this->class);
        return $parsed;
    }

    /**
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function create($data){
        $origin = ParseConvention::parse($data, PARSE_MODE::camelToSnake);
        $this->fill($origin);
        $this->save();

        return ParseConvention::parse($this->original, PARSE_MODE::snakeToCamel, $this->class);
    }

    /**
     * Atualiza o registro no banco de dados
     * @param integer $id
     * @param object $data
     * @param boolean $ignoreNulls
     * @return object
     * @throws \Exception
     */
    public function edit($id, $data, $ignoreNulls = true){
        $register = parent::find($id);
        if(!$register instanceof $this) return null;

        //TODO: Pensar em uns nomes melhores depois kkkkk
        $origin = ParseConvention::parse($data, PARSE_MODE::camelToSnake);
        $destin = $register->original;
        $obj = Objectfy::transferTo($origin, $destin, $ignoreNulls);

        $register->fill($obj);
        $register->save();

        //TODO: Pensar em como podemos fazer os retornos dos cruds, visto que se for um response, podem faltar campos para mexer depois
        return ParseConvention::parse($register->original, PARSE_MODE::snakeToCamel, $this->class);
    }

    /**
     * Apaga um registro no banco de dados
     * @param $id
     * @return bool
     */
    public function remove($id) :bool {
        return $this->where('id', $id)->delete();
    }

}
