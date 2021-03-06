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

    /**
     * Get a registered service
     * If service is callable will resolve the call and replace
     * the service value with the returned value unless it was
     * registered as a factory
     * 
     * @param string $id
     * @return mixed $service
     * 
     * @throws ServiceNotFoundException If service does not exist
     */
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

    /**
     * Check if a service is registered
     * 
     * @param string $id
     * @return boolean $service_exists
     */
    public function has($id)
    {
        return isset($this->services[$id]);
    }

    /**
     * Register a standard service.
     * 
     * @param string $id
     * @param mixed $value
     * @param boolean $immutable = false
     * @return null
     */
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

    /**
     * Register a factory service
     * Factory services will not be resolved on only the first
     * time it is requested, it will the callable every time it is requested
     * 
     * @param string $id
     * @param Callable $callable
     * @return null
     */
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
