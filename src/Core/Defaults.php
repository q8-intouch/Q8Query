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
    ];

    /**
     * get default token by key because of the follows:
     * cant depend totally on config file as values can be deleted
     * these values are required for validation later
     * if a new token was added later i wont able to
     * force the user to update the config file
     * so all the token must fallback to theses function
     * @param $key
     * @return mixed
     */
    public static function getToken($key)
    {
        return static::$tokens[$key];
    }

}