<?php


namespace Q8Intouch\Q8Query;


use Q8Intouch\Q8Query\Core\PathMalformedException;
use Q8Intouch\Q8Query\Core\Validator;

class Query
{
    public $params;


    /**
     * @param $path
     * @return Query
     * @throws PathMalformedException
     */
    public static function QueryFromPathString($path)
    {
        $validator = (new Validator())->validatePath($path);
        if (!$validator->getResult())
            throw new PathMalformedException($validator->getMessage());

        $query = new static();
        $query->params = explode('/', $path);
        return $query;
    }


}