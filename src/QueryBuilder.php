<?php


namespace Q8Intouch\Q8Query;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Q8Intouch\Q8Query\Associator\Associator;
use Q8Intouch\Q8Query\Core\Caller;
use Q8Intouch\Q8Query\Core\Exceptions\ModelInBuilderNotAllowedException;
use Q8Intouch\Q8Query\Core\ModelNotFoundException;
use Q8Intouch\Q8Query\Core\NoQueryParameterFound;
use Q8Intouch\Q8Query\Core\NoStringMatchesFound;
use Q8Intouch\Q8Query\Core\ParamsMalformedException;
use Q8Intouch\Q8Query\Core\Utils;
use Q8Intouch\Q8Query\Core\Validator;
use Q8Intouch\Q8Query\Filterer\Filterer;
use Q8Intouch\Q8Query\Orderer\Orderer;
use Q8Intouch\Q8Query\Selector\Selector;

class QueryBuilder
{
    /**
     * @var Model
     */
    private $query;

    /**
     * QueryBuilder constructor.
     * @noinspection PhpDocMissingThrowsInspection ModelInBuilderNotAllowedException
     * @param $params array with the following pattern ['Model', {id}, ....]
     * @throws Core\Exceptions\MethodNotAllowedException
     * @throws ModelNotFoundException
     * @throws ParamsMalformedException
     * @throws \ReflectionException
     */
    public function __construct($params)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->query = (new Query($params))->getModelQuery();
        // throw if the query is returned instead of model
        if ($this->query instanceof Model)
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new ModelInBuilderNotAllowedException(
                "Path with an '/{id}' suffix is not allowed. Models cant be called within builder.");

    }

    /**
     * @param $path
     * @return static
     * @throws Core\Exceptions\MethodNotAllowedException
     * @throws ModelNotFoundException
     * @throws ParamsMalformedException
     * @throws \ReflectionException
     */
    public static function QueryFromPathString($path)
    {
        $builder = new static(explode('/', $path));
        return $builder;
    }

    /**
     * @param $string
     * @return $this
     * @throws NoStringMatchesFound
     */
    public function filter($string)
    {
        Filterer::createFromString($string)->filter($this->query);
        return $this;
    }

    /**
     * @param $string
     * @return $this
     * @throws NoStringMatchesFound
     */
    public function order($string)
    {
        Orderer::createFromString($string)->order($this->query);
        return $this;
    }


    /**
     * @param $string
     * @return $this
     * @throws Core\Exceptions\MethodNotAllowedException
     * @throws NoStringMatchesFound
     * @throws \ReflectionException
     */
    public function associate($string)
    {
        Associator::createFromString($string)->associateBuilder(($this->query));
        return $this;
    }

    /**
     * @param $string
     * @return $this
     * @throws Core\Exceptions\MethodNotAllowedException
     * @throws NoStringMatchesFound
     * @throws \ReflectionException
     */
    public function select($string)
    {
        Selector::createFromString($string)->selectFromQuery(($this->query));
        return $this;
    }

    public function get($columns = ['*'])
    {
        return $this->query->get($columns);
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $this->query->paginate(
            $columns,
            $perPage,
            $columns,
            $pageName,
            $page);
    }
}