<?php
namespace Q8Intouch\Q8Query\Core;


class Utils
{
    /**
     * @param $str string
     * @return array[]|false|string[]
     */
    public static function splitRelatedAndAttribute($str)
    {
        // split at last occurrence
        return preg_split('/\.(?=[^\.]*$)/', $str);
    }
}