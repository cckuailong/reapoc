<?php

namespace GeminiLabs\SiteReviews;

use Closure;
use Exception;
use GeminiLabs\SiteReviews\Exceptions\BindingResolutionException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

abstract class Container
{
    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * @var array
     */
    protected $buildStack = [];

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var array[]
     */
    protected $with = [];

    /**
     * @param string $alias
     * @param mixed $concrete
     * @return void
     */
    public function alias($alias, $concrete)
    {
        $this->instances[$alias] = $concrete;
    }

    /**
     * @param string $abstract
     * @param mixed $concrete
     * @param bool $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        $this->dropStaleInstances($abstract);
        $concrete = Helper::ifTrue(is_null($concrete), $abstract, $concrete);
        if (!$concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * @param mixed $abstract
     * @return mixed
     */
    public function make($abstract, array $parameters = [])
    {
        if (is_string($abstract) && !class_exists($abstract)) {
            $alias = __NAMESPACE__.'\\'.Str::removePrefix($abstract, __NAMESPACE__);
            $abstract = Helper::ifTrue(class_exists($alias), $alias, $abstract);
        }
        return $this->resolve($abstract, $parameters);
    }

    /**
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * @param Closure|string $concrete
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function construct($concrete)
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $this->getLastParameterOverride()); // probably a bound closure
        }
        try {
            $reflector = new ReflectionClass($concrete); // class or classname provided
        } catch (ReflectionException $e) {
            throw new BindingResolutionException("Target class [$concrete] does not exist.", 0, $e);
        }
        if (!$reflector->isInstantiable()) {
            $this->throwNotInstantiable($concrete); // not an instantiable class
        }
        $this->buildStack[] = $concrete;
        if (is_null($constructor = $reflector->getConstructor())) {
            array_pop($this->buildStack);
            return new $concrete(); // class has no __construct
        }
        try {
            $instances = $this->resolveDependencies($constructor->getParameters()); // resolve class dependencies
        } catch (BindingResolutionException $e) {
            array_pop($this->buildStack);
            throw $e;
        }
        array_pop($this->buildStack);
        return $reflector->newInstanceArgs($instances); // return a new class
    }

    /**
     * @param string $abstract
     * @return void
     */
    protected function dropStaleInstances($abstract)
    {
        unset($this->instances[$abstract]);
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return null|\ReflectionClass|\ReflectionNamedType|\ReflectionType
     */
    protected function getClass($parameter)
    {
        if (version_compare(phpversion(), '8', '<')) {
            return $parameter->getClass(); // @compat PHP < 8
        }
        return $parameter->getType();
    }

    /**
     * @param string $abstract
     * @param string $concrete
     * @return Closure
     */
    protected function getClosure($abstract, $concrete)
    {
        return function ($container, $parameters = []) use ($abstract, $concrete) {
            return $abstract == $concrete
                ? $container->construct($concrete)
                : $container->resolve($concrete, $parameters);
        };
    }

    /**
     * @param string $abstract
     * @return mixed
     */
    protected function getConcrete($abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }
        return $abstract;
    }

    /**
     * @return array
     */
    protected function getLastParameterOverride()
    {
        return Arr::consolidate(end($this->with));
    }

    /**
     * @param ReflectionParameter $dependency
     * @return mixed
     */
    protected function getParameterOverride($dependency)
    {
        return $this->getLastParameterOverride()[$dependency->name];
    }

    /**
     * @param ReflectionParameter $dependency
     * @return bool
     */
    protected function hasParameterOverride($dependency)
    {
        return array_key_exists($dependency->name, $this->getLastParameterOverride());
    }

    /**
     * @param mixed $concrete
     * @param string $abstract
     * @return bool
     */
    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    /**
     * @param string $abstract
     * @return bool
     */
    protected function isShared($abstract)
    {
        return isset($this->instances[$abstract]) || !empty($this->bindings[$abstract]['shared']);
    }

    /**
     * @param mixed $abstract
     * @param array $parameters
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function resolve($abstract, $parameters = [])
    {
        if (isset($this->instances[$abstract]) && empty($parameters)) {
            return $this->instances[$abstract]; // return an existing singleton
        }
        $this->with[] = $parameters;
        $concrete = $this->getConcrete($abstract);
        $object = Helper::ifTrue($this->isBuildable($concrete, $abstract),
            function () use ($concrete) { return $this->construct($concrete); },
            function () use ($concrete) { return $this->make($concrete); }
        );
        if ($this->isShared($abstract) && empty($parameters)) {
            $this->instances[$abstract] = $object; // store as a singleton
        }
        array_pop($this->with);
        return $object;
    }

    /**
     * Resolve a class based dependency from the container.
     * @return mixed
     * @throws Exception
     */
    protected function resolveClass(ReflectionParameter $parameter)
    {
        try {
            return $this->make($this->getClass($parameter)->getName());
        } catch (Exception $error) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }
            throw $error;
        }
    }

    /**
     * @return array
     */
    protected function resolveDependencies(array $dependencies)
    {
        $results = [];
        foreach ($dependencies as $dependency) {
            if ($this->hasParameterOverride($dependency)) {
                $results[] = $this->getParameterOverride($dependency);
                continue;
            }
            $results[] = Helper::ifTrue(is_null($this->getClass($dependency)),
                function () use ($dependency) { return $this->resolvePrimitive($dependency); },
                function () use ($dependency) { return $this->resolveClass($dependency); }
            );
        }
        return $results;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function resolvePrimitive(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        $this->throwUnresolvablePrimitive($parameter);
    }

    /**
     * @param string $concrete
     * @return void
     * @throws BindingResolutionException
     */
    protected function throwNotInstantiable($concrete)
    {
        if (empty($this->buildStack)) {
            $message = "Target [$concrete] is not instantiable.";
        } else {
            $previous = implode(', ', $this->buildStack);
            $message = "Target [$concrete] is not instantiable while building [$previous].";
        }
        throw new BindingResolutionException($message);
    }

    /**
     * @param ReflectionParameter $parameter
     * @return void
     * @throws BindingResolutionException
     */
    protected function throwUnresolvablePrimitive(ReflectionParameter $parameter)
    {
        throw new BindingResolutionException("Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}");
    }
}
