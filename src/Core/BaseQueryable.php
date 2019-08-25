<?php


namespace Q8Intouch\Q8Query\Core;


use Illuminate\Http\Request;
use Q8Intouch\Q8Query\Selector\Selector;

abstract class BaseQueryable
{

    /**
     * @var array
     */
    protected $attributes;


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
        $key = static::getKey();
        if (!$request->has($key)) {
            throw new NoQueryParameterFound('Param: ' . $key . " wasn't found");
        }
        return static::createFromString($request->query($key));
    }

    /**
     * @return string
     */
    protected abstract static function getKey();

    /**
     * create a filterer using a native string without the filter parameter
     * ex: name eq "some string" and id ne 1 or id eq 1
     * the prefix `filter=` is not needed and will cause an exception
     *
     * @param string $s
     * @return static
     * @throws NoStringMatchesFound
     */
    public static function createFromString(string $s)
    {

        $related = static::extractParamsFromString($s);
        return new static($related);
    }

    /**
     * @param $s
     * @return mixed
     * @throws NoStringMatchesFound
     */
    public static function extractParamsFromString($s)
    {
        if (!preg_match(static::getRegex(), $s, $matches))
            throw new NoStringMatchesFound("No string delimiters found please check the docs");
        return array_slice($matches, 1);
    }

    protected abstract static function getRegex();

}