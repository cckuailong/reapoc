<?php

namespace Aventura\Wprss\Core\Model;

use Aventura\Wprss\Core;

/**
 * Common functionality for spinners.
 *
 * @since 4.8.1
 */
abstract class SpinnerAbstract extends Core\Plugin\ComponentAbstract implements SpinnerInterface
{
    public function spin($content, $options = array())
    {
        $this->getApi()->spin($content, $options);
    }

    /**
     * @since 4.8.1
     * @return SpinnerApiInterface
     */
    abstract public function getApi();
}