<?php

namespace Q8Intouch\Q8Query\Selector;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Q8Intouch\Q8Query\Core\Caller;
use Q8Intouch\Q8Query\Core\Defaults;
use Q8Intouch\Q8Query\Core\Exceptions\NoQueryParameterFound;
use Q8Intouch\Q8Query\Core\Exceptions\NoStringMatchesFound;
use Q8Intouch\Q8Query\Core\Utils;

class Selector
{

    /**
     * @var array
     */
    private $attributes;


    /**
     * Associator constructor.
     * @param $attributes
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * create a filterer using request
     * if null is passed the filtered uses the globals
     *
     * @param Request|null $request
     * @return Selector
     * @throws NoQueryParameterFound
     * @throws NoStringMatchesFound
     */
    public static function createFromRequest(Request $request = null)
    {
        if (!$request)
            $request = Request::createFromGlobals();
        $associatorKey = static::getKey();
        if (!$request->has($associatorKey)) {
            throw new NoQueryParameterFound('Param: ' . $associatorKey . " wasn't found");
        }
        return static::createFromString($request->query($associatorKey));
    }

    protected static function getKey()
    {
        return config('q8-query.selector', 'select');
    }

    /**
     * create a filterer using a native string without the filter parameter
     * ex: name eq "some string" and id ne 1 or id eq 1
     * the prefix `filter=` is not needed and will cause an exception
     *
     * @param string $s
     * @return Selector
     * @throws NoStringMatchesFound
     */
    public static function createFromString(string $s)
    {

        $related = static::extractParamsFromString($s);
        return new Selector($related);
    }

    /**
     * @param $s
     * @return mixed
     * @throws NoStringMatchesFound
     */
    public static function extractParamsFromString($s)
    {
        if (!preg_match_all(Defaults::$relatedModelRegex, $s, $matches))
            throw new NoStringMatchesFound("No string delimiters found please check the docs");
        return $matches[0];
    }


    /**
     * @param $query Builder
     * @throws \Q8Intouch\Q8Query\Core\Exceptions\MethodNotAllowedException
     * @throws \ReflectionException
     */
    public function selectFromQuery($query)
    {


        [$directAttributes, $relatedAttributes] = $this->getDirectAndRelatedAttributes();

        // select direct related attributes
        $query->select($directAttributes);

        // select from related attributes
        $this->selectRelatedAttributes($query, $relatedAttributes);
    }

    /**
     * @param $query Builder
     * @param $related array
     * @throws \ReflectionException
     * @throws \Q8Intouch\Q8Query\Core\Exceptions\MethodNotAllowedException
     */
    protected function selectRelatedAttributes($query, $related)
    {
        foreach ($related as $relation => $columns)
            if ((new Caller($query))->authorizeCallOrThrow($relation))
            $query->with([$relation => function ($query) use ($columns)  {
                $query->select($columns);
            }]);
    }

    /**
     * @return array
     */
    protected function getDirectAndRelatedAttributes()
    {
        $directAttributes = [];
        $relatedAttributes = [];
        foreach ($this->attributes as $attribute) {
            // check if a related select
            if (str_contains($attribute, '.')) {
                [$key, $value] = Utils::splitRelatedAndAttribute($attribute);
                $relatedAttributes[$key][] = $value;
            }
            else
                $directAttributes[] = $attribute;
        }

        return [$directAttributes, $relatedAttributes];
    }

    /**
     * @param $model Model
     * @throws \Q8Intouch\Q8Query\Core\Exceptions\MethodNotAllowedException
     * @throws \ReflectionException
     */
    public function selectFromModel($model)
    {
        [$directAttributes, $relatedAttributes] = $this->getDirectAndRelatedAttributes();

        // hide all attributes that aren't included within the select query
        $model->makeHidden( array_diff(array_keys($model->getAttributes()), $directAttributes));

        $this->loadRelated($model, $relatedAttributes);
    }

    /**
     * @param $model
     * @param $relatedAttributes
     * @throws \Q8Intouch\Q8Query\Core\Exceptions\MethodNotAllowedException
     * @throws \ReflectionException
     */
    protected function loadRelated($model, $relatedAttributes)
    {
        foreach ($relatedAttributes as $relation => $columns)
            if ((new Caller($model))->authorizeCallOrThrow($relation))
            $model->load([$relation => function ($query) use ($columns)  {
                $query->select($columns);
            }]);
    }
}