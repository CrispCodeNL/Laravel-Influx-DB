<?php

namespace CrispCode\LaravelInfluxDB\Tests\Mocks;

class TestClass
{
    public function __construct(public readonly string $serialization)
    {
    }

    public function __toString(): string
    {
        return $this->serialization;
    }
}
