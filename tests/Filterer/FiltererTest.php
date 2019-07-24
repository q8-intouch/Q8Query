<?php

namespace Q8Intouch\Q8Query\Test\Filterer;

use Q8Intouch\Q8Query\Filterer\Filterer;
use Q8Intouch\Q8Query\Test\TestCase;
use ReflectionClass;

class FiltererTest extends TestCase
{

    private $splitBySpacesMethod;
    private $splitByLogicalTokensMethod;


    protected function setUp(): void
    {
        parent::setUp();
        $this->splitBySpacesMethod = self::getMethod('splitBySpaces');
        $this->splitByLogicalTokensMethod = self::getMethod('splitByLogicalTokens');
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass(Filterer::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }


    /**
     * @dataProvider splitBySpacesProvider
     * @param $testCase
     * @param $testResult
     */
    public function testSplitBySpaces($testCase, $testResult)
    {
        $this->assertEquals($this->splitBySpacesMethod->invokeArgs(null, [$testCase]), $testResult);
    }

    public function splitBySpacesProvider()
    {
        return [
            [
                'name eq "some string"',
                ['name', 'eq', '"some string"']
            ],
            [
                "name2 eq 'some string'",
                ['name2', 'eq', "'some string'"]
            ],
            [
                'name eq \'some word',
                ['name', 'eq', 'some', 'word']
            ],
            [
                'name eq "some string" and "another"',
                ['name', 'eq', '"some string"', 'and', '"another"']
            ],
            [
                '   some      extra     spaces       ',
                ['some', 'extra', 'spaces']
            ],
            [
                'escape quote "test \' and \'"',
                ['escape', 'quote', '"test \' and \'"']
            ],
            [
                'no"spaces"',
                ['no', '"spaces"']
            ],
        ];
    }

    /**
     * @dataProvider splitByLogicalTokensProvider
     * @param $testCase
     * @param $testResult
     */
    public function testSplitByLogicalTokens($testCase, $testResult)
    {
        $this->assertEquals($this->splitByLogicalTokensMethod->invokeArgs(null, [$testCase]), $testResult);
    }

    public function splitByLogicalTokensProvider()
    {
        return [
            [
                ['normal', 'string', 'array'],
                [
                    [
                        "logical" => "and",
                        "expr" => ['normal', 'string', 'array']
                    ],
                ]
            ],
            [
                ['"string and string"'],
                [
                    [
                        "logical" => "and",
                        "expr" => ['"string and string"']
                    ],
                ]
            ],
            [
                ['"string"', 'and', 'another string'],
                [
                    [
                        "logical" => "and",
                        "expr" => ['"string"']
                    ],
                    [
                        "logical" => "and",
                        "expr" => ['another string']
                    ],
                ]
            ],
            [
                ['"string"', 'or', 'another', 'string'],
                [
                    [
                        "logical" => "and",
                        "expr" => ['"string"']
                    ],
                    [
                        "logical" => "or",
                        "expr" => ['another', 'string']
                    ],
                ]
            ],
            [
                ['"string"', 'or', 'and', 'another', 'string'],
                [
                    [
                        "logical" => "and",
                        "expr" => ['"string"']
                    ],
                    [
                        "logical" => "or",
                        "expr" => []
                    ],
                    [
                        "logical" => "and",
                        "expr" => ['another', 'string']
                    ],
                ]
            ],
            [
                ['string', 'or', 'and'],
                [
                    [
                        "logical" => "and",
                        "expr" => ['string']
                    ],
                    [
                        "logical" => "or",
                        "expr" => []
                    ],[
                        "logical" => "and",
                        "expr" => []
                    ],
                ]
            ],
            [
                ['or', 'string'],
                [
                    [
                        "logical" => "and",
                        "expr" => []
                    ],
                    [
                        "logical" => "or",
                        "expr" => ['string']
                    ]
                ]
            ],
        ];
    }

}