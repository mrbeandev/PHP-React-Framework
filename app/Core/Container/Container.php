<?php

namespace App\Core\Container;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use RuntimeException;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, callable|string|null $concrete = null): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'singleton' => false,
        ];
    }

    public function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete ?? $abstract,
            'singleton' => true,
        ];
    }

    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function make(string $abstract): mixed
    {
        if (array_key_exists($abstract, $this->instances)) {
            return $this->instances[$abstract];
        }

        $binding = $this->bindings[$abstract] ?? null;
        $concrete = $binding['concrete'] ?? $abstract;

        $object = $this->resolveConcrete($concrete);

        if (($binding['singleton'] ?? false) === true) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    public function call(callable|array|string $target, array $parameters = []): mixed
    {
        if (is_string($target) && str_contains($target, '@')) {
            [$class, $method] = explode('@', $target, 2);
            $target = [$class, $method];
        }

        if (is_array($target) && count($target) === 2) {
            [$classOrInstance, $method] = $target;
            $instance = is_object($classOrInstance) ? $classOrInstance : $this->make($classOrInstance);
            $reflection = new ReflectionMethod($instance, $method);
            $args = $this->resolveArguments($reflection, $parameters);

            return $reflection->invokeArgs($instance, $args);
        }

        $reflection = new ReflectionFunction($target);
        $args = $this->resolveArguments($reflection, $parameters);

        return $reflection->invokeArgs($args);
    }

    private function resolveConcrete(callable|string $concrete): mixed
    {
        if (is_callable($concrete) && !is_string($concrete)) {
            return $concrete($this);
        }

        if (!is_string($concrete)) {
            throw new RuntimeException('Container binding must be a class name or closure.');
        }

        $reflection = new ReflectionClass($concrete);
        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $concrete();
        }

        $args = $this->resolveArguments($constructor, []);
        return $reflection->newInstanceArgs($args);
    }

    private function resolveArguments(ReflectionFunction|ReflectionMethod $reflection, array $parameters): array
    {
        $resolved = [];

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $parameters)) {
                $resolved[] = $parameters[$name];
                continue;
            }

            $type = $parameter->getType();
            if ($type !== null && !$type->isBuiltin()) {
                $typeName = $type->getName();

                foreach ($parameters as $provided) {
                    if (is_object($provided) && is_a($provided, $typeName)) {
                        $resolved[] = $provided;
                        continue 2;
                    }
                }

                $resolved[] = $this->make($typeName);
                continue;
            }

            if ($parameter->isDefaultValueAvailable()) {
                $resolved[] = $parameter->getDefaultValue();
                continue;
            }

            throw new RuntimeException('Unable to resolve parameter $' . $name . '.');
        }

        return $resolved;
    }
}
