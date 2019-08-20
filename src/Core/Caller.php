<?php


namespace Q8Intouch\Q8Query\Core;


use DocBlockReader\Reader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Q8Intouch\Q8Query\Core\Exceptions\MethodNotAllowedException;

class Caller
{
    protected $reflection;
    protected $object;

    /**
     * Caller constructor.
     * @param $object
     * @throws \ReflectionException
     */
    public function __construct($object)
    {
        $this->object = $object;
        $class = $this->getModelClass($object);
        $this->reflection = new \ReflectionClass($class);
    }

    /**
     * @param $method
     * @param $returnType
     * @return mixed|null
     * @throws \ReflectionException
     * @throws MethodNotAllowedException
     */
    public function call($method, &$returnType = null)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $reader = new Reader($this->reflection->getName(), $method);
        // check for return/types && if not hidden before calling
        $returnType = $this->getReturnType($method, $reader);

        // fetch call mode
        $mode = config('q8-query.caller-mode', 'strict');
        // check if strict mode enabled
        if ( !$reader->getParameter('Hidden') && (($mode == 'strict' && $returnType) || $mode == 'loss')) {

            return $this->object->{$method}();
        }
        else
            throw new MethodNotAllowedException("Your are not authorized to call the following: {$method}");
    }


    /**
     * @param $eloquent Model|Builder
     * @return string
     */
    protected function getModelClass($eloquent)
    {
        return $eloquent instanceof Builder ? get_class($eloquent->getModel()) : get_class($eloquent);
    }

    /**
     * @param $method
     * @param $reader Reader
     * @return bool|mixed|null
     * @throws \ReflectionException
     */
    protected function getReturnType($method, $reader)
    {
        return $this->reflection->getMethod($method)->getReturnType() ?: $reader->getParameter('return');
    }
}