<?php

namespace Aventura\Wprss\Core\Component;

use Aventura\Wprss\Core;
use Dhii\Di\FactoryInterface;

/**
 * Helper component for things related to the backend.
 *
 * @since 4.10
 */
class AdminHelper extends Core\Plugin\ComponentAbstract
{
    /**
     * The factory used by this instance to create services.
     *
     * @since 4.11
     *
     * @var FactoryInterface
     */
    protected $factory;

    public function __construct($data, FactoryInterface $factory) {
        parent::__construct($data);
        $this->_setFactory($factory);
    }

    /**
     * Determine if currently showing page is related to WPRSS.
     *
     * @since 4.10
     *
     * @return bool True if currently showing a WPRSS-related page; false otherwise.
     */
    public function isWprssPage()
    {
        require_once(WPRSS_INC . 'functions.php');
        require_once(WPRSS_INC . 'admin-ajax-notice.php');

        return wprss_is_wprss_page();
    }

    /**
     * Creates a new instance of a Command.
     *
     * A command is a callable object that can contain all data necessary to invoke a callback.
     *
     * @since 4.10
     *
     * @param array|callable $data A callable, or an array with the follwing indices:
     *  - `function` - The callable to assign to the command;
     *  - `args` - An array of arguments to invoke the command with.
     *
     * @return Core\Model\Command
     */
    public function createCommand($data)
    {
        $cmd = new Core\Model\Command($data);

        return $cmd;
    }

    /**
     * Resolves a value to something concrete.
     *
     * If the value is a callable, calls it. Otherwise, returns value.
     *
     * @since 4.10
     *
     * @param mixed $value Anything.
     *
     * @return mixed A non-callable value.
     */
    public function resolveValue($value)
    {
        if (is_callable($value)) {
            $value = call_user_func_array($value, array());
        }

        return $value;
    }

    /**
     * Resolves a value accounting for case of it producing output.
     *
     * @since 4.11
     *
     * @param mixed $value The value to resolve.
     *
     * @return mixed|string The resolved value, if not null.
     *  Otherwise, output produced during resolution.
     */
    public function resolveValueOutput($value)
    {
        ob_start();
        $value = $this->resolveValue($value);
        $output = ob_get_clean();

        return is_null($value)
                ? $output
                : $value;
    }

    /**
     * Computes a hash of a given callable.
     *
     * @since 4.11
     *
     * @param callable $callable The callable to hash.
     *
     * @throws \InvalidArgumentException If not a valid callable.
     *
     * @return string A hash of the callable.
     */
    public function hashCallable($callable)
    {
        if (\is_object($callable) && \is_callable($callable)) {
            return $this->hashObject($callable);
        }

        if (\is_array($callable) && \is_callable($callable)) {
            if (\is_object($callable[0])) {
                $callable[0] = \get_class($callable);
            }

            return $this->hashArray($callable);
        }

        if (\is_string($callable) && \is_callable($callable)) {
            return $this->hashScalar($callable);
        }

        throw new \InvalidArgumentException('Could not hash: not a valid callback');
    }

    /**
     * Computes a hash of the array.
     *
     * Accounts for nested arrays.
     *
     * @since 4.11
     *
     * @param array $array The array to hash.
     *
     * @return string A hash of the array.
     */
    public function hashArray(array $array)
    {
        $itemHashes = array();
        foreach ($array as $_idx => $_item) {
            if (\is_array($_item)) {
                $itemHashes[$_idx] = $this->hashArray($_item);
            } elseif (\is_object($_item)) {
                $itemHashes[$_idx] = $this->hashObject($_item);
            } elseif (\is_resource($_item)) {
                $itemHashes[$_idx] = (string) $_item;
            } else {
                $itemHashes[$_idx] = $_item;
            }
        }

        $itemHashes = \serialize($itemHashes);

        return $this->hashScalar($itemHashes);
    }

    /**
     * Computes a hash of an object.
     *
     * The same object will always have the same hash.
     * Different identical objects will produce different results.
     *
     * @since 4.11
     *
     * @param object $object The object to hash.
     *
     * @return string A hash of the object.
     */
    public function hashObject($object)
    {
        return \spl_object_hash($object);
    }

    /**
     * Computes a hash of a scalar value.
     *
     * @since 4.11
     *
     * @param string|int|float|bool $value The value to hash.
     *
     * @return string A hash of the scalar value.
     */
    public function hashScalar($value)
    {
        return \sha1($value);
    }

    /**
     * Creates a new admin notificiation instance.
     *
     * @since 4.11
     *
     * @param array $data The data for the notice.
     * @return NoticeInterface The new notice.
     */
    public function createNotice($data)
    {
        return $this->_getFactory()->make($this->_pn('generic_fallback'), $data);
    }

    /**
     * Retrieves the factory used by this instance.
     *
     * @since 4.11
     *
     * @return FactoryInterface The factory instance.
     */
    protected function _getFactory()
    {
        return $this->factory;
    }

    /**
     * Assigns the factory to be used by this instance.
     *
     * @since 4.11
     *
     * @param FactoryInterface $factory The factory instance..
     *
     * @return $this
     */
    protected function _setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Retrieve the prefix used for notice services retrieved by this instance.
     *
     * @since 4.11
     *
     * @param string $name The service ID to prefix, if any.
     *
     * @return string The prefix, or prefixed ID.
     */
    protected function _pn($name = null)
    {
        $prefix = $this->getData('notice_service_id_prefix');
        return static::stringHadPrefix($name)
            ? $name
            : "{$prefix}{$name}";
    }

    /**
     * Retrieve the prefix used for services retrieved by this instance.
     *
     * @since 4.11
     *
     * @param string $name The service ID to prefix, if any.
     *
     * @return string The prefix, or prefixed ID.
     */
    protected function _p($name = null)
    {
        $prefix = $this->getData('service_id_prefix');
        return static::stringHadPrefix($name)
            ? $name
            : "{$prefix}{$name}";
    }
}
