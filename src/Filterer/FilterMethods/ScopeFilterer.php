<?php

namespace Q8Intouch\Q8Query\Filterer\FilterMethods;


use Illuminate\Database\Eloquent\Builder;
use Q8Intouch\Q8Query\Core\Defaults;
use Q8Intouch\Q8Query\Core\Utils;
use Q8Intouch\Q8Query\Filterer\Expression;
use Q8Intouch\Q8Query\Filterer\Filterable;
use Q8Intouch\Q8Query\Filterer\Filterer;
use Q8Intouch\Q8Query\Filterer\Option;

class ScopeFilterer implements Filterable
{

    /**
     * @param Expression $expression
     * @return bool
     */
    public function validate($expression): bool
    {
        return count($expression->lexemes) > 1 && $expression->lexemes[0] == Defaults::tokenFromConfig('scope');
    }

    public function filter($query, $expression): Builder
    {
        $lexemes = $expression->lexemes;
//        if ()
        return $query;
    }

}