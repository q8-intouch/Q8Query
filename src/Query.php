<?php


namespace Q8Intouch\Q8Query;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Q8Intouch\Q8Query\Associator\Associator;
use Q8Intouch\Q8Query\Core\ModelNotFoundException;
use Q8Intouch\Q8Query\Core\NoQueryParameterFound;
use Q8Intouch\Q8Query\Core\NoStringMatchesFound;
use Q8Intouch\Q8Query\Core\ParamsMalformedException;
use Q8Intouch\Q8Query\Core\Utils;
use Q8Intouch\Q8Query\Core\Validator;
use Q8Intouch\Q8Query\Filterer\Filterer;
use Q8Intouch\Q8Query\Selector\Selector;

class Query
{

    /**
     * @var array
     */
    public $params;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $model;

    /**
     *
     * Query constructor.
     * @param $params array with the following pattern ['Model', {id}, ....]
     * @throws ParamsMalformedException
     */
    public function __construct($params)
    {
        $this->validator = new Validator();

        $this->validator->validateParams($params);

        if (!$this->validator->getResult() || !count($params))
            throw new ParamsMalformedException($this->validator->getMessage());
        $this->params = $params;
    }

    /**
     * @param $path
     * @return Query
     * @throws ParamsMalformedException
     */
    public static function QueryFromPathString($path)
    {
        $query = new static(explode('/', $path));
        return $query;
    }

    /**
     * @return mixed
     */
    public function getValidator()
    {
        return $this->validator;
    }


    /**
     * @return Model|Collection
     * @throws ModelNotFoundException
     * @throws NoStringMatchesFound
     * @throws NoQueryParameterFound
     */
    public function build()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->attachQueriesFromParams($this->getModel()::query());

    }

    /**
     * build function has to be created first
     * @return Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|object|null
     * @throws ModelNotFoundException
     * @throws NoQueryParameterFound
     * @throws NoStringMatchesFound
     * @see build
     */
    public function get()
    {
        return
            $this->postFetchOperations(
                $this->fetchIfBuilder(
                    $this->build()
                )
            );
    }

    /**
     *
     * @return boolean
     */
    public function isSingleObjectPath()
    {
        return !(count($this->params) % 2);
    }

    /**
     * @return string
     * @throws ModelNotFoundException
     */
    public function getModel()
    {
        if (!$this->model)
            return Utils::getModel($this->params[0]);
        else
            return $this->model;
    }

    /**
     * attach queries according url segments
     *
     * @param $model Model
     * @return Model|Collection|string
     * @throws NoStringMatchesFound
     * @throws NoQueryParameterFound
     */
    public function attachQueriesFromParams($model)
    {
        for ($i = 1; $i < count($this->params); $i++) {
            $model = $this->updateQueryByParamSection($i, $model);
        }
        $this->prefetchOperations($model);

        return $model;
    }


    /**
     * @param $index
     * @param $query Builder|Model
     * @return Builder
     */
    protected function updateQueryByParamSection($index, $query)
    {
        return $index % 2 ? $query->whereKey($this->params[$index])->first() : $query->{$this->params[$index]}();
    }

    /**
     * @param $eloquent
     * @throws NoStringMatchesFound
     */
    protected function prefetchOperations($eloquent)
    {
        try {
            if ($eloquent instanceof Model) {
                Selector::createFromRequest()->selectFromModel($eloquent);
                Associator::createFromRequest()->associateModel($eloquent);
            } else {

                Filterer::createFromRequest()->filter($eloquent);
                Associator::createFromRequest()->associateBuilder($eloquent);
                Selector::createFromRequest()->selectFromQuery($eloquent);
            }
        } catch (NoQueryParameterFound $e) {
        }
    }

    /**
     * @param Model|array $model
     * @return array|Model
     */
    protected function postFetchOperations($model)
    {
        return $model;
    }

    /**
     * @param Builder|Model $model
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model
     */
    protected function fetchIfBuilder($model)
    {
        return $model instanceof Model ? $model : $model->get();
    }


}