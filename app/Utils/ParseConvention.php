<?php

namespace App\Utils;
use Exception;

enum PARSE_MODE: int{
    case snakeToCamel = 0;
    case camelToSnake = 1;
}

class ParseConvention {

    public static function parse(array|object $origin, PARSE_MODE $mode = PARSE_MODE::camelToSnake, string $class = null): array|object{
        if(!is_null($class) && !class_exists($class)) throw new Exception("A classe " . $class . " não foi encontrada.");

        $parsed = [];

        if(gettype($origin) === 'object'){
            $origin = get_object_vars($origin);
        }

        switch($mode){
            case PARSE_MODE::camelToSnake:
                $parsed = self::parser($origin, 'camelToSnakeParser');
            break;

            case PARSE_MODE::snakeToCamel:
                $parsed = self::parser($origin, 'snakeToCamelParser');
            break;
        }

        if(!is_null($class)){
            $parsed = Objectfy::arrayToClass($parsed, $class);
        }

        return $parsed;
    }

    private static function parser(array $origin, string $parser): array{
        $parsed = [];
        $parsedKey = '';

        foreach($origin as $key => $param){
            if(gettype($param) === 'array'){
                $parsedKey = self::$parser($key);
                $parsed[$parsedKey] = self::parser($param, $parser);
                continue;
            }

            $parsedKey = self::$parser($key);
            $parsed[$parsedKey] = $param;
        }

        return $parsed;
    }

    private static function camelToSnakeParser($text): string{
        $result = '';

        $result = gettype($text) === 'string' ? strtolower($text[0]) : $text;
        for($i = 1; $i < strlen($text); $i++){
            if(ctype_upper($text[$i])){
                $result .= '_' . strtolower($text[$i]);
            }else{
                $result .= $text[$i];
            }
        }

        return $result;
    }

    private static function snakeToCamelParser($text): string{
        $result = '';

        $result = strtolower($text[0]);
        for($i = 1; $i < strlen($text); $i++){
            if($text[$i] === '_'){
                $i = $i < strlen($text) ? $i+1 : $i;

                $result .= strtoupper($text[$i]);
            }else{
                $result .= strtolower($text[$i]);
            }
        }

        return $result;
    }

}
