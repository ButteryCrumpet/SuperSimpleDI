<?php

use PHPUnit\Framework\TestCase;
use SuperSimpleDI\Container;
use SuperSimpleDI\Exceptions\NotInvokableException;

class ContainerTest extends TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = new Container;
    }

    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            Container::class,
            $this->container
        );
    }

    public function testCanRegisterGetScalar()
    {
        $service = "this is a service";
        $this->container->register("service1", $service);
        $this->assertEquals($service, $this->container->get("service1"));
    }

    public function testCanRegisterGetCallable()
    {
        $out = "this is a callable service";
        $this->container->register("service2", function ($container) use ($out) {
            return $out;
        });
        $this->assertEquals($out, $this->container->get("service2"));
    }

    public function testCanRegisterGetFactory()
    {
        $this->container->registerFactory("factory1", function ($container) {
            if ($this->container->has("test")) {
                return $this->container->get("test");
            }
            $this->container->register("test", "new val");
            return "old val";
        });
        $this->assertEquals("old val", $this->container->get("factory1"));
        $this->assertEquals("new val", $this->container->get("factory1"));
    }

    /**
     * @expectedException SuperSimpleDI\Exceptions\NotInvokableException
     */
    public function testRegisterFactoryRejectsNonCallable()
    {
        $this->container->registerFactory("factory", "phpinfo");
    }

    /**
     * @expectedException SuperSimpleDI\Exceptions\ImmutableServiceException
     */
    public function testImmutableException()
    {
        $this->container->register("immutableService", "an immutable service", true);
        $this->container->register("immutableService", "it cannot be mutated");
    }
}
