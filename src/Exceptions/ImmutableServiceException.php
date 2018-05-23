<?php

namespace SuperSimpleDI\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class ImmutableServiceException extends \InvalidArgumentException implements ContainerExceptionInterface
{
}
