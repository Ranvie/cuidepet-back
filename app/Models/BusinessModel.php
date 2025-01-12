<?php

namespace App\Models;

use App\Utils\PARSE_MODE;
use Illuminate\Database\Eloquent\Model;
use App\Utils\ParseConvention;
use App\Utils\Objectfy;
use Illuminate\Database\Eloquent\Collection;
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
     * Objeto de conversão de classes
     * @var ParseConvention
     */
    private ParseConvention $obParseConvention;

    /**
     * Método Construtor
     */
    public function __construct(){
        $this->obParseConvention = new ParseConvention;
    }

    /**
     * Método responsável por converter de snake_case para camelCase
     */
    public function parser(Model|Collection $registers) {
        $parsed = [];

        if(isset($registers->relations))
            foreach ($registers->relations as $register) $parsed[] = $this->parser($register);

        $parsed[] = !$registers instanceof Collection
            ? $this->obParseConvention::parse($registers->original, PARSE_MODE::snakeToCamel, $registers->class)
            : $registers->map(function($obModel){
                return $this->obParseConvention::parse($obModel->original, PARSE_MODE::snakeToCamel, $obModel->class);
            });

        return $parsed;
    }

    public function list($limit = 10, $page = 1, $hardCodedMaxItems = 50) {
        $registers = $this->paginate($limit, ['*'], 'page', $page);

        $parsed = $this->parser($registers->getCollection());

        $parsed['perPage']     = $registers->perPage();
        $parsed['lastPage']    = $registers->lastPage();
        $parsed['currentPage'] = $registers->currentPage();
        $parsed['maxItems']    = $hardCodedMaxItems;

        return $parsed;
    }

    /**
     * Procura um registro por ID
     * @param integer $id
     * @param array<string> $relations
     * @return null|object
     */
    public function getById($id, $relations = [], bool $parse = true) {
        $model = parent::where($this->primaryKey, $id)->with($relations)->first();
        if(!$model instanceof $this) return null;
        if(!$parse) return $model;

        $teste = [];
        $parsed = $this->parser($model);
        /*foreach($model->relations as $key => $relation) {
            if(empty($relation)) { continue; }

            //$parsed->$key = $this->parser($relation);
            $t
        }*/

        dd($parsed);

        return $parsed;
    }

    /**
     * @param array $data
     * @param array<string> $relations
     * @return null|object
     */
    public function create($data, $relations = [], $parse = true) {
        if(empty($data)) {
            $this->save();
            return $this->getById($this->original[$this->primaryKey], $relations, $parse);
        }

        return
            DB::transaction(function() use($data, $relations, $parse) {
            $origin = ParseConvention::parse($data, PARSE_MODE::camelToSnake);

            $this->fill($origin);
            $this->save();

            foreach($relations as $relation){
                $content = $origin[$relation];
                $this->saveRelations($content, $relation);
            }

            return $this->getById($this->original[$this->primaryKey], $relations, $parse);
        });
    }

    /**
     * Atualiza o registro no banco de dados
     * @param integer $id
     * @param array|object $data
     * @param boolean $ignoreNulls
     * @return null|object
     */
    public function edit($id, $data, $ignoreNulls = true, $parse = true) {
        $register = parent::find($id);
        if(!$register instanceof $this) return null;

        //TODO: Pensar em uns nomes melhores depois kkkkk
        $origin = ParseConvention::parse($data, PARSE_MODE::camelToSnake);
        $destin = $register->original;
        $obj = Objectfy::transferTo($origin, $destin, $ignoreNulls);

        $register->fill($obj);
        $register->save();

        //TODO: Pensar em como podemos fazer os retornos dos cruds, visto que se for um response, podem faltar campos para mexer depois
        return $this->getById($id, [], $parse);
    }

    /**
     * Apaga um registro no banco de dados
     * @param integer $id
     * @return boolean
     */
    public function remove($id = null) {
        $id = $id ?? $this->original['id'];

        if(!$id) return false;
        return $this->where('id', $id)->delete();
    }

    public function newModel(){
        return new static();
    }

}
