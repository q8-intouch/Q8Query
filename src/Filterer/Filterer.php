<?php

namespace Q8Intouch\Q8Query\Filterer;


use Illuminate\Http\Request;

class Filterer
{


    protected static $logicalTokens = [
        'or',
        'and'
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
     *
     */

    public function __construct()
    {
    }

    /**
     * create a filterer using request
     * if null is passed the filtered uses the globals
     *
     * @param Request|null $request
     * @return Filterer
     * @throws NoStringMatchesFound
     */
    public static function createFromRequest(Request $request = null)
    {
        if (!$request)
           $request = Request::createFromGlobals();
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
    public static function createFromString(string $s){


        // change to a 2d array using the logic operators
        // turn into a
        $params = static::extractParamsFromString($s);
        return new Filterer();
    }

    /**
     * extract parameter 2d array from string where each row will contain a possible query
     * ex:
     *      input:  name eq "some string" and id ne 1 or id eq 1
     *      output:
     *              [
     *                  [
     *                      logic = ""
     *                      expr = ["name",    "eq",    "some string"],
     *                  ],
     *                  [
     *                      logic = "and"
     *                      expr = ["id",    "ne",    1],
     *                  ],
     *                  [
     *                      logic = "or"
     *                      expr = ["id",    "eq",    1],
     *                  ],
     *              ]
     *
     * grouping operators aren't yet supported
     *
     *
     *
     * @param string $s
     * @return array
     * @throws NoStringMatchesFound
     */
    protected static function extractParamsFromString(string $s){
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
     * @return array
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
                $result[] = static::extractExpressionArray($lexemes, $token, $start, $i);
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
     * @return array
     */
    protected static function extractExpressionArray($lexemes, $logical, $start, $end){
        return [
            'logical' => $logical,
            'expr' => array_slice($lexemes, $start, $end - $start)
        ];
    }

    /**
     * check and return lexeme if it is a registered token
     *
     * @param $lexeme string
     * @return string|null
     */
    protected static function isLogicalToken($lexeme)
    {
        foreach (static::$logicalTokens as $key => $token)
            if ($lexeme == config('q8-query.tokens.' . $token))
                return $key;

        return null;

    }
}