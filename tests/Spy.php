<?php

namespace CrispCode\LaravelInfluxDB\Tests;

use ReflectionClass;
use ReflectionProperty;

class Spy
{
    public readonly ReflectionClass $clsRef;
    public readonly ReflectionProperty $propRef;
    public readonly mixed $propVal;

    private array $callbacks = [];

    public function __construct(public readonly object $observed, public readonly string $property)
    {
        $this->clsRef = new ReflectionClass($this->observed);
        $this->propRef = $this->clsRef->getProperty($this->property);
        $this->propVal = $this->propRef->getValue($this->observed);
        $this->propRef->setValue($this->observed, $this);
    }

    public function intercept(string $method, callable $callback)
    {
        $this->callbacks[$method] = $callback;
    }

    public function __call(string $name, array $arguments)
    {
        if (array_key_exists($name, $this->callbacks)) {
            return $this->callbacks[$name](...$arguments);
        } else {
            return $this->propVal->$name(...$arguments);
        }
    }
}
