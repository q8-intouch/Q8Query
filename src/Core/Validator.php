<?php


namespace Q8Intouch\Q8Query\Core;


class Validator
{
    /**
     * use this as variable for future releases where fail reasons will be saved also
     *
     * @var bool
     */
    protected $validationResult = true;

    /**
     * - holds all the available regexes available for paths combinations
     * - Dont add delimiters it is added later dynamically
     * - multiple elements are using `or` operator I.e: if any of the elements matches, return true
     * - The pattern is as follows:
     *      1. string only as start (not numbers is included) followed with '/' then a number
     *      2. the pattern can be repeated n times infinity
     * @var array
     *
     */
    protected static $pathRegexes = [
      '^([a-zA-Z]+\/[0-9]+\/?)+([a-zA-Z]+)?$',
      '^[a-zA-Z]+$',
    ];

    /**
     * - These holds an array of repetitive pattern
     * - Dont add delimiters it is added later dynamically
     * - regex array are mapped to another params array @see validateParams
     * @var array
     */
    protected static $paramsRegexes = [
        '/^[a-zA-Z]+$/',
        '/^[0-9]+$/'
    ];

    /**
     * validate the string passed as URL if it is having an illegal format according to @see pathRegexes
     *
     * @param $path
     * @return $this
     */
    public function validatePath($path)
    {
        $regex = '/'. implode('|', static::$pathRegexes ) . '/';
        $this->validationResult = preg_match($regex, $path);
        return $this;

    }

    /**
     * validate the params according to @see paramsRegexes
     * - the params are mapped to regexes
     * - ex:
     *      1- [param1, param2, param3] && [reg1, reg2] are mapped as
     *          - param1 is validated using reg1
     *          - param2 is validated using reg2
     *          - param3 is validated using reg1 (repetitively)
     *
     * @param $params array
     * @return Validator
     */
    public function validateParams($params)
    {
        $regexes = static::$paramsRegexes;

        for ($i = 0; $i < count($params); $i++) {
           if(!preg_match($regexes[$i % count($regexes)], $params[$i]))
           {
               $this->validationResult = false;
               return $this;
           }

        }
        return $this;
    }

    public function getResult(){
        return $this->validationResult;
    }

    public function getMessage(){
        return "Pattern not matched";
    }
}