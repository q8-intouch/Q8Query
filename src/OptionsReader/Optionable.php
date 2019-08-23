<?php


namespace Q8Intouch\Q8Query\OptionsReader;


use ReflectionClass;
use ReflectionMethod;

interface Optionable
{
    /**
     * @param $class ReflectionClass
     * @param $method ReflectionMethod
     * @return bool
     */
    public function check($class, $method) : bool ;

    /**
     * @param $class ReflectionClass
     * @param $method ReflectionMethod
     * @return Option
     */
    public function getOptions($class, $method): Option;
}