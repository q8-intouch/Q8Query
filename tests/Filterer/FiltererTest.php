<?php

namespace Q8Intouch\Q8Query\Test\Filterer;

use Q8Intouch\Q8Query\Filterer\Filterer;
use Q8Intouch\Q8Query\Test\TestCase;
use ReflectionClass;

class FiltererTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();

    }


    /**
     * @dataProvider splitBySpacesProvider
     * @param $testCase
     * @param $testResult
     */
    public function testSplitBySpaces($testCase, $testResult)
    {
        $method = self::getMethod("splitBySpaces");
        $this->assertEquals($method->invokeArgs(null, [$testCase]), $testResult);

    }


    public function splitBySpacesProvider()
    {
        return [
            [
                "test" => 'name eq "some string"',
                "actual" => ['name', 'eq', '"some string"']
            ],
            [
                "test" => "name2 eq 'some string'",
                "actual" => ['name2', 'eq', "'some string'"]
            ],
            [
                "test" => 'name eq \'some word',
                "actual" => ['name', 'eq', 'some', 'word']
            ],
            [
                "test" => 'name eq "some string" and "another"',
                "actual" => ['name', 'eq', '"some string"', 'and', '"another"']
            ],
            [
                "test" => '   some      extra     spaces       ',
                "actual" => ['some', 'extra', 'spaces']
            ],
            [
                "test" => 'escape quote "test \' and \'"',
                "actual" => ['escape', 'quote', '"test \' and \'"']
            ],
            [
                "test" => 'no"spaces"',
                "actual" => ['no', '"spaces"']
            ],
        ];
    }


    protected static function getMethod($name)
    {
        $class = new ReflectionClass(Filterer::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}