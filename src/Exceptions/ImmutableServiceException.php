<?php

namespace SuperSimpleDI\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class ImmutableIdException extends \InvalidArgumentException implements ContainerExceptionInterface
{
}
