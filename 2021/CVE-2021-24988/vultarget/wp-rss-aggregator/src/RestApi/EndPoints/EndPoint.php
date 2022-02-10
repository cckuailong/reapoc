<?php

namespace RebelCode\Wpra\Core\RestApi\EndPoints;

use Dhii\Validation\ValidatorInterface;

/**
 * A simple implementation of a REST API endpoint.
 *
 * @since 4.13
 */
class EndPoint implements EndPointInterface
{
    /**
     * The endpoint's route.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $route;

    /**
     * The endpoint's accepted HTTP methods.
     *
     * @since 4.13
     *
     * @var string[]
     */
    protected $methods;

    /**
     * The endpoint's handler.
     *
     * @since 4.13
     *
     * @var callable
     */
    protected $handler;

    /**
     * The endpoint's authorization handler, if any.
     *
     * @since 4.13
     *
     * @var ValidatorInterface|null
     */
    protected $authHandler;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string                  $route       The route.
     * @param string[]                $methods     The accepted HTTP methods.
     * @param callable                $handler     The handler.
     * @param ValidatorInterface|null $authHandler Optional authorization handler.
     */
    public function __construct($route, array $methods, callable $handler, ValidatorInterface $authHandler = null)
    {
        $this->route = $route;
        $this->methods = $methods;
        $this->handler = $handler;
        $this->authHandler = $authHandler;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getAuthHandler()
    {
        return $this->authHandler;
    }
}
