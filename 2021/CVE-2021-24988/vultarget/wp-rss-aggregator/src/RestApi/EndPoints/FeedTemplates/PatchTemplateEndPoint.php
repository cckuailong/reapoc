<?php

namespace RebelCode\Wpra\Core\RestApi\EndPoints\FeedTemplates;

use ArrayAccess;
use RebelCode\Wpra\Core\Data\DataSetInterface;
use RebelCode\Wpra\Core\RestApi\EndPoints\AbstractRestApiEndPoint;
use stdClass;
use Traversable;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The REST API endpoint for patching templates.
 *
 * @since 4.13
 */
class PatchTemplateEndPoint extends AbstractRestApiEndPoint
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
     * Constructor.
     *
     * @since 4.13
     *
     * @param DataSetInterface $collection The templates' collection data set.
     */
    public function __construct(DataSetInterface $collection)
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
        $rId = isset($request['id']) ? ($request['id']) : null;
        $fId = filter_var($rId, FILTER_SANITIZE_STRING);

        if (!isset($this->collection[$fId])) {
            return new WP_Error(
                'template_not_found',
                sprintf(__('Template "%s" does not exist', 'wprss'), $fId),
                ['status' => 404]
            );
        }

        // Get the patch from the request params and remove the ID (which cannot be patched)
        $patch = $request->get_params();
        unset($patch['id']);

        // Get the template to be patched
        $template = $this->collection[$fId];
        // Recursively patch it
        $template = $this->recursivePatch($template, $patch);

        return new WP_REST_Response($template);
    }

    /**
     * Recursively patches a subject with every entry in a given patch data array.
     *
     * @since 4.13
     *
     * @param array|ArrayAccess          $subject The subject to patch.
     * @param array|stdClass|Traversable $patch   The data to patch the subject with.
     *
     * @return array|ArrayAccess The patched subject.
     */
    protected function recursivePatch($subject, $patch)
    {
        foreach ($patch as $key => $value) {
            $subject[$key] = is_array($value)
                ? $this->recursivePatch($subject[$key], $value)
                : $value;
        }

        return $subject;
    }
}
