<?php

namespace Q8Intouch\Q8Query\Test\Filterer;

use Q8Intouch\Q8Query\Core\NoStringMatchesFound;
use Q8Intouch\Q8Query\Core\Utils;
use Q8Intouch\Q8Query\Filterer\Expression;
use Q8Intouch\Q8Query\Filterer\Filterer;
use Q8Intouch\Q8Query\Test\TestCase;
use ReflectionClass;

class FiltererTest extends TestCase
{

    private $splitBySpacesMethod;
    private $splitByLogicalTokensMethod;
    private $splitRelatedAndAttributeMethod;


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

    /**
     * @dataProvider splitBySpacesExceptionProvider
     * @param $testCase
     * @param $testResult
     */
    public function testSplitBySpacesException($testCase, $testResult)
    {
        $this->expectException($testResult);
        $this->splitBySpacesMethod->invokeArgs(null, [$testCase]);
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

    /**
     * @dataProvider splitRelatedAndAttributeProvider
     * @param $testCase
     * @param $testResult
     */
    public function testSplitRelatedAndAttribute($testCase, $testResult)
    {
        $this->assertEquals( Utils::splitRelatedAndAttribute($testCase), $testResult);
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
            [
                "tab\tcheck",
                ['tab', 'check']
            ],
            [
                "new line\ncheck",
                ['new', 'line', 'check']
            ],
            [
                "scope inTime('test string',1,int)",
                ['scope', "inTime", "'test string',1,int"]
            ],
        ];
    }

    public function splitBySpacesExceptionProvider()
    {
        return [
            ["", NoStringMatchesFound::class],
            [" ", NoStringMatchesFound::class],
            ["     ", NoStringMatchesFound::class],
            ["\n", NoStringMatchesFound::class],
            ["\t", NoStringMatchesFound::class],

        ];
    }

    public function splitByLogicalTokensProvider()
    {
        return [
            [
                ['normal', 'string', 'array'],
                [
                    new Expression("and", ['normal', 'string', 'array'])
                ]
            ],
            [
                ['"string and string"'],
                [
                    new Expression("and", ['"string and string"'])
                ],
            ],
            [
                ['"string"', 'and', 'another string'],
                [
                    new Expression("and", ['"string"']),
                    new Expression("and", ['another string'])
                ],
            ],
            [
                ['"string"', 'or', 'another', 'string'],
                [
                    new Expression("and", ['"string"']),
                    new Expression("or", ['another', 'string'])
                ],
            ],
            [
                ['"string"', 'or', 'and', 'another', 'string'],
                [
                    new Expression("and", ['"string"']),
                    new Expression("or", []),
                    new Expression("and", ['another', 'string'])
                ],
            ],
            [
                ['string', 'or', 'and'],
                [
                    new Expression("and", ['string']),
                    new Expression("or", []),
                    new Expression("and", [])
                ],
            ],
            [
                ['or', 'string'],
                [
                    new Expression("and", []),
                    new Expression("or", ['string'])
                ]
            ],
            [
                ['string', 'or', 'scope', '"string",12'],
                [
                    new Expression("and", ['string']),
                    new Expression("or", ['scope', '"string",12'])
                ]
            ],
            [
                ['string', 'and', 'scope','active', '"string",13'],
                [
                    new Expression("and", ['string']),
                    new Expression("and", ['scope', 'active', '"string",13'])
                ]
            ],
        ];
    }

    public function splitRelatedAndAttributeProvider()
    {
        return [
          ['user.name', ['user', 'name']],
          ['user.address.name', ['user.address', 'name']],
          ['user', ['user']],
          ['user.', ['user','']],
          ['user..', ['user.', '']],
          ['user.name.', ['user.name', '']],
        ];
    }
}