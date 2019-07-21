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
     * holds all the available regexes available for paths combinations
     * Dont use delimiters it is added later dynamically
     *
     * @var array
     *
     */
    private static $pathRegexes = [
      '^([a-zA-Z]+\/[0-9]+\/?)+([a-zA-Z]+)?$',
      '^[a-zA-Z]+$',
    ];

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