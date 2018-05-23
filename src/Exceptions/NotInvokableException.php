<?php

namespace SuperSimpleDI\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class NotInvokableException extends \InvalidArgumentException implements ContainerExceptionInterface
{
}
