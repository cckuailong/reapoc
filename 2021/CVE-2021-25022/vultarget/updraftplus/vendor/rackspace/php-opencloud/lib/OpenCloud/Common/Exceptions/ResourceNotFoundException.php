<?php

namespace OpenCloud\Common\Exceptions;

use Guzzle\Http\Exception\BadResponseException;

class ResourceNotFoundException extends HttpResponseException
{
    public static function factory(BadResponseException $exception)
    {
        $response = $exception->getResponse();

        $message = sprintf(
            "This resource you were looking for could not be found; the API returned a %s status code with this message:\n%s",
            $response->getStatusCode(),
            (string) $response->getBody()
        );

        $e = new self($message);
        $e->setResponse($response);
        $e->setRequest($exception->getRequest());

        return $e;
    }
}
