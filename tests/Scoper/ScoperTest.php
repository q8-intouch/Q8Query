<?php
namespace Q8Intouch\Q8Query\Test\Scoper;


use Q8Intouch\Q8Query\Orderer\Orderer;
use Q8Intouch\Q8Query\Scoper\Scoper;
use ReflectionClass;
use Tests\TestCase;

class ScoperTest extends TestCase
{
    private $extractParamsFromString;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractParamsFromString = self::getMethod('extractFunctionAndArgs');
    }

    protected static function getMethod($name)
    {
        $class = new ReflectionClass(Scoper::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @dataProvider extractParamsFromStringProvider
     * @param $testCase
     * @param $testResult
     */
    public function testExtractParamsFromString($testCase, $testResult)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
         $this->assertEquals( Scoper::extractParamsFromString($testCase), $testResult);
    }

    public function extractParamsFromStringProvider()
    {
        return [
            ["fun(arg1)", ["fun(arg1)"]],
            ['fun(arg1, "string arg")', ['fun(arg1, "string arg")']],
            ['fun(arg11), fun2(arg21, arg22)', ['fun(arg11)', 'fun2(arg21, arg22)']],
            ['fun(arg11), fun2(arg21, arg22', ['fun(arg11)', 'fun2', 'arg21', 'arg22']],
            ['fun(arg11), fun2(arg21,arg22)', ['fun(arg11)', 'fun2(arg21,arg22)']],
            ['fun(arg11), fun2(arg21,arg22) fun3', ['fun(arg11)', 'fun2(arg21,arg22)', 'fun3']],
            ['fun(arg11), fun2(arg21,arg22) fun3()', ['fun(arg11)', 'fun2(arg21,arg22)', 'fun3()']],
            ['fun(arg11), fun2(arg21,arg22) fun3("string arg3")', ['fun(arg11)', 'fun2(arg21,arg22)', 'fun3("string arg3")']],
        ];
    }

    /**
     * @dataProvider extractFunctionAndArgsProvider
     * @param $testCase
     * @param $testResult
     */
    public function testExtractFunctionAndArgs($testCase, $testResult)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals($this->extractParamsFromString->invokeArgs(new Scoper([]), [$testCase]), $testResult);
    }

    public function extractFunctionAndArgsProvider()
    {
        return [
            ['fun', ['fun', []]],
            ['fun()', ['fun', []]],
            ['fun(1)', ['fun', ['1']]],
            ['fun(1, arg2)', ['fun', ['1', 'arg2']]],
            ['fun(1, arg2)', ['fun', ['1', 'arg2']]],
            ['fun("arg string", arg2)', ['fun', ['"arg string"', 'arg2']]],
        ];
    }

}