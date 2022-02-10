<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use WP_Error;

class RestTypeController extends \WP_REST_Controller
{
    public function __construct()
    {
        $this->namespace = glsr()->id.'/v1';
        $this->rest_base = 'types';
    }

    /**
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        $data = [];
        foreach (glsr()->retrieveAs('array', 'review_types') as $slug => $name) {
            $data[] = compact('name', 'slug');
        }
        if (empty($data)) {
            $error = _x('There are no review types.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_review_invalid_types', $error, ['status' => 400]);
        }
        return rest_ensure_response($data);
    }

    /**
     * @param \WP_REST_Request $request
     * @return true|\WP_Error
     */
    public function get_items_permissions_check($request)
    {
        if (!is_user_logged_in()) {
            $error = _x('Sorry, you are not allowed to view review types.', 'admin-text', 'site-reviews');
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
                'callback' => [$this, 'get_items'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'get_items_permissions_check'],
            ],
        ]);
    }
}
