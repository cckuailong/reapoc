<?php

namespace Dhii\Di;

use Dhii\Di\Exception\ContainerException;
use Dhii\Di\Exception\NotFoundException;
use Exception;
use Interop\Container\ContainerInterface as BaseContainerInterface;
use Interop\Container\ServiceProvider as BaseServiceProviderInterface;

/**
 * This container accepts a parent instance, which cannot be changed from external objects.
 *
 * @since 0.1
 */
class ContainerWithImmutableParent extends AbstractParentAwareContainer implements ParentAwareContainerInterface
{
    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param BaseServiceProviderInterface $definitions Service definitions to add to this container.
     * @param BaseContainerInterface       $parent      The container, which is to become this container's parent.
     */
    public function __construct(BaseServiceProviderInterface $definitions = null, BaseContainerInterface $parent = null)
    {
        if (!is_null($definitions)) {
            $this->_set($definitions);
        }

        $this->_setParentContainer($parent);
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getParentContainer()
    {
        return $this->_getParentContainer();
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function get($id)
    {
        return $this->_get($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function has($id)
    {
        return $this->_has($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     *
     * @return NotFoundException The new exception instance.
     */
    protected function _createNotFoundException($message, $code = 0, Exception $innerException = null)
    {
        return new NotFoundException($message, $code, $innerException);
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     *
     * @return ContainerException The new exception instance.
     */
    protected function _createContainerException($message, $code = 0, Exception $innerException = null)
    {
        return new ContainerException($message, $code, $innerException);
    }
}
