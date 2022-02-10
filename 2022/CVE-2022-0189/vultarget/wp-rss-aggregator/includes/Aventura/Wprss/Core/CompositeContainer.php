<?php

namespace Aventura\Wprss\Core;

use Aventura\Wprss\Core\Plugin\Di\AbstractWritableCompositeContainer;
use Interop\Container\ContainerInterface as BaseContainerInterface;

/**
 * A container that delegates service lookup to child containers.
 *
 * @since 4.11
 */
class CompositeContainer extends AbstractWritableCompositeContainer
{
    /**
     * @since 4.11
     *
     * @param BaseContainerInterface $parent The parent of this container, if any.
     */
    public function __construct(BaseContainerInterface $parent = null)
    {
        if (!is_null($parent)) {
            $this->_setParentContainer($parent);
        }
    }
}
