<?php

namespace RebelCode\Wpra\Core\RestApi;

use Dhii\Validation\Exception\ValidationFailedExceptionInterface;
use Dhii\Validation\ValidatorInterface;
use RebelCode\Wpra\Core\RestApi\EndPoints\EndPointInterface;
use WP_Error;
use WP_REST_Request;

/**
 * A REST API route manager.
 *
 * @since 4.13
 */
class EndPointManager
{
    /**
     * The REST API endpoints.
     *
     * @since 4.13
     *
     * @var EndPointInterface[]
     */
    protected $endPoints;

    /**
     * The namespace to use for the routes.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $namespace;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string              $namespace The namespace to use for the routes.
     * @param EndPointInterface[] $endPoints The REST API endpoints.
     */
    public function __construct($namespace, $endPoints)
    {
        $this->namespace = $namespace;
        $this->endPoints = $endPoints;
    }

    /**
     * Registers the routes and endpoints with WordPress.
     *
     * @since 4.13
     */
    public function register()
    {
        foreach ($this->endPoints as $endPoint) {
            $route = $endPoint->getRoute();
            $methods = $endPoint->getMethods();
            $handler = $endPoint->getHandler();
            $authFn = $endPoint->getAuthHandler();

            register_rest_route($this->namespace, $route, [
                'methods' => $methods,
                'callback' => $handler,
                'permission_callback' => $this->getPermissionCallback($authFn),
            ]);
        }
    }

    /**
     * Retrieves the permissions callback for an auth validator.
     *
     * @since 4.13
     *
     * @param ValidatorInterface|null $authValidator The validator instance, if any.
     *
     * @return callable The callback.
     */
    protected function getPermissionCallback(ValidatorInterface $authValidator = null)
    {
        if ($authValidator === null) {
            return function () {
                return true;
            };
        }

        return function (WP_REST_Request $request) use ($authValidator) {
            try {
                $authValidator->validate($request);

                return true;
            } catch (ValidationFailedExceptionInterface $exception) {
                return new WP_Error('wprss_not_authorized', __('Unauthorized', 'wprss'), [
                    'status' => 401,
                    'reasons' => $exception->getValidationErrors(),
                ]);
            }
        };
    }
}
