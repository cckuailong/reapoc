<?php

namespace Aventura\Wprss\Core\Block;

/**
 * A block that renders using a callback function.
 *
 * @since 4.11
 */
class CallbackBlock extends AbstractBlock
{
    /**
     * {@inheritdoc}
     *
     * @since 4.11
     *
     * @param array $data
     * @param \callable $callback
     */
    public function __construct(array $data = array(), $callback = null)
    {
        parent::__construct($data);

        $this->setCallback($callback);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getOutput()
    {
        $callback = $this->getCallback();

        return is_callable($callback)
            ? call_user_func($callback)
            : '';
    }
}
