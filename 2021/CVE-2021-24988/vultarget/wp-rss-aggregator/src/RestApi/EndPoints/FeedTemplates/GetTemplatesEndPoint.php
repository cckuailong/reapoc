<?php

namespace RebelCode\Wpra\Core\RestApi\EndPoints\FeedTemplates;

use Iterator;
use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use RebelCode\Wpra\Core\RestApi\EndPoints\AbstractRestApiEndPoint;
use RebelCode\Wpra\Core\Util\PaginatedIterator;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The REST API end point for retrieving templates.
 *
 * @since 4.13
 */
class GetTemplatesEndPoint extends AbstractRestApiEndPoint
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
        $data = $this->getResponseData($request);

        return new WP_REST_Response($data);
    }

    /**
     * Retrieves the response data.
     *
     * @since 4.13
     *
     * @param WP_REST_Request $request
     *
     * @return array|mixed
     */
    protected function getResponseData(WP_REST_Request $request)
    {
        $id = filter_var($request['id'], FILTER_SANITIZE_STRING);

        if (!empty($id)) {
            return $this->collection[$id];
        }

        $filtered = $this->filterCollection($request, $this->collection);
        $itemCount = $filtered->getCount();
        $paginated = $this->paginateCollection($request, $filtered);

        return [
            'items' => $paginated,
            'count' => $itemCount,
        ];
    }

    /**
     * Applies filters to the collection based on the request.
     *
     * @since 4.13
     *
     * @param WP_REST_Request     $request    The request.
     * @param CollectionInterface $collection The collection to filter.
     *
     * @return CollectionInterface The filtered collection.
     */
    protected function filterCollection(WP_REST_Request $request, CollectionInterface $collection)
    {
        $filter = [];

        $search = filter_var($request['s'], FILTER_SANITIZE_STRING);
        $idSet = filter_var($request['set'], FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        $type = filter_var($request['type'], FILTER_SANITIZE_STRING);

        if (!empty($search)) {
            $filter['s'] = $search;
        }

        if (!empty($idSet)) {
            $filter['id'] = $idSet;
        }

        if (!empty($type)) {
            $filter['type'] = $type;
        }

        return empty($filter)
            ? $collection
            : $collection->filter($filter);
    }

    /**
     * Paginates the collection.
     *
     * @since 4.13
     *
     * @param WP_REST_Request     $request    The request.
     * @param CollectionInterface $collection The collection to paginate.
     *
     * @return Iterator The pagination collection iterator.
     */
    protected function paginateCollection(WP_REST_Request $request, CollectionInterface $collection)
    {
        $collection = $this->filterCollection($request, $collection);

        $num = filter_var($request['num'], FILTER_VALIDATE_INT);
        $page = filter_var($request['page'], FILTER_VALIDATE_INT);

        $num = empty($num) ? 20 : $num;
        $page = empty($page) ? 1 : $page;

        return new PaginatedIterator($collection, $page, $num);
    }
}
