<?php

namespace Q8Intouch\Q8Query\OptionsReader\Methods;

use Illuminate\Database\Eloquent\Relations\Relation;
use Q8Intouch\Q8Query\OptionsReader\Option;
use Q8Intouch\Q8Query\OptionsReader\Optionable;

class RelationMethod implements Optionable
{
    protected $type;
    /**
     * @param $class \ReflectionClass
     * @param $method \ReflectionMethod
     * @return bool
     */
    public function check($class, $method): bool
    {
        return !$method->getNumberOfParameters()
            && $method->getReturnType()
            && is_a($method->getReturnType()->getName(), Relation::class, true) ; // && is_a($method->getReturnType(), Relation::class);
    }

    /**
     * @param $class \ReflectionClass
     * @param $method \ReflectionMethod
     * @return Option
     */
    public function getOptions($class, $method): Option
    {
        $option = new Option();
        $option->name = $method->getName();
        $option->type = "Relation: " . $method->getReturnType()->getName();
        $option->examples = [
            '/'. $class->getShortName() .'{id}/' . $method->getName(),
            '/' . $class->getShortName()  . '?' . config('q8-query.associator', 'associate') . '=' . $method->getName(),
            '/' . $class->getShortName()  . '/{id}?' . config('q8-query.associator', 'associate') . '=' . $method->getName()
        ];
        return $option;
    }
}