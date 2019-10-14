<?php
namespace Q8Intouch\Q8Query\Scoper;

use Q8Intouch\Q8Query\Core\BaseQueryable;
use Q8Intouch\Q8Query\Core\Caller;
use Q8Intouch\Q8Query\Core\Defaults;
use Q8Intouch\Q8Query\Core\Exceptions\NoStringMatchesFound;

class Scoper extends BaseQueryable
{

    protected $argsRegex= "/[^\s\"'\(\),]+|\"([^\"]*)\"|'([^']*)'/";

    /**
     * @return string
     */
    protected static function getKey()
    {
        return Defaults::keywordFromConfig('scoper');
    }

    /**
     * @param $s
     * @return mixed
     * @throws NoStringMatchesFound
     */
    public static function extractParamsFromString($s)
    {
        if (!preg_match_all(static::getRegex(), $s, $matches))
            throw new NoStringMatchesFound("No string delimiters found please check the docs");
        return $matches[0];
    }

    protected static function getRegex()
    {
        return "/[a-zA-Z_]+[0-9a-zA-Z_]*(\([^\(\)]*\))?/";
    }

    public function scope($query){
        foreach ($this->attributes as $attribute)
        {
            [$closure, $args] = self::extractFunctionAndArgs($attribute);

            /** @noinspection PhpUnhandledExceptionInspection */
            (new Caller($query))->authorizeCallOrThrow('scope' . ucfirst($closure));

//            call_user_func_array([$query, $closure], $args);
            $query->$closure();
            $query->toSql();
        }
    }

    protected function extractFunctionAndArgs($function)
    {
        preg_match_all($this->argsRegex, $function, $matches);
        return [$matches[0][0], count($matches[0]) > 1 ? array_slice($matches[0],1) : []];
    }



}