<?php

namespace SuperSimpleDI\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class ServiceNotFoundException extends \InvalidArgumentException implements ContainerExceptionInterface
{
}
