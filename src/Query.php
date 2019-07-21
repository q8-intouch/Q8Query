<?php


namespace Q8Intouch\Q8Query;


use Q8Intouch\Q8Query\Core\PathMalformedException;
use Q8Intouch\Q8Query\Core\Validator;

class Query
{

    /**
     * @var array
     */
    public $params;

    /**
     *
     * Query constructor.
     * @param $params array with the following pattern ['Model', {id}, ....]
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

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