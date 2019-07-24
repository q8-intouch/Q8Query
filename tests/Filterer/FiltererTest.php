<?php

namespace Q8Intouch\Q8Query\Test\Filterer;

use Q8Intouch\Q8Query\Filterer\Filterer;
use Q8Intouch\Q8Query\Test\TestCase;
use ReflectionClass;

class FiltererTest extends TestCase
{

    private $splitBySpacesMethod;
    private $splitByLogicalTokensMedthod;


    protected function setUp(): void
    {
        parent::setUp();
        $this->splitBySpacesMethod = self::getMethod('splitBySpaces');
        $this->splitByLogicalTokensMedthod = self::getMethod('$splitByLogicalTokens');
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
        
    }

    public function splitByLogicalTokensProvider()
    {
        return [];
    }

}