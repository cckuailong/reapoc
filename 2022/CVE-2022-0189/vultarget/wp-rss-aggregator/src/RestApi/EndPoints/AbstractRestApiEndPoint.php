<?php

namespace RebelCode\Wpra\Core\RestApi\EndPoints;

use Dhii\Transformer\TransformerInterface;
use Exception;
use InvalidArgumentException;
use RebelCode\Wpra\Core\RestApi\Transformers\RecursiveToArrayTransformer;
use Traversable;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Abstract functionality for REST API end points.
 *
 * @since 4.13
 */
abstract class AbstractRestApiEndPoint
{
    /**
     * @since 4.13
     */
    public function __invoke()
    {
        $request = func_get_arg(0);

        if (!($request instanceof WP_REST_Request)) {
            throw new InvalidArgumentException('Argument is not a WP_REST_Request instance');
        }

        try {
            // Handle the request and get the response
            $response = $this->handle($request);
        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }

        // Retrieve the data
        $data = $response->get_data();
        // Turn the data into an array if it's a traversable
        $aData = ($data instanceof Traversable)
            ? iterator_to_array($data)
            : $data;

        // Transform the data if a transformer is given
        $transformer = $this->getTransformer();
        $tData = ($transformer instanceof TransformerInterface)
            ? $transformer->transform($aData)
            : $aData;

        // Update the response with the transformed data
        $response->set_data($tData);

        return $response;
    }

    /**
     * Retrieves the response transformer to use, if any.
     *
     * @since 4.13
     *
     * @return TransformerInterface|null The transformer instance or null if no transformer is required.
     */
    protected function getTransformer()
    {
        return new RecursiveToArrayTransformer();
    }

    /**
     * Creates an erroneous response from an exception.
     *
     * @since 4.17
     *
     * @param Exception $exception The exception from which to create the response.
     *
     * @return WP_Error The erreneous response.
     */
    protected function exceptionResponse(Exception $exception)
    {
        $message = $exception->getMessage();
        $data = [
            'status' => 500,
            'trace' => [],
        ];

        foreach ($exception->getTrace() as $trace) {
            $file = array_key_exists('file', $trace) ? basename($trace['file']) : '<unknown>';
            $line = array_key_exists('line', $trace) ? $trace['line'] : '<unknown>';
            $fn = array_key_exists('function', $trace) ? $trace['function'] : '<unknown>';

            if (array_key_exists('args', $trace)) {
                $args = array_map(function ($arg) {
                    if (is_scalar($arg)) {
                        return $arg;
                    }

                    return is_object($arg)
                        ? get_class($arg)
                        : gettype($arg);
                }, $trace['args']);
                $argsStr = implode(', ', $args);
            } else {
                $argsStr = '<unknown>';
            }

            $data['trace'][] = sprintf('%s(%s) @ %s:%s', $fn, $argsStr, $file, $line);
        }

        return new WP_Error('wprss_rest_api_error', $message, $data);
    }

    /**
     * Handles the request and provides a response.
     *
     * @since 4.13
     *
     * @param WP_REST_Request $request The request.
     *
     * @return WP_REST_Response|WP_Error The response or error.
     */
    abstract protected function handle(WP_REST_Request $request);
}
