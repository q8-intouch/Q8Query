<?php


namespace Q8Intouch\Q8Query\Filterer;


use Q8Intouch\Q8Query\Core\Defaults;

class Validator
{

    private $comparisonRules;
    private $complexComparisonRules;

    public function __construct()
    {
        $this->comparisonRules = static::getComparisonRules();
        $this->complexComparisonRules = static::getComplexComparisonRules();

    }

    /**
     * rules that are strict and will be validated using size
     * @return array
     */
    protected static function getComparisonRules()
    {
        $operators = ['=', '!=', '>', '>=', '<', '<=', 'like'];

        // each operator having it own rule for further customization if needed
        $rules = [];
        foreach ($operators as $operator)
                $rules[$operator] = [
                    Defaults::$nestableAttributeRegex,
                   '/^' . Defaults::tokenFromConfig($operator) . '$/',
                    Defaults::$valueRegex
                ];

        return $rules;
    }

    /**
     * rules that aren't having a certain pattern or rule
     * no specific size, can consist of a sub simple rule, or can have multiple valid rules
     * customized rules can be added by specifying the function name without the get prefix
     * ex: to validate against function named getComparisonRules add '{ComparisonRules}' as
     * the following
     * @return array
     */
    protected static function getComplexComparisonRules(){
        return [
            'has' => [
                [
                    '{token}',
                    Defaults::$nestableAttributeRegex
                ],
                [
                    '{token}',
                    '{ComparisonRules}' // a comparison rule can be inserted here
                ],

            ]
        ];
    }

    /**
     * @param $lexemes array
     * @param $token
     * @return bool
     */
    public function validateComparisonRules($lexemes, &$token = null )
    {
        $rules = $this->comparisonRules;
        foreach ($rules as $operator => $ruleArray)
        {
            // check if the validation rule size is the same as
            if (count($ruleArray) == count($lexemes))
            {
                // assume the validity of the rules
                $isValid = true;

                // check if rules apply to the full array
                for ($i = 0; $i < count($ruleArray); $i++) {
                    // set validity to false if not
                   if (!preg_match($ruleArray[$i], $lexemes[$i]))
                        $isValid = false;
                }
                // check if still valid
                if ($isValid)
                {
                    $token = $operator;
                    return true;
                }

            }

        }

        return false;
    }
}