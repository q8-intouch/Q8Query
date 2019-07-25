<?php


namespace Q8Intouch\Q8Query\Filterer;


class Expression
{
    public $logical;
    public $expression;

    /**
     * Expression constructor.
     * @param $logical
     * @param $expression
     */
    public function __construct($logical, $expression)
    {
        $this->logical = $logical;
        $this->expression = $expression;
    }


}