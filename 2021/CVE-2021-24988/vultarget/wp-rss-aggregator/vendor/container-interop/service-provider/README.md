# Standard service providers

This project tries to find a solution for cross-framework modules (aka bundles) through **standard container configuration**. It is part of the [container-interop](https://github.com/container-interop/container-interop) group.

**Work in progress:** the project is currently experimental and is being tried in frameworks, containers and modules until considered viable. Until a 1.0.0 release the code in this repository is not stable. Expect changes breaking backward compatibility between minor versions (0.1.x -> 0.2.x).

[![Gitter chat](https://badges.gitter.im/container-interop/definition-interop.png)](https://gitter.im/container-interop/definition-interop)

## Background

Three main alternatives were identified to standardize container configuration:

- standard PHP objects/interfaces representing container definitions
- standard container configuration format (e.g. XML, …)
- standard service providers

The first solution that container-interop members tried to implement was [a set of standard PHP interfaces for container definitions](https://github.com/container-interop/definition-interop). While this solution is working, it has a few limitations and it is complicated to explain, understand and use.

There were then discussions about a standard configuration format (for example in XML), which has the advantage of being slightly easier to understand and use for module developers. This work has not be formalized yet because of the amount of work needed. This approach would also suffers from a few of the limitations identified in the first approach. It would also requires the inclusion in the standard of many specific features: the standard must define many different ways for how objects can be created and dependencies injected. That makes the standard complex to define, and would force all containers (even simple ones) to support all the features.

This repository contains a proposition for **standard service providers** (service providers are PHP components that provide container entries). This approach has turned out to be simpler on many level:

- the standard is much simpler, which means it is easier to explain and understand
- it is easier to use as it relies on plain old PHP code
- it is easier to implement support in containers

## Usage

To declare a service provider, simply implement the `ServiceProvider` interface.

```php
use Interop\Container\ServiceProvider;

class MyServiceProvider implements ServiceProvider
{
    public function getServices()
    {
        return [
            'my_service' => function(ContainerInterface $container, callable $getPrevious = null) {
                $dependency = $container->get('my_other_service');
                return new MyService($dependency);
            }
        ];
    }
}
```

The `getServices()` method must return a list of all container entries the service provider wishes to register:

- the key is the entry name
- the value is a callable that will return the entry, aka the **factory**

Factories have the following signature:

```php
function(ContainerInterface $container, callable $getPrevious = null)
```

Factories accept the following parameters:

- the container (instance of `Interop\Container\ContainerInterface`)
- a callable that returns the previous entry if overriding a previous entry, or `null` if not

The only difference between defining an entry from scratch or overriding/extending a previous entry is that the `$getPrevious` parameter will be either a `callable` or `null`. Factories are free to *use it or ignore it* if it's not `null`.

If you know you will not be using the `$container` parameter or the `$getPrevious` parameter, you can omit them:

```php
    function() {
        return new MyService();
    }
```

Each factory is responsible for returning a given entry of the container. Nothing should be cached by service providers, this is the responsibility of the container.

### Values (aka parameters)

A service provider can provide PHP objects (services) as well as any value. Simply return the value you wish from factory methods.

### Aliases

To alias a container entry to another, you can get the aliased entry from the container and return it:

```php
class MyServiceProvider implements ServiceProvider
{
    public function getServices()
    {
        return [
            'my_service' => [ MyServiceProvider::class, 'createMyService' ],
            'alias' => [ MyServiceProvider::class, 'resolveAlias' ],
        ];
    }
    
    // ...
    
    public static function resolveAlias(ContainerInterface $container)
    {
        return $container->get('my_service');
    }
}
```

### Entry overriding

Overriding an entry defined in another service provider is as easy as defining it again.

Module A:

```php
class A implements ServiceProvider
{
    public function getServices()
    {
        return [
            'foo' => [ A::class,  'getFoo' ],
        ];
    }
    
    public static function getFoo()
    {
        return 'abc';
    }
}
```

Module B:

```php
class B implements ServiceProvider
{
    public function getServices()
    {
        return [
            'foo' => [ B::class, 'getFoo' ],
        ];
    }
    
    public static function getFoo()
    {
        return 'def';
    }
}
```

If you register the service providers in the correct order in your container (A first, then B), then the entry `foo` will be `'def'` because B's definition will override A's.

### Entry extension

Extending an entry before it is returned by the container is very similar to overriding it.

Module A:

```php
class A implements ServiceProvider
{
    public function getServices()
    {
        return [
            'logger' => [ A::class, 'getLogger' ],
        ];
    }
    
    public static function getLogger()
    {
        return new Logger;
    }
}
```

Module B:

```php
class B implements ServiceProvider
{
    public function getServices()
    {
        return [
            'logger' => [ B::class, 'getLogger' ],
        ];
    }
    
    public static function getLogger(ContainerInterface $container, callable $getPrevious = null)
    {
        // Get the previous entry
        $previous = $getPrevious();

        // Register a new log handler
        $previous->addHandler(new SyslogHandler());
    
        // Return the object that we modified
        return $previous;
    }
}
```

If you register the service providers in the correct order in your container (A first, then B), the logger will be first created by `A` then a new handler will be registered on it by `B`.

## Compatible projects
### Projects consuming *service providers*

- [Laravel service provider bridge](https://github.com/thecodingmachine/laravel-universal-service-provider/): Use container-interop's service-providers into any [Laravel](http://laravel.com/) application.
- [Simplex](https://github.com/mnapoli/simplex): A [Pimple 3](https://github.com/silexphp/Pimple) fork with full [container-interop](https://github.com/container-interop/container-interop) compliance and cross-framework service-provider support.
- [Service provider bridge bundle](https://github.com/thecodingmachine/service-provider-bridge-bundle): Use container-interop's service-providers into a Symfony container.
- [Yaco](https://github.com/thecodingmachine/yaco): A compiler that generates container-interop compliant containers. Yaco can consume service-providers.

### Packages providing *service providers*

- [DBAL Module](https://github.com/thecodingmachine/dbal-universal-module): A module integrating [Doctrine DBAL](http://www.doctrine-project.org/projects/dbal.html) in an application using a service provider.
- [Glide Module](https://github.com/mnapoli/glide-module): A module integrating Glide in an application using a service provider.
- [Stratigility Module](https://github.com/thecodingmachine/stratigility-harmony): A service provider for the Stratigility PSR-7 middleware.
- [Whoops PSR-7 Middleware Module](https://github.com/thecodingmachine/whoops-middleware-universal-module): a service provider for the [Whoops](https://filp.github.io/whoops/) [PSR-7 middleware](https://github.com/franzliedke/whoops-middleware).

## Best practices

### Managing configuration

The service created by a factory should only depend on the input parameters of the factory (`$container` and `$getPrevious`).
If the factory needs to fetch parameters, those should be fetched from the container directly.

```php
class MyServiceProvider implements ServiceProvider
{
    public function getServices()
    {
        return [
            'logger' => [ MyServiceProvider::class, 'createLogger' ],
        ];
    }
    
    public static function createLogger(ContainerInterface $container)
    {
        // The path to the log file is fetched from the container, not from the service provider state.
        return new FileLogger($this->container->get('logFilePath'));
    }
}
```

## FAQ

### Why inject a callable instead of the previous entry directly in factories?

In a first version, service provider factories received the previous entry directly as a second parameter:

```php
    public static function getMyService(ContainerInterface $container, $previous = null)
    {
        // ...
    }
```

That caused 2 problems:

- it was inefficient since it caused the container to resolve all the previous entries that might exist, even when they were overridden by another service provider
- when the entry name was a class name, autowiring containers would try to resolve the previous entry using autowiring: when some parameters could not be resolved by the container, there would be exceptions

By injecting a callable that returns the previous entry, that makes it *lazily loaded*. That is both more efficient and avoids most problems with autowiring containers.

For a more detailed explanation you can read the full discussion in the [issue #9](https://github.com/container-interop/service-provider/issues/9).

### Why does the service provider not configure the container instead of returning entries?

Service providers usually take a container and configure it (e.g. in Pimple). The problem is that it requires the container to expose methods for configuration. That's an impossible requirement in a standard because all containers have a different API for configuration and they could never be made to implement the same.

These service providers provide factories for each container entry it provides. They do not require configuration methods on containers, so they can be made compatible with all/most of them. Each container entry is, in the end, just a callable to invoke, which most containers can do.

## Puli integration

The Puli integration is completely optional and not required to use this standard. It is only here to facilitate usage with Puli.

This package provides a [Puli *binding type*](http://docs.puli.io/en/latest/discovery/getting-started.html): `container-interop/service-provider`. Modules using Puli and implementing this standard can register service providers (fully qualified class names) through this binding type.

This way, frameworks or applications based on Puli can discover service providers automatically.

To register your service provider, simply use Puli's `bind` command:

```sh
puli bind --class Acme\\Foo\\MyServiceProvider container-interop/service-provider
```
