<?php

namespace Q8Intouch\Q8Query\Filterer\FilterMethods;


use Illuminate\Database\Eloquent\Builder;
use Q8Intouch\Q8Query\Core\Caller;
use Q8Intouch\Q8Query\Core\Defaults;
use Q8Intouch\Q8Query\Filterer\Expression;
use Q8Intouch\Q8Query\Filterer\Filterable;
use Q8Intouch\Q8Query\Filterer\Filterer;

class ScopeFilterer implements Filterable
{

    protected static $argsRegex = "/[^\(\)\s,\"']+|\"([^\"]*)\"|'([^']*)'/";

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
        $closure = Filterer::$logicalTokens[$expression->logical];

        // get scope
        $scope = $lexemes[1];

        /** @noinspection PhpUnhandledExceptionInspection */
        (new Caller($query))->authorizeCallOrThrow('scope' . ucfirst($scope));

        // get params if exists
        $args = $this->extractArgs($lexemes);
        $query->{$closure}(function ($query) use ($scope, $args) {
            call_user_func_array([$query, $scope], $args);
        });
        return $query;
    }

    protected function extractArgs($lexemes)
    {
        if (count($lexemes) >= 3 && preg_match_all(self::$argsRegex, $lexemes[2], $matches))
            return $matches[0];

        return [];
    }

}