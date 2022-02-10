<?php

namespace Aventura\Wprss\Core\Http\Message;

/**
 * @todo Substitute with PSR-7 interface.
 * @since 4.9
 * @link https://github.com/php-fig/http-message/blob/master/src/MessageInterface.php
 */
interface MessageInterface
{
    /**
     * @since 4.9
     */
    public function getBody();
}
