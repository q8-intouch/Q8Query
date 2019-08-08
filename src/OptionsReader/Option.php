<?php


namespace Q8Intouch\Q8Query\OptionsReader;


class Option
{

    /**
     * @var string
     */
    public $name;

    /**
     * type: attribute:string, relation:manyToMany, ..etc
     * @var string
     */
    public $type;

    /**
     * read from annotation for extra info
     * @var string
     */
    public $description;

    /**
     * @var array
     */
    public $examples;

    /**
     * @var array
     */
    public $extra;
}