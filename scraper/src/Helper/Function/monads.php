<?php

/**
 * Monad
 * @param $value
 * @return Closure
 */
function m($value): Closure
{
    return function(callable $cb = null) use($value) {

        if(is_null($cb)) {

            if((!$value instanceof Closure)) {
                return $value;
            }

            $result = $value();

            while($result instanceof Closure) {
                $result = $result();
            }

            return $result;
        }

        return m(function() use($cb, $value) {
            if((!$value instanceof Closure)) {
                return $cb($value);
            }
            $result = $value();

            return $cb($result);
        });

    };
}

/**
 *
 * безопасное получение айтема по ключу
 * @param $key
 * @param array $default
 * @return Closure
 */
function maybe_key($key, $default = []): Closure
{
    return function($array) use($key, $default) {

        if (!is_array($array) || !array_key_exists($key, $array)) {
            return $default;
        }
        return $array[$key];
    };
}

function maybe_method(string $method, $default = null, array $args = []): Closure
{
    return function($object) use($method, $default, $args) {

        if(!is_object($object) || !method_exists($object, $method)) {
            return $default;
        }

        return $object->{$method}(...$args);
    };
}

/**
 * Возвращает значение массива по точечной нотации
 * @param string $keys
 * @return Closure
 */
function get_by_dot_keys(string $keys): Closure
{
    $path = explode('.', $keys);

    return function(array $array) use($path) {

        $mArray = m($array);

        foreach($path as $key) {
            $mArray = $mArray(function($resultedArray) use($key) {
                return $resultedArray[$key];
            });
        }

        return $mArray();
    };
}

/**
 * Присваивает значение в массив по точечной нотации. Возвращает копию массива, так как хз как обновлять по ссылке в цепочке коллбэков:(
 * @param string $keys
 * @param $value
 * @return Closure
 */
function set_by_dot_keys(string $keys, $value): Closure
{
    return function(array $array) use($keys, $value) {
        $keys = explode_dot($keys);
        $reference = &$array;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $reference)) {
                $reference[$key] = [];
            }
            $reference = &$reference[$key];
        }
        $reference = is_array($value) && count($value) === 1 ? m($value)(maybe_key(0, $value))() : $value;

        unset($reference);

        return $array;
    };
}