<?php

namespace Q8Intouch\Q8Query\Test\Orderer;


use Q8Intouch\Q8Query\Core\NoStringMatchesFound;
use Q8Intouch\Q8Query\Filterer\Filterer;
use Q8Intouch\Q8Query\Orderer\Orderer;
use ReflectionClass;
use Tests\TestCase;

class OrdererTest extends TestCase
{
    private $extractParamsFromString;


    protected function setUp(): void
    {
        parent::setUp();
        $this->extractParamsFromString = self::getMethod('extractParamsFromString');
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass(Orderer::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @dataProvider extractParamsFromStringProvider
     * @param $testCase
     * @param $testResult
     */
    public function testExtractParamsFromString($testCase, $testResult)
    {
        $this->assertEquals($this->extractParamsFromString->invokeArgs(null, [$testCase]), $testResult);
    }

    /**
     * @dataProvider extractParamsFromStringProviderException
     * @param $testCase
     * @param $testResult
     */
    public function testExtractParamsFromStringException($testCase, $testResult)
    {
        $this->expectException($testResult);
        $this->assertEquals($this->extractParamsFromString->invokeArgs(null, [$testCase]), $testResult);
    }


    public function extractParamsFromStringProvider()
    {
        return [
            ['name, asc', ['name', 'asc']],
            ['name, desc', ['name', 'desc']],
            ['name, desc', ['name', 'desc']],
            ['name desc', ['name', 'desc']],
            [' name   desc', ['name', 'desc']],
            ['name          desc', ['name', 'desc']],
            ['name         ', ['name']],
            ['name', ['name']],
            [' name', ['name']],
            [' name1', ['name1']],
            ['_name2', ['_name2']],
        ];
    }

    public function extractParamsFromStringProviderException()
    {
        return [
            ['_name te', NoStringMatchesFound::class],
        ];
    }


}