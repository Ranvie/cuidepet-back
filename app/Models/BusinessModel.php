<?php

namespace App\Models;

use App\Classes\Filter;
use App\Utils\PARSE_MODE;
use Illuminate\Database\Eloquent\Model;
use App\Utils\ParseConvention;
use App\Utils\Objectfy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Modelo de negócio base para os modelos do sistema.
 * Fornece métodos genéricos para operações comuns, como listagem, criação, edição e remoção de registros.
 */
class BusinessModel extends Model {

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
  public function __construct() {
    $this->obParseConvention = new ParseConvention;
  }

  /**
   * Converte os objetos do banco de dados para o formato definido na propriedade $class
   * @param  Model|Collection $registers
   * @param  string|null      $class
   * @return object
   */
  public function parser(Model|Collection $registers, ?string $class = null) :object {
    $parsed = !$registers instanceof Collection
      ? $this->obParseConvention::parse($registers->original, PARSE_MODE::snakeToCamel, $class ?? $registers->class)
      : $registers->map(function ($obModel) {
        return $this->obParseConvention::parse($obModel->original, PARSE_MODE::snakeToCamel, $obModel->class); //TODO: Está inserindo objetos via método mágico no DTO
      });

    if (isset($registers->relations))
      foreach ($registers->relations as $key => $register) is_null($register) ?: $parsed->$key = $this->parser($register);

    return $parsed;
  }

  /**
   * Lista os registros do banco de dados
   * @param  int      $limit
   * @param  int      $page
   * @param  int      $hardCodedMaxItems
   * @param  string[] $relations
   * @param  Filter[] $filters
   * @return array
   */
  public function list(int $limit = 10, int $page = 1, int $hardCodedMaxItems = 100, array $relations = [], array $filters = []) :array {
    if ($limit > $hardCodedMaxItems) $limit = $hardCodedMaxItems;

    $query = self::query();
    $this->addFilters($query, $filters);

    $query->with($relations);
    $registers = $query->paginate($limit, ['*'], 'page', $page);

    $parsedRegisters = [];
    foreach ($registers as $register) {
      $parsedRegisters[] = $this->parser($register);
    }

    $parsed['registers']   = $parsedRegisters;
    $parsed['perPage']     = $registers->perPage();
    $parsed['lastPage']    = $registers->lastPage();
    $parsed['currentPage'] = $registers->currentPage();
    $parsed['total']       = $registers->total();
    $parsed['maxItems']    = $hardCodedMaxItems;

    return $parsed;
  }

  /**
   * Procura um registro por ID
   * @param  int      $id
   * @param  string[] $relations
   * @param  boolean  $parse
   * @return null|object
   */
  public function getById(int $id, array $relations = [], bool $parse = true) :?object {
    $query = self::query();
    $this->addFilters($query);

    $model = $query->where($this->primaryKey, $id)->with($relations)->first();
    if (!$model instanceof $this) return null;
    if (!$parse) return $model;

    return $this->parser($model);
  }

  /**
   * Procura um registro por uma query personalizada
   * @param  string[] $relations
   * @param  boolean  $parse
   * @return null|object
   */
  public function getByQuery(array $filters, array $relations = [], bool $parse = true) :?object {
    $queryBuilder = self::query();
    $this->addFilters($queryBuilder, $filters);

    $model = $queryBuilder->with($relations)->first();
    if (!$model instanceof $this) return null;
    if (!$parse) return $model;

    return $this->parser($model);
  }

  /**
   * Adiciona filtros a uma query
   * @param  Builder $query
   * @param  Filter[] $filters
   * @return void
   */
  private function addFilters(Builder $query, array $filters = []) :void {
    foreach ($filters as $filter) {
      $query->where($filter->column, $filter->operator, $filter->value, $filter->boolean);
    }
  }

  /**
   * @param  array    $data
   * @param  string[] $relations
   * @param  boolean  $parse
   * @return object
   */
  public function create(array $data, array $relations = [], bool $parse = true) :object {
    if (empty($data)) {
      $this->save();
      return $this->getById($this->original[$this->primaryKey], $relations, $parse);
    }

    return
      DB::transaction(function () use ($data, $relations, $parse) {
        $origin = ParseConvention::parse($data, PARSE_MODE::camelToSnake);

        $this->fill($origin);
        $this->save();

        return $this->getById($this->original[$this->primaryKey], $relations, $parse);
      });
  }

  /**
   * Atualiza o registro no banco de dados
   * @param  int          $id
   * @param  array|object $data
   * @param  boolean      $ignoreNulls
   * @param  boolean      $parse
   * @return null|object
   */
  public function edit(int $id, array|object $data, bool $ignoreNulls = true, bool $parse = true) :?object {
    $register = $this->getById($id, parse: false);
    if (!$register instanceof $this) return null;

    $origin = ParseConvention::parse($data, PARSE_MODE::camelToSnake);
    $destin = $register->original;
    $obj    = Objectfy::transferTo($origin, $destin, $ignoreNulls);

    $register->fill($obj);
    $register->save();

    return $this->getById($id, [], $parse);
  }

  /**
   * Apaga um registro no banco de dados
   * @param  int|null $id
   * @return bool
   */
  public function remove(?int $id = null) :bool {
    $id = $id ?? $this->original['id'];
    
    if (!$id) 
      return false;

    $query = self::query();
    $query->where('id', $id);

    return $query->delete();
  }

  /**
   * Cria uma nova instância do modelo
   * @return static
   */
  public function newModel() :static {
    return new static();
  }
  
}
