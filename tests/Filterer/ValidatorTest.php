<?php

namespace Q8Intouch\Q8Query\Test\Filterer;

use Q8Intouch\Q8Query\Core\Defaults;
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
    /**
     * @dataProvider validateComplexComparisonRulesProvider
     * @param $lexemes
     * @param $result
     * @param $token
     */
    public function testValidateComplexComparisonRules($lexemes, $result, $token)
    {
        self::assertEquals((new Validator())->validateComplexComparisonRules($lexemes, $resultToken), $result);
        self::assertEquals($resultToken, $token);
    }

    public function validateComparisonRulesProvider()
    {
        return [
            [
                ['name', Defaults::getToken('='), 'testName'],
                true,
                '='
            ],
            [
                ['age', Defaults::getToken('<'), '5'],
                true,
                '<'
            ],
            [
                ['age', Defaults::getToken('<'), '5', "test extra"],
                false,
                null
            ],
            [
                ['test less', Defaults::getToken('<')],
                false,
                null
            ],
            [
                [Defaults::getToken('<')],
                false,
                null
            ],
            [
                ['single item'],
                false,
                null
            ],
            [
                ['two', 'items'],
                false,
                null
            ],
            [
                ['test', 'three', 'items'],
                false,
                null
            ],
            [
                [],
                false,
                null
            ],
            [
                ['test', 'extra', Defaults::getToken('>=')],
                false,
                null
            ],
            [
                ['test', 'extra', Defaults::getToken('=')],
                false,
                null
            ],
            [
                [Defaults::getToken('='), 'test', 'extra'],
                false,
                null
            ],
            [
                ['test', Defaults::getToken('='), 'multiple spaces'],
                true,
                '='
            ],
            [
                ['test', Defaults::getToken('='), '"quoted spaces"'],
                true,
                '='
            ],
            [
                ['test', Defaults::getToken('=')],
                false,
                null
            ],
        ];
    }

    public function validateComplexComparisonRulesProvider()
    {
        return [
            [
                [Defaults::getToken('has'), 'addresses.name'],
                true,
                'has'
            ],
            [
                [Defaults::getToken('has'), 'addresses.name', 'eq', 'name'],
                true,
                'has'
            ],
            [
                ['text', 'addresses.name', 'eq', 'name'],
                false,
                null
            ],
            [
                [Defaults::getToken('has')],
                false,
                null
            ],
            [
                [],
                false,
                null
            ],
            [
                [Defaults::getToken('has'), 'name', 'eq', ],
                true,
                'has'
            ],
        ];
    }
}