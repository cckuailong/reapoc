<?php

namespace RebelCode\Wpra\Core\RestApi\EndPoints\Handlers;

use Iterator;
use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;
use RebelCode\Wpra\Core\RestApi\EndPoints\AbstractRestApiEndPoint;
use RebelCode\Wpra\Core\Util\PaginatedIterator;
use WP_REST_Request;
use WP_REST_Response;

/**
 * A generic endpoint handler for entity collections.
 *
 * @since 4.17.2
 */
class GetEntityHandler extends AbstractRestApiEndPoint
{
    /**
     * @since 4.17.2
     *
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * @since 4.17.2
     *
     * @var string
     */
    protected $idKey;

    /**
     * @since 4.17.2
     *
     * @var callable[]
     */
    protected $filters;

    /**
     * @since 4.17.2
     *
     * @var int
     */
    protected $defPerPage;

    /**
     * Constructor.
     *
     * @since 4.17.2
     *
     * @param CollectionInterface $collection The entity collection.
     * @param string              $idKey      The key in requests for IDs, for responding with single entities.
     * @param callable[]          $params     The available request params, as functions. Each function is given the
     *                                        current accumulated list of collection filters and the request as
     *                                        arguments and is expected to return the new list of collection filters.
     * @param int                 $defPerPage The default number of entities to respond with per page.
     */
    public function __construct(CollectionInterface $collection, $idKey, array $params = [], $defPerPage = 20)
    {
        $this->collection = $collection;
        $this->idKey = $idKey;
        $this->filters = $params;
        $this->defPerPage = $defPerPage;
    }

    /**
     * @inheritDoc
     *
     * @since 4.17.2
     */
    protected function handle(WP_REST_Request $request)
    {
        $id = filter_input(INPUT_GET, $this->idKey, FILTER_VALIDATE_INT);

        if (!empty($id)) {
            return new WP_REST_Response($this->collection[$id]->export());
        }

        $filteredColl = $this->filterCollection($request, $this->collection);
        $paginatedIter = $this->paginateCollection($request, $filteredColl);

        return new WP_REST_Response([
            'items' => $paginatedIter,
            'count' => $filteredColl->getCount(),
        ]);
    }

    /**
     * Applies filters to the collection based on the request.
     *
     * @since 4.17.2
     *
     * @param WP_REST_Request     $request    The request.
     * @param CollectionInterface $collection The collection to filter.
     *
     * @return CollectionInterface The filtered collection.
     */
    protected function filterCollection(WP_REST_Request $request, CollectionInterface $collection)
    {
        $filters = [];

        foreach ($this->filters as $key => $paramFn) {
            $filters = call_user_func_array($paramFn, [$filters, $request]);
        }

        return empty($filters)
            ? $collection
            : $collection->filter($filters);
    }

    /**
     * Paginates the collection.
     *
     * @since 4.17.2
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

        $num = empty($num) ? $this->defPerPage : $num;
        $page = empty($page) ? 1 : $page;

        return new PaginatedIterator($collection, $page, $num);
    }
}
