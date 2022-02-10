<?php

namespace RebelCode\Wpra\Core\RestApi\EndPoints\FeedTemplates;

use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use RebelCode\Wpra\Core\RestApi\EndPoints\AbstractRestApiEndPoint;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The REST API endpoint for deleting templates.
 *
 * @since 4.13
 */
class DeleteTemplateEndPoint extends AbstractRestApiEndPoint
{
    /**
     * The query iterator for templates.
     *
     * @since 4.13
     *
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param CollectionInterface $collection The templates' collection data set.
     */
    public function __construct(CollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function handle(WP_REST_Request $request)
    {
        $id = filter_var($request['id'], FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);

        if (!empty($id)) {
            return new WP_REST_Response($this->deleteSingle($id));
        }

        $idSet = filter_var($request['ids'], FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

        if (!empty($idSet)) {
            return new WP_REST_Response($this->deleteCollection($idSet));
        }

        return new WP_Error(
            'template_missing_params',
            __('Missing template ID or set of IDs in the request', 'wprss'),
            ['status' => 400]
        );
    }

    /**
     * Deletes a single template by ID.
     *
     * @since 4.13
     *
     * @param int $id the ID of the template to delete.
     *
     * @return mixed|WP_Error The response data or error.
     */
    protected function deleteSingle($id)
    {
        if (!isset($this->collection[$id])) {
            return new WP_Error(
                'template_not_found',
                sprintf(__('Template "%s" does not exist', 'wprss'), $id),
                ['status' => 404]
            );
        }

        unset($this->collection[$id]);

        return [];
    }

    /**
     * Deletes a collection of templates, by IDs.
     *
     * @since 4.13
     *
     * @param int[] $ids The IDs of the templates to delete.
     *
     * @return mixed|WP_Error The response data or error.
     */
    protected function deleteCollection($ids)
    {
        $collection = $this->collection->filter(['id' => $ids]);
        $collection->clear();

        return [];
    }
}
