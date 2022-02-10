<?php

namespace Aventura\Wprss\Core\Plugin\Di;

use Exception as BaseException;
use Dhii\Di\AbstractCompositeContainer;
use Dhii\Di\ParentAwareContainerInterface;
use Interop\Container\ContainerInterface as BaseContainerInterface;

/**
 * Common functionality for composite containers that can have children added.
 *
 * @since 4.11
 */
class AbstractWritableCompositeContainer extends AbstractCompositeContainer implements
    ParentAwareContainerInterface,
    WritableCompositeContainerInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function get($id)
    {
        return $this->_getDelegated($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function has($id)
    {
        return $this->_hasDelegated($id);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getParentContainer()
    {
        return $this->_getParentContainer();
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getContainers()
    {
        return $this->_getContainers();
    }

    /**
     * Adds a child container.
     *
     * @since 4.11
     *
     * @param BaseContainerInterface $container The container to add.
     *
     * @return $this This instance.
     */
    public function add(BaseContainerInterface $container)
    {
        $this->_add($container);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     *
     * @return NotFoundException The new exception instance.
     */
    protected function _createNotFoundException($message, $code = 0, BaseException $innerException = null)
    {
        return new NotFoundException($message, $code, $innerException);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     *
     * @return ContainerException The new exception instance.
     */
    protected function _createContainerException($message, $code = 0, BaseException $innerException = null)
    {
        return new ContainerException($message, $code, $innerException);
    }
}
