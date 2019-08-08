<?php

namespace Q8Intouch\Q8Query\OptionsReader;

use Q8Intouch\Q8Query\Core\ModelNotFoundException;
use Q8Intouch\Q8Query\Core\Utils;
use Q8Intouch\Q8Query\OptionsReader\Methods\RelationMethod;
use ReflectionClass;
use ReflectionMethod;

class OptionsReader
{
    protected $model;

    /**
     * @var Optionable[]
     */
    protected $methodsSchemas;

    public function __construct($model)
    {
        $this->model = $model;
        $this->registerMethods();
    }

    /**
     * @param $modelName string
     * @return OptionsReader
     * @throws ModelNotFoundException
     */
    public static function createFromModelString($modelName){
       return new static(Utils::getModel($modelName));
    }

    public function extractOptions()
    {
        $class = new ReflectionClass($this->model);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        $options = [];
        foreach ($methods as $method)
        {
            foreach ($this->methodsSchemas as $methodSchema)
            {
                if($methodSchema->check($class, $method)){
                    $options[] = $methodSchema->getOptions($class, $method);
                }
            }
        }

        return $options;
    }

    protected function registerMethods()
    {
        $this->methodsSchemas[] = new RelationMethod();
    }

}