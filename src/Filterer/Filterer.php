<?php

namespace Q8Intouch\Q8Query\Filterer;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Q8Intouch\Q8Query\Core\Defaults;

class Filterer
{
    /**
     * @var Expression[]
     */
    private $expressions;

    /**
     * @var Validator
     */
    protected $validator;

    protected static $logicalTokens = [
        'or' => 'orWhere',
        'and' => 'where'
    ];

    protected $complexFilters = [
        'has' => 'filterByHas'
    ];

    /**
     * The following class is highly dependant on compiler and programming languages concepts
     * please consider the understanding of these concepts before proceeding with maintaining.
     * proceeding without enough knowledge may cause inconsistency and unintended crashes
     *
     * I am using the following references:
     *  1. Concepts of Programming Languages by Boston University (sec 2,3,4)
     *      1.1. free version can be found at: https://cs-people.bu.edu/lapets/320/
     *  2. How Parsers and Compilers Work by  Stephen Raymond Ferg
     *      2.1. article cn found at: http://parsingintro.sourceforge.net
     *
     * However the concepts used later are much more higher level i.e no need to iterate char by char
     *  as a higher methods can be used from  the native php implementation
     * @param $expressions Expression[]
     */

    public function __construct($expressions)
    {
        $this->expressions = $expressions;
        $this->validator = new Validator();
    }

    /**
     * create a filterer using request
     * if null is passed the filtered uses the globals
     *
     * @param Request|null $request
     * @return Filterer
     * @throws NoStringMatchesFound
     * @throws NoQueryParameterFound
     */
    public static function createFromRequest(Request $request = null)
    {
        if (!$request)
            $request = Request::createFromGlobals();
        if (!$request->has(config('filterer', 'filter'))) {
            throw new NoQueryParameterFound('Param: ' . config('filterer', 'filter') . " wasn't found");
        }
        return static::createFromString($request->query('filter'));
    }

    /**
     * create a filterer using a native string without the filter parameter
     * ex: name eq "some string" and id ne 1 or id eq 1
     * the prefix `filter=` is not needed and will cause an exception
     *
     * @param string $s
     * @return Filterer
     * @throws NoStringMatchesFound
     */
    public static function createFromString(string $s)
    {


        // change to a 2d array using the logic operators
        // turn into a
        $expressions = static::extractParamsFromString($s);
        return new Filterer($expressions);
    }

    /**
     * extract parameters as expression array from string where each row will contain a possible query
     * ex:
     *      input:  name eq "some string" and id ne 1 or id eq 1
     *      output: expression array
     *
     * grouping operators aren't yet supported
     *
     *
     *
     * @param string $s
     * @return array
     * @throws NoStringMatchesFound
     */
    protected static function extractParamsFromString(string $s)
    {
        $lexers = static::splitBySpaces($s);
        return static::splitByLogicalTokens($lexers);
    }

    /**
     *  split strings by empty spaces if not between quotes
     *
     * @param string $s
     * @return array
     * @throws NoStringMatchesFound
     */
    protected static function splitBySpaces(string $s)
    {

        if (!preg_match_all("/[^\s\"']+|\"([^\"]*)\"|'([^']*)'/", $s, $matches))
            throw new NoStringMatchesFound("No string delimiters found please check the docs");
        return $matches[0];
    }

    /**
     * convert 1d array to 2d array at the index of logical operators
     * @param $lexemes array
     * @return Expression[]
     */
    protected static function splitByLogicalTokens($lexemes)
    {
        $result = [];
        $start = 0;
        $nextLogical = 'and';

        // this loop is replaceable by ready made functions from php core like and str split, str index of
        // however after complexity analysis, the following loop has faster execution time
        for ($i = 0; $i < count($lexemes); $i++) {
            if ($token = static::isLogicalToken($lexemes[$i])) {
                $result[] = static::extractExpressionArray($lexemes, $nextLogical, $start, $i);
                $nextLogical = $token;
                $start = $i + 1;
            }
        }

        // do it one more time for the last element
        $result[] = static::extractExpressionArray($lexemes, $nextLogical, $start, $i);

        return $result;

    }

    /**
     * @param $lexemes array
     * @param $logical string
     * @param $start int
     * @param $end int
     * @return Expression
     */
    protected static function extractExpressionArray($lexemes, $logical, $start, $end)
    {
        return new Expression($logical, array_slice($lexemes, $start, $end - $start));
    }

    /**
     * check and return lexeme if it is a registered token
     *
     * @param $lexeme string
     * @return string|null
     */
    protected static function isLogicalToken($lexeme)
    {
        foreach (static::$logicalTokens as $token => $value)
            if ($lexeme == Defaults::tokenFromConfig($token))
                return $token;

        return null;

    }

    /**
     * @param $query Builder
     * @return mixed
     */
    public function filter($query)
    {
        if (!is_array($this->expressions)) {
            // throw
        }
        foreach ($this->expressions as $expression) {
            $lexemes = $expression->lexemes;
            if ($this->validator->validateComparisonRules($lexemes, $operator)) {
                $this->attachSimpleComparisonRule($query, $expression, $operator);
            } else if ($this->validator->validateComplexComparisonRules($lexemes, $operator)) {
                $this->{$this->complexFilters[$operator]}($query, $expression);
            }
            // else if check complex
//            else if ($validator)
            // else throw
        }
        $query->toSql();
        return $query;
    }

    /**
     * @param $expression Expression
     * @return mixed
     */
    protected function getClauseFromExpression($expression)
    {
        return static::$logicalTokens[$expression->logical];
    }

    /**
     * @param $query
     * @param $expression Expression
     * @param $operator
     */
    protected function attachSimpleComparisonRule($query, $expression, $operator)
    {
        // this will throw if a different comparison rule size was specified other than 3
        // TODO
        // update this to locate the token index and replace the lexeme with operator at the same index
        $table_name = $query->getModel()->getTable();
        $lexemes = $expression->lexemes;
        $this->updateValuesForSpecialCases($lexemes, $operator);
        $query->{$this->getClauseFromExpression($expression)}($table_name . '.' . $lexemes[0],
            // trim the single and double quotes
            // TODO
            // change this to a global replacer, so wont have to call this each time
            $operator, preg_replace("/('|\")/", "", $lexemes[2]));
    }

    /**
     * @param $lexemes array
     * @param $operator
     */
    protected function updateValuesForSpecialCases(&$lexemes, &$operator)
    {
        if ($operator == 'like') {
            $lexemes[2] = '%' . $lexemes[2] . '%';
        }
    }

    /**
     * @param $query Builder
     * @param $expression Expression
     */
    protected function filterByHas($query, $expression)
    {
        $lexemes = $expression->lexemes;
        if (count($lexemes) == 2) {
            $query->whereHas($lexemes[1]);
            return;
        }
        $subLexemes = array_slice($lexemes, 1);
        if ($this->validator->validateComparisonRules($subLexemes, $operator)) {

            $related = static::splitRelatedAndAttribute($lexemes[1]);
            // validate against basic rules
            $subLexemes[0] = $related[1];
            $query->{$this->getHasClosure($expression)}($related[0], function ($query) use ($related, $subLexemes, $operator) {
                $this->attachSimpleComparisonRule($query, new Expression('and', $subLexemes), $operator);
            });
        }
    }

    protected function getHasClosure($expression)
    {
        return [
            'and' => 'whereHas',
            'or' => 'orWhereHas',
        ][$expression->logical];
    }

    /**
     * @param $str string
     * @return array[]|false|string[]
     */
    protected static function splitRelatedAndAttribute($str)
    {
        // split at last occurrence
        return preg_split('/\.(?=[^\.]*$)/', $str);
    }

}