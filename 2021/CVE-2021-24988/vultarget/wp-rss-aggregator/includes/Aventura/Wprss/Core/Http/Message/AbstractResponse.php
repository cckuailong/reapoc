<?php

namespace Aventura\Wprss\Core\Http\Message;

use Aventura\Wprss\Core;

/**
 * Base functionality for responses
 * 
 * @since 4.9
 */
abstract class AbstractResponse extends Core\DataObject implements ResponseInterface
{
    /**
     * @since 4.9
     */
    const K_BODY = 'body';
    
    /**
     * @since 4.9
     */
    protected $body;
    
    /**
     * {@inheritdoc}
     * @since 4.9
     */
    public function getBody()
    {
        return $this->getData(static::K_BODY);
    }
}
