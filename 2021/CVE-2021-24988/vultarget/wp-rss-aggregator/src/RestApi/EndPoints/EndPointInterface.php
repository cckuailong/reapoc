<?php

namespace RebelCode\Wpra\Core\RestApi\EndPoints;

use Dhii\Validation\ValidatorInterface;

/**
 * An interface that represents a REST API endpoint.
 *
 * @since 4.13
 */
interface EndPointInterface
{
    /**
     * Retrieves the endpoint's route.
     *
     * @since 4.13
     *
     * @return string
     */
    public function getRoute();

    /**
     * Retrieves the endpoint's accepted HTTP methods.
     *
     * @since 4.13
     *
     * @return string[]
     */
    public function getMethods();

    /**
     * Retrieves the endpoint's handler.
     *
     * @since 4.13
     *
     * @return callable
     */
    public function getHandler();

    /**
     * Retrieves the endpoint's authorization validator, if any.
     *
     * @since 4.13
     *
     * @return ValidatorInterface|null
     */
    public function getAuthHandler();
}
