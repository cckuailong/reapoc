<?php

namespace RebelCode\Wpra\Core\Container;

use Dhii\Di\Exception\NotFoundException;
use Interop\Container\ContainerInterface;
use RebelCode\Wpra\Core\Modules\ModuleInterface;

/**
 * A container implementation specifically tailored for modules.
 *
 * @since 4.13
 */
class ModuleContainer implements ContainerInterface
{
    /**
     * The inner container.
     *
     * @since 4.13
     *
     * @var ContainerInterface
     */
    protected $inner;

    protected $definitions;

    protected $cache;

    protected $proxy;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param ModuleInterface         $module The module instance.
     * @param ContainerInterface|null $proxy  Optional container to pass to service definitions.
     */
    public function __construct(ModuleInterface $module, ContainerInterface $proxy = null)
    {
        $this->definitions = $this->compileModuleServices($module);
        $this->useProxy($proxy);
        $this->cache = [];
    }

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param ContainerInterface|null $proxy  Optional container to pass to service definitions.
     */
    public function useProxy(ContainerInterface $proxy = null)
    {
        $this->proxy = $proxy;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function get($id)
    {
        static $stack = [];

        // If no definition for the given ID, throw an exception
        if (!$this->has($id)) {
            $stackStr = implode("\n", $stack);
            throw new NotFoundException(
                sprintf(__('Service "%s" was not found; stack: %s', 'wprss'), $id, "\n$stackStr")
            );
        }

        $stack[] = $id;

        try {
            // Invoke the definition and save the service in cache, if needed
            if (!array_key_exists($id, $this->cache)) {
                $container = ($this->proxy === null) ? $this : $this->proxy;
                $this->cache[$id] = call_user_func_array($this->definitions[$id], [$container]);
            }
        } finally {
            array_pop($stack);
        }

        return $this->cache[$id];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function has($id)
    {
        return array_key_exists($id, $this->definitions);
    }

    /**
     * Compiles the module's service definitions.
     *
     * @since 4.13
     *
     * @param ModuleInterface $module The module instance.
     *
     * @return callable[] The service definitions.
     */
    protected function compileModuleServices(ModuleInterface $module)
    {
        $factories = $module->getFactories();
        $extensions = $module->getExtensions();

        // Compile the factories and extensions into a flat definitions list
        $definitions = [];
        foreach ($factories as $key => $definition) {
            // Merge factory with its extension, if an extension exists
            if (array_key_exists($key, $extensions)) {
                $extension = $extensions[$key];
                $definition = function (ContainerInterface $c) use ($definition, $extension) {
                    return $extension($c, $definition($c));
                };
            }
            // Add to definitions
            $definitions[$key] = $definition;
        }

        return $definitions;
    }
}
