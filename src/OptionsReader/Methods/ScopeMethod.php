<?php


namespace Q8Intouch\Q8Query\OptionsReader\Methods;


use DocBlockReader\Reader;
use Illuminate\Support\Str;
use Q8Intouch\Q8Query\Core\Defaults;
use Q8Intouch\Q8Query\Core\Utils;
use Q8Intouch\Q8Query\OptionsReader\Option;
use Q8Intouch\Q8Query\OptionsReader\Optionable;

class ScopeMethod implements Optionable
{
    private $returnType;
    public function check($class, $method): bool
    {
        $reader = new Reader($class->getName(), $method->getName());

        // check for return/types && if not hidden before calling
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->returnType = Utils::getReturnType($class ,$method->getName(), $reader);

        $mode = config('q8-query.caller-mode', 'strict');

        return Str::startsWith($method->getName(), 'scope')
            && !$reader->getParameter('Hidden')
            && (($mode == 'strict' && $this->returnType)
                || $mode == 'loss');
    }

    public function getOptions($class, $method): Option
    {
        // scopeIsActive -> isActive
        $methodName = lcfirst(substr($method->getName(), 5 /* len of 'scope' */ ));
        $option = new Option();
        $option->name = $methodName;
        $option->type = "Filterer Method";
        $option->examples = [
            '/' . $class->getShortName()  . '?' .Defaults::tokenFromConfig('filterer')
                .'='. Defaults::tokenFromConfig('scope') . ' ' .$methodName . '(...args)'  ,
        ];
        
        $params = $method->getParameters();

        for ($i = 1; $i < count($params); $i++) {
            $option->args[] =  $this->getArgument($params[$i]);
        }
        return $option;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @param $arg \ReflectionParameter
     * @return string
     */
    private function getArgument($arg)
    {
        $string = $arg->getName();
        if ($arg->isDefaultValueAvailable())
            /** @noinspection PhpUnhandledExceptionInspection */
            $string = $string . '=' . $arg->getDefaultValue();
            return $string;
    }
}