<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema\SummaryParameters;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;
use WP_Error;

class RestSummaryController extends RestReviewController
{
    public function __construct()
    {
        parent::__construct();
        $this->rest_base = 'summary';
    }

    /**
     * @return array
     */
    public function get_collection_params()
    {
        $params = glsr(SummaryParameters::class)->parameters();
        return apply_filters('rest_rating_summary_collection_params', $params);
    }

    /**
     * @return array
     */
    public function get_item_schema()
    {
        if (empty($this->schema)) {
            $this->schema = [
                '$schema' => 'http://json-schema.org/draft-04/schema#',
                'properties' => [
                    'average' => [
                        'context' => ['view'],
                        'description' => _x('The average rating.', 'admin-text', 'site-reviews'),
                        'type' => 'number',
                    ],
                    'maximum' => [
                        'context' => ['view'],
                        'description' => _x('The defined maximum rating.', 'admin-text', 'site-reviews'),
                        'type' => 'integer',
                    ],
                    'minimum' => [
                        'context' => ['view'],
                        'description' => _x('The defined minimum rating.', 'admin-text', 'site-reviews'),
                        'type' => 'integer',
                    ],
                    'ranking' => [
                        'context' => ['view'],
                        'description' => _x('The bayesian ranking number.', 'admin-text', 'site-reviews'),
                        'type' => 'number',
                    ],
                    'ratings' => [
                        'context' => ['view'],
                        'description' => _x('The total number of reviews for each rating level from zero to maximum rating.', 'admin-text', 'site-reviews'),
                        'items' => ['type' => 'integer'],
                        'type' => 'array',
                    ],
                    'reviews' => [
                        'context' => ['view'],
                        'description' => _x('The total number of reviews used to calculate the average.', 'admin-text', 'site-reviews'),
                        'type' => 'integer',
                    ],
                ],
                'title' => 'rating-summary',
                'type' => 'object',
            ];
        }
        return $this->add_additional_fields_schema($this->schema);
    }

    /**
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_items($request)
    {
        $args = $this->normalizedArgs($request);
        if ($request['_rendered']) {
            return rest_ensure_response([
                'rendered' => glsr(SiteReviewsSummaryShortcode::class)->build($args),
            ]);
        }
        return rest_ensure_response(glsr_get_ratings($args)->toArray());
    }

    /**
     * @param \WP_REST_Request $request
     * @return true|\WP_Error
     */
    public function get_items_permissions_check($request)
    {
        if (!is_user_logged_in()) {
            $error = _x('Sorry, you are not allowed to view review summaries.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_forbidden_context', $error, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return true;
    }

    /**
     * @return void
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/'.$this->rest_base, [
            [
                'args' => $this->get_collection_params(),
                'callback' => [$this, 'get_items'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'get_items_permissions_check'],
            ],
            'schema' => [$this, 'get_public_item_schema'],
        ]);
    }
}
