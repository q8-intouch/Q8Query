<?php

namespace Q8Intouch\Q8Query\Test\Filterer;

use Q8Intouch\Q8Query\Core\Defaults;
use Q8Intouch\Q8Query\Filterer\FilterMethods\HasFilterer;
use Q8Intouch\Q8Query\Filterer\Validator;
use Q8Intouch\Q8Query\Test\TestCase;

class ValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @dataProvider validateComparisonRulesProvider
     * @param $lexemes
     * @param $result
     * @param $token
     */
    public function testValidateComparisonRules($lexemes, $result, $token)
    {
        self::assertEquals((new Validator())->validateComparisonRules($lexemes, $resultToken), $result);
        self::assertEquals($resultToken, $token);
    }
}