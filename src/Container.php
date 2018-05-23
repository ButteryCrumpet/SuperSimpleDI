<?php

namespace SuperSimpleDI;

use Psr\Container\ContainerInterface;
use SuperSimpleDI\Exceptions\NotInvokableException;
use SuperSimpleDI\Exceptions\ImmutableServiceException;
use SuperSimpleDI\Exceptions\ServiceNotFoundException;

class Container implements ContainerInterface
{
    private $services = array();
    private $mutable = array();
    private $factories = array();

    public function get($id)
    {
        if (!isset($this->services[$id])) {
            throw new ServiceNotFoundException("The service: ${id} does not exist");
        }

        if (isset($this->factories[$id])) {
            return $this->services[$id]($this);
        }

        if (\method_exists($this->services[$id], '__invoke')) {
            $this->services[$id] = $this->services[$id]($this);
        }
        return $this->services[$id];
    }

    public function has($id)
    {
        return isset($this->services[$id]);
    }

    public function register($id, $value, $immutable = false)
    {
        if (isset($this->mutable[$id])) {
            if (!$this->mutable[$id]) {
                throw new ImmutableServiceException("The service: ${id} is immutable");
            }
        }

        $this->services[$id] = $value;
        $this->mutable[$id] = !$immutable;
    }

    public function registerFactory($id, $callable)
    {
        if (!\method_exists($callable, '__invoke')) {
            throw new NotInvokableException("Value must be a closure or invokable object");
        }

        if (isset($this->mutable[$id])) {
            if (!$this->mutable[$id]) {
                throw new ImmutableServiceException("The service: ${id} is immutable");
            }
        }

        $this->factories[$id] = true;
        $this->services[$id] = $callable;
    }
}
