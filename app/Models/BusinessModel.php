<?php

namespace App\Models;

use App\DTO\User\UserDTO;
use App\Exceptions\BusinessException;
use App\Utils\PARSE_MODE;
use Illuminate\Database\Eloquent\Model;
use App\Utils\ParseConvention;
use App\Utils\Objectfy;
use Illuminate\Support\Facades\DB;

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

    public function getPaginated($limit = 10, $page = 1, $hardCodedMaxItems = 50){

        $parsed = [];
        $registers = $this->paginate($limit, ['*'], 'page', $page);
        foreach($registers as $register){
            $parsed[] = ParseConvention::parse($register->original, PARSE_MODE::snakeToCamel, UserDTO::class);
        }

        $parsed['perPage']     = $registers->perPage();
        $parsed['lastPage']    = $registers->lastPage();
        $parsed['currentPage'] = $registers->currentPage();
        $parsed['maxItems']    = $hardCodedMaxItems;

        return $parsed;
    }

    /**
     * Procura um registro por ID
     * @param integer $id
     * @param array $relations
     * @return null|object
     */
    public function getById($id, $relations = []){
        $model = parent::where('id', $id)->with($relations)->first();
        if(!$model instanceof $this) return null;

        $parsed = ParseConvention::parse($model->original, PARSE_MODE::snakeToCamel, $this->class);
        foreach($model->relations as $key => $relation) {
            if(empty($relation)) { continue; }

            if($relation::class == 'Illuminate\Database\Eloquent\Collection' ){
                $items = [];
                foreach($relation as $item) {
                    $items[] = ParseConvention::parse($item->original, PARSE_MODE::snakeToCamel, $item->class);
                }

                $parsed->$key = $items;
            }
            else{
                $parsed->$key = ParseConvention::parse($relation->original, PARSE_MODE::snakeToCamel, $relation->class);
            }
        }

        return $parsed;
    }

    /**
     * @param array $data
     * @return null|object
     */
    public function create($data, $relations = []){
        if(empty($data)) {
            $this->save();
            return $this->getById($this->original['id'], $relations);
        }

        return
            DB::transaction(function() use($data, $relations){
            $origin = ParseConvention::parse($data, PARSE_MODE::camelToSnake);

            $this->fill($origin);
            $this->save();

            foreach($relations as $relation){
                $content = $origin[$relation];
                $this->saveRelations($content, $relation);
            }

            return $this->getById($this->original['id'], $relations);
        });
    }

    private function saveRelations($content, $relation){
        $isArray = isset($content[0]);
        $relationType = get_class($this->$relation());

        if($isArray){
            foreach($content as $item){
                $parse = ParseConvention::parse($item, PARSE_MODE::camelToSnake);
                $this->$relation()->create($parse, []);
            }
        }
        else{
            $parse = ParseConvention::parse($content, PARSE_MODE::camelToSnake);
            $this->$relation()->create($parse, []);
        }
    }

    /**
     * Atualiza o registro no banco de dados
     * @param integer $id
     * @param array|object $data
     * @param boolean $ignoreNulls
     * @return object
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
        return $this->getById($id);
    }

    /**
     * Apaga um registro no banco de dados
     * @param $id
     * @return bool
     */
    public function remove($id = null) : bool {
        $id = $id ?? $this->original['id'];

        if(!$id) return false;
        return $this->where('id', $id)->delete();
    }

}
