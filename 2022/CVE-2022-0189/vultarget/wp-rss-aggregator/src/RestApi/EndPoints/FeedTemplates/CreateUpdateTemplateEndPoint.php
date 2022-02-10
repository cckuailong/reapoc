<?php

namespace RebelCode\Wpra\Core\RestApi\EndPoints\FeedTemplates;

use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\RestApi\EndPoints\AbstractRestApiEndPoint;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The REST API endpoint for creating or updating templates.
 *
 * @since 4.13
 */
class CreateUpdateTemplateEndPoint extends AbstractRestApiEndPoint
{
    /**
     * The query iterator for templates.
     *
     * @since 4.13
     *
     * @var DataSetInterface
     */
    protected $collection;

    /**
     * True if the endpoint is idempotent, false otherwise.
     *
     * @since 4.13
     *
     * @var bool
     */
    protected $idempotent;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param DataSetInterface $collection The templates' collection data set.
     * @param bool             $idempotent True to make the endpoint be idempotent, false to not.
     */
    public function __construct(DataSetInterface $collection, $idempotent = true)
    {
        $this->collection = $collection;
        $this->idempotent = $idempotent;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function handle(WP_REST_Request $request)
    {
        $id = (isset($request['id']))
            ? filter_var($request['id'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)
            : null;

        if (empty($id) && $this->idempotent) {
            return new WP_Error(
                'wprss_template_id_missing', __('Missing template ID or slug name'), ['status' => 400]
            );
        }

        $data = $request->get_params();
        $data = array_intersect_key($data, ['name' => null, 'slug' => null, 'type' => null, 'options' => null]);

        $this->collection[$id] = $data;

        return new WP_REST_Response($this->collection[$id]);
    }
}
