<?php
namespace Q8Intouch\Q8Query\Orderer;


use Illuminate\Database\Eloquent\Builder;
use Q8Intouch\Q8Query\Core\BaseQueryable;
use Q8Intouch\Q8Query\Core\Defaults;

class Orderer extends BaseQueryable
{

    /**
     * @return string
     */
    protected static function getKey()
    {
        return Defaults::keywordFromConfig('orderer');
    }

    protected static function getRegex()
    {
        return "/^\s*([a-zA-Z_]+[0-9a-zA-Z_])*,?\s*(desc|asc)?$/";
    }

    /**
     * @param $query Builder
     * @return Builder
     */
    public function order($query)
    {
        return $query->orderBy($this->attributes[0], $this->attributes[1] ?? 'asc');
    }
}