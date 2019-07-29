<?php


namespace Q8Intouch\Q8Query\Filterer;


class Expression
{
    public $logical;
    public $lexemes;

    /**
     * Expression constructor.
     * @param $logical
     * @param $lexemes
     */
    public function __construct($logical, $lexemes)
    {
        $this->logical = $logical;
        $this->lexemes = $lexemes;
    }


}