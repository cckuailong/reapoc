<?php

namespace OpenCloud\Common\Exceptions;

use Guzzle\Http\Exception\BadResponseException;

class ForbiddenOperationException extends HttpResponseException
{
    public static function factory(BadResponseException $exception)
    {
        $response = $exception->getResponse();

        $message = sprintf(
            "This operation was forbidden; the API returned a %s status code with this message:\n%s",
            $response->getStatusCode(),
            (string) $response->getBody()
        );

        $e = new self($message);
        $e->setResponse($response);
        $e->setRequest($exception->getRequest());

        return $e;
    }
}
