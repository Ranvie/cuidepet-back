<?php

namespace App\Utils;
use ReflectionClass;
use Exception;

//TODO: Tem como melhorar essa classe colocando para pegar as chaves por recursão... Ela só obtém no primeiro nível
class Objectfy {

    public static function checkIfObjectIsValid(array $object, array $allowedList = ['NULL', 'string', 'integer', 'double']): bool{
        $objVals = ($object);

        foreach($objVals as $objVal){
            if(!in_array(gettype($objVal), $allowedList)){
                throw new Exception('O tipo ' . gettype($objVal) . ' não é suportado. Tipos permitidos nos atributos do objeto pai: ' . implode(', ', $allowedList));
            }
        }

        return true;
    }

    /**
     * Recebe o mapeamento e um array para converter
     * @param array $convertion ex ['input-id' => 'id', ['input-text'] => 'description']
     * @param array $origin ex ['input-id' => valor, 'input-text' => valor]
     * @return array um array com as chaves convertidas com base em $convertion
     */
    public static function parseArray(array $convertion, array $origin): array{
        $arr = [];

        foreach($origin as $key => $value){
            if(!key_exists($key, $convertion)){
                throw new Exception('A chave ' . $key . ' não está presente no array de conversão ($convertion).');
            }

            $key = $convertion[$key];
            $arr[$key] = $value;
        }

        return $arr;
    }

    /**
     *  Converte um array em uma classe (A classe deve ter um construtor que não recebe parâmetros)
     *  @param array $origin recebe um array com os atributos da classe
     *  @param string $class recebe a classe do objeto de retorno, ex Class::class
     */
    public static function arrayToClass(array $origin, string $class): object | null{
        $parsed = new $class();
        $refObj = new ReflectionClass($parsed);
        $arrKeys = array_keys($origin);

        if(!class_exists($class)) {
            throw new Exception('A classe ' . $class . ' não foi encontrada');
        }


        foreach($arrKeys as $key){
            if(!$refObj->hasProperty($key)){
                continue;
            }

            $prop = $refObj->getProperty($key);

            $prop->setAccessible(true);
            $prop->setValue($parsed, $origin[$key]);
        }

        return $parsed;
    }

    public static function transferTo($origin, $destin, $ignoreNulls = true){

        switch (gettype($origin)){
            case 'object':
                foreach($origin as $key => $value){
                    if($ignoreNulls && is_null($value)) continue;

                    $destin->$key = $value;
                }
                break;
            case 'array':
                foreach($origin as $key => $value){
                    if($ignoreNulls && is_null($value)) continue;

                    $destin[$key] = $value;
                }
                break;
        }

        return $destin;
    }
}
