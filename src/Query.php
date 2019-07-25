<?php


namespace Q8Intouch\Q8Query;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Q8Intouch\Q8Query\Core\ModelNotFoundException;
use Q8Intouch\Q8Query\Core\ParamsMalformedException;
use Q8Intouch\Q8Query\Core\Validator;

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
     */
    public function build()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this->attachQueriesFromParams($this->getModel()::query());

    }

    /**
     * build function has to be created first @see build
     * @return Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function get()
    {
        return $this->isSingleObjectPath()
            ? $this->builder->first()
            : $this->builder->get();
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
            foreach (config('q8-query.namespaces', []) as $prefix) {
                $model = $prefix . '\\' . $this->params[0];
                if (class_exists($model))
                    return $model;
            }
        else
            return $this->model;

        throw new ModelNotFoundException();
    }

    /**
     * attach queries according url segments
     *
     * @param $model Model
     * @return Model|Collection|string
     */
    public function attachQueriesFromParams($model)
    {
        for ($i = 1; $i < count($this->params); $i++) {
            $model = $this->updateQueryByParamSection($i, $model);
        }
        return $model instanceof Model ? $model : $this->analyzeOptionalParams($model);
    }

    protected function analyzeOptionalParams($model){
        // TODO
        // remove white lines
        // check parenthesis
        // parse model
        return $model->get();
    }

    /**
     * @param $index
     * @param $query Builder|Model
     * @return Builder
     */
    protected function updateQueryByParamSection($index, $query){
        return $index % 2 ?  $query->whereKey($this->params[$index])->first() : $query->{$this->params[$index]}();
    }


}