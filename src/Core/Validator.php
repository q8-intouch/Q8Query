<?php


namespace Q8Intouch\Q8Query\Core;


class Validator
{
    /**
     * use this as variable for future releases where fail reasons will be saved also
     *
     * @var bool
     */
    private $validationResult = true;

    /**
     * - holds all the available regexes available for paths combinations
     * - Dont add delimiters it is added later dynamically
     * - The pattern is as follows:
     *      1. string only as start (not numbers is included) followed with '/' then a number
     *      2. the pattern can be repeated n times infinity
     * @var array
     *
     */
    private static $pathRegexes = [
      '^([a-zA-Z]+\/[0-9]+\/?)+([a-zA-Z]+)?$',
      '^[a-zA-Z]+$',
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
        $this->validationResult = preg_match($regex, $path, $matches);
        return $this;

    }

    public function getResult(){
        return $this->validationResult;
    }

    public function getMessage(){
        return "Pattern not matched";
    }
}