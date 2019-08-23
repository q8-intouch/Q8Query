<?php


namespace Q8Intouch\Q8Query\Core;


class Defaults
{

    protected static $tokens = [
        'or' => 'or',
        'and' => 'and',
        '='   => 'eq',
        '!='  => 'ne',
        '>'   => 'gt',
        '>='   => 'ge',
        '<'   => 'lt',
        '<='   => 'le',
        'has' => 'has',
        'like' => 'contain',
        'scope' => 'scope',
        'filterer' => 'filter',
    ];

    /**
     * get default token by key because of the follows:
     * cant depend totally on config file as values can be deleted
     * these values are required for validation later
     * if a new token was added later i wont able to
     * force the user to update the config file
     * so all the token must fallback to this function
     * @param $key
     * @return mixed
     */
    public static function getToken($key)
    {
        return  static::$tokens[$key];
    }

    public static function tokenFromConfig($key)
    {
        return config('q8-query.tokens.'. $key,  static::$tokens[$key]);
    }

    public  static $attributeRegex = "/^[a-zA-Z]+[0-9a-zA-Z]*$/";

    public static $valueRegex = '/^.*$/';

    public  static $nestableAttributeRegex = "/^[a-zA-Z_]+(\.[a-zA-Z_]*)*$/";

    public  static $relatedModelRegex = "/[a-zA-Z_\.]+/";
}