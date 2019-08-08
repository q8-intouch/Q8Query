<?php


namespace Q8Intouch\Q8Query\OptionsReader;


interface Optionable
{
    public function check($class, $method) : bool ;
    public function getOptions($class, $method): Option;
}