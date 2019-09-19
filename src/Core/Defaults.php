<?php


namespace Q8Intouch\Q8Query\Core;


class Defaults
{

    public static function tokenFromConfig($key)
    {
        return config('q8-query.tokens.'. $key);
    }
    public static function keywordFromConfig($key)
    {
        return config('q8-query.'. $key);
    }

    public  static $attributeRegex = "/^[a-zA-Z]+[0-9a-zA-Z]*$/";

    public static $valueRegex = '/^.*$/';

    public  static $nestableAttributeRegex = "/^[a-zA-Z_]+(\.[a-zA-Z_]*)*$/";

    public  static $relatedModelRegex = "/[a-zA-Z_\.]+/";
}