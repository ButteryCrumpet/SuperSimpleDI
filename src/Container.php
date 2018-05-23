<?php

namespace SuperSimpleDI;

use Psr\Container\ContainerInterface;
use Exceptions\NotInvokableException;
use Exceptions\ImmutableIdException;

class Container implements ContainerInterface
{
    private $services = array();
    private $mutable = array();
    private $factories = array();

    public function get($id)
    {
        if (isset($factories[$id])) {
            return $this->services[$id]($this);
        }

        if (\method_exists($callable, '__invoke')) {
            $this->services[$id] = $this->services[$id]($this);
        }
        return $this->services[$id];
    }

    public function has($id)
    {
        return isset($this->services[$id]);
    }

    public function register($id, $value, $mutable = true)
    {
        if (isset($this->immutable[$id])) {
            throw new ImmutableServiceException("The service: ${id} is immutable");
        }

        $this->services[$id] = $value;
        $this->mutable[$id] = $mutable;
    }

    public function registerFactory($id, $callable)
    {
        if (!\method_exists($callable, '__invoke')) {
            throw new NotInvokableException("Value must be a closure or invokable object");
        }

        if (!$this->mutable[$id]) {
            throw new ImmutableServiceException("The service: ${id} is immutable");
        }

        $this->factories[$id] = true;
        $this->services[$id] = $value;
    }
}
