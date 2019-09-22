<?php

namespace Q8Intouch\Q8Query\Core;


use DocBlockReader\Reader;
use Q8Intouch\Q8Query\Core\Exceptions\ModelNotFoundException;

class Utils
{
    /**
     * @param $str string
     * @return array[]|false|string[]
     */
    public static function splitRelatedAndAttribute($str)
    {
        // split at last occurrence
        return preg_split('/\.(?=[^\.]*$)/', $str);
    }

    /**
     * @param $modelName string
     * @return string
     * @throws ModelNotFoundException
     */
    public static function getModel($modelName)
    {
        foreach (config('q8-query.namespaces', []) as $prefix) {
            $model = $prefix . '\\' . $modelName;
            if (class_exists($model))
                return $model;
        }

        throw new ModelNotFoundException();
    }

    /**
     * @param $class \ReflectionClass
     * @param $method string
     * @param $reader Reader
     * @return bool|mixed|null
     * @throws \ReflectionException
     */
    public static function getReturnType($class, $method, $reader)
    {
        return $class->getMethod($method)->getReturnType() ?: $reader->getParameter('return');
    }
}