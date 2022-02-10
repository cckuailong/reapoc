<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\Response\Prepare;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema\ReviewParameters;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema\ReviewSchema;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class RestReviewController extends \WP_REST_Controller
{
    public function __construct()
    {
        $this->namespace = glsr()->id.'/v1';
        $this->rest_base = 'reviews';
    }

    /**
     * @return bool
     */
    public function check_read_permission(Review $review)
    {
        return $review->is_approved || glsr()->can('read_post', $review->ID);
    }

    /**
     * @param WP_REST_Request $request
     * @return \WP_REST_Response|WP_Error
     */
    public function create_item($request)
    {
        $review = glsr_create_review($request->get_params());
        if (false === $review || !$review->isValid()) {
            $error = _x('Review creation failed, please check the Site Reviews console log for more details.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_review_create_item', $error, ['status' => 500]);
        }
        if ($request['_rendered']) {
            $response = $this->renderedItems($request);
        } else {
            $data = $this->prepare_item_for_response($review, $request);
            $response = rest_ensure_response($data);
        }
        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $review->ID)));
        return $response;
    }

    /**
     * @param WP_REST_Request $request
     * @return true|WP_Error
     */
    public function create_item_permissions_check($request)
    {
        if (!empty($request['id'])) {
            $error = _x('Cannot create existing review.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_review_exists', $error, ['status' => 400]);
        }
        if (!empty($request['author']) && get_current_user_id() !== $request['author'] && !glsr()->can('edit_others_posts')) {
            $error = _x('Sorry, you are not allowed to create reviews as this user.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_edit_others', $error, ['status' => rest_authorization_required_code()]);
        }
        if (!glsr()->can('create_posts')) {
            $error = _x('Sorry, you are not allowed to create reviews as this user.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_create', $error, ['status' => rest_authorization_required_code()]);
        }
        if (!$this->check_assign_terms_permission($request)) {
            $error = _x('Sorry, you are not allowed to assign the provided terms.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_assign_term', $error, ['status' => rest_authorization_required_code()]);
        }
        return true;
    }

    /**
     * @param WP_REST_Request $request
     * @return \WP_REST_Response|WP_Error
     */
    public function delete_item($request)
    {
        $request->set_param('context', 'edit');
        if ((bool) $request['force']) {
            return $this->forceDeleteItem($request);
        }
        if (EMPTY_TRASH_DAYS < 1) {
            $error = sprintf(_x('The review does not support trashing. Set "%s" to delete.', 'admin-text', 'site-reviews'), 'force=true');
            return new WP_Error('rest_trash_not_supported', $error, ['status' => 501]);
        }
        if ('trash' === get_post_status($request['id'])) {
            $error = _x('The review has already been deleted.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_already_trashed', $error, ['status' => 410]);
        }
        if (!wp_trash_post($request['id'])) {
            $error = _x('The review cannot be deleted.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_delete', $error, ['status' => 500]);
        }
        $review = glsr_get_review($request['id']);
        return $this->prepare_item_for_response($review, $request);
    }

    /**
     * @param WP_REST_Request $request
     * @return true|WP_Error
     */
    public function delete_item_permissions_check($request)
    {
        $review = glsr_get_review($request['id']);
        if (!$review->isValid()) {
            $error = _x('Invalid review ID.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_review_invalid_id', $error, ['status' => 404]);
        }
        if (!glsr()->can('delete_post', $review->ID)) {
            $error = _x('Sorry, you are not allowed to delete this review.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_delete', $error, ['status' => rest_authorization_required_code()]);
        }
        return true;
    }

    /**
     * @return array
     */
    public function get_collection_params()
    {
        $params = glsr(ReviewParameters::class)->parameters();
        $params['context'] = $this->get_context_param(['default' => 'view']);
        return apply_filters('rest_review_collection_params', $params, glsr()->post_type);
    }

    /**
     * @param WP_REST_Request $request
     * @return \WP_REST_Response|WP_Error
     */
    public function get_item($request)
    {
        if ($request['_rendered']) {
            return $this->renderedItem($request);
        }
        $review = glsr_get_review($request['id']);
        $data = $this->prepare_item_for_response($review, $request);
        return rest_ensure_response($data);
    }

    /**
     * @param WP_REST_Request $request
     * @return true|WP_Error
     */
    public function get_item_permissions_check($request)
    {
        $review = glsr_get_review($request['id']);
        if (!$review->isValid()) {
            $error = _x('Invalid review ID.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_review_invalid_id', $error, ['status' => 404]);
        }
        if (!is_user_logged_in() || !$this->check_read_permission($review)) {
            $error = _x('Sorry, you are not allowed to view this review.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_view', $error, ['status' => rest_authorization_required_code()]);
        }
        return true;
    }

    /**
     * @return array
     */
    public function get_item_schema()
    {
        if (empty($this->schema)) {
            $this->schema = glsr(ReviewSchema::class, ['api' => $this])->schema();
        }
        return $this->add_additional_fields_schema($this->schema);
    }

    /**
     * @param WP_REST_Request $request
     * @return \WP_REST_Response|WP_Error
     */
    public function get_items($request)
    {
        if ($request['_rendered']) {
            return $this->renderedItems($request);
        }
        $results = glsr_get_reviews($this->normalizedArgs($request));
        $reviews = [];
        foreach ($results->reviews as $review) {
            if ($this->check_read_permission($review)) {
                $data = $this->prepare_item_for_response($review, $request);
                $reviews[] = $this->prepare_response_for_collection($data);
            }
        }
        if ($results->args['page'] > $results->max_num_pages && $results->total > 0) {
            $error = _x('The page number requested is larger than the number of pages available.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_invalid_page_number', $error, ['status' => 400]);
        }
        $response = rest_ensure_response($reviews);
        if (is_wp_error($response)) {
            return $response;
        }
        return $this->prepareResponse($response, $request, $results);
    }

    /**
     * @param WP_REST_Request $request
     * @return true|WP_Error
     */
    public function get_items_permissions_check($request)
    {
        if (!is_user_logged_in()) {
            $error = _x('Sorry, you do not have permission to access reviews.', 'admin-text', 'site-reviews');
        }
        if ('edit' === $request['context'] && !glsr()->can('edit_posts')) {
            $error = _x('Sorry, you are not allowed to edit reviews.', 'admin-text', 'site-reviews');
        }
        if (isset($error)) {
            return new WP_Error('rest_forbidden_context', $error, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return true;
    }

    /**
     * @param Review $review
     * @param WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function prepare_item_for_response($review, $request)
    {
        $fields = $this->get_fields_for_response($request);
        $prepare = new Prepare($fields, $review, $request);
        glsr()->store('api', true); // load all review fields!
        foreach ($fields as $field) {
            call_user_func([$prepare, $field]);
        }
        glsr()->discard('api');
        $data = $prepare->item();
        $data = $this->add_additional_fields_to_object($data, $request);
        $data = $this->filter_response_by_context($data, Arr::get($request, 'context', 'view'));
        $response = rest_ensure_response($data);
        $links = $this->prepareLinks($review);
        $response->add_links($links);
        if ($self = Arr::get($links, 'self.href')) {
            $actions = $this->getAvailableActions($review, $request);
            foreach ($actions as $rel) {
                $response->add_link($rel, $self);
            }
        }
        return $response; // @todo filter this, i.e. "rest_prepare_{glsr()->post_type}"
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
            ], [
                'args' => $this->get_endpoint_args_for_item_schema(\WP_REST_Server::CREATABLE),
                'callback' => [$this, 'create_item'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'create_item_permissions_check'],
            ],
            'schema' => [$this, 'get_public_item_schema'],
        ]);
        register_rest_route($this->namespace, '/'.$this->rest_base.'/(?P<id>[\d]+)', [
            [
                'args' => [
                    'context' => $this->get_context_param(['default' => 'view']),
                ],
                'callback' => [$this, 'get_item'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'get_item_permissions_check'],
            ],
            [
                'args' => $this->get_endpoint_args_for_item_schema(\WP_REST_Server::EDITABLE),
                'callback' => [$this, 'update_item'],
                'methods' => \WP_REST_Server::EDITABLE,
                'permission_callback' => [$this, 'update_item_permissions_check'],
            ],
            [
                'args' => [
                    'force' => [
                        'default' => false,
                        'description' => _x('Whether to bypass Trash and force deletion.', 'admin-text', 'site-reviews'),
                        'type' => 'boolean',
                    ],
                ],
                'callback' => [$this, 'delete_item'],
                'methods' => \WP_REST_Server::DELETABLE,
                'permission_callback' => [$this, 'delete_item_permissions_check'],
            ],
            'args' => [
                'id' => [
                    'description' => _x('Unique identifier for the object.', 'admin-text', 'site-reviews'),
                    'type' => 'integer',
                ],
            ],
            'schema' => [$this, 'get_public_item_schema'],
        ]);
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function update_item($request)
    {
        $review = glsr_update_review($request['id'], $request->get_params());
        if (!$review) {
            $error = _x('Review update failed, please check the Site Reviews console log for more details.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_update_review', $error, ['status' => 500]);
        }
        $request->set_param('context', 'edit');
        if ($request['_rendered']) {
            return $this->renderedItem($request);
        }
        $data = $this->prepare_item_for_response($review, $request);
        $response = rest_ensure_response($data);
        return $response;
    }

    /**
     * @param WP_REST_Request $request
     * @return true|WP_Error
     */
    public function update_item_permissions_check($request)
    {
        $review = glsr_get_review($request['id']);
        if (!$review->isValid()) {
            $error = _x('Invalid review ID.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_review_invalid_id', $error, ['status' => 404]);
        }
        if (!glsr()->can('edit_post', $review->ID)) {
            $error = _x('Sorry, you are not allowed to edit this review.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_edit', $error, ['status' => rest_authorization_required_code()]);
        }
        if (!empty($request['author']) && get_current_user_id() !== $request['author'] && !glsr()->can('edit_others_posts')) {
            $error = _x('Sorry, you are not allowed to update reviews as this user.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_edit_others', $error, ['status' => rest_authorization_required_code()]);
        }
        if (!$this->check_assign_terms_permission($request)) {
            $error = _x('Sorry, you are not allowed to assign the provided terms.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_assign_term', $error, ['status' => rest_authorization_required_code()]);
        }
        return true;
    }

    /**
     * @param WP_REST_Request $request
     * @return bool
     */
    protected function check_assign_terms_permission($request)
    {
        $terms = Arr::consolidate($request['assigned_terms']);
        foreach ($terms as $termId) {
            if (!get_term($termId, glsr()->taxonomy)) {
                continue; // Invalid terms will be rejected later
            }
            if (!current_user_can('assign_term', (int) $termId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param WP_REST_Request $request
     * @return \WP_REST_Response|WP_Error
     */
    public function forceDeleteItem($request)
    {
        $review = glsr_get_review($request['id']);
        $previous = $this->prepare_item_for_response($review, $request);
        $result = wp_delete_post($review->ID, true);
        if (false === $result) {
            $error = _x('The review cannot be deleted.', 'admin-text', 'site-reviews');
            return new WP_Error('rest_cannot_delete', $error, ['status' => 500]);
        }
        return rest_ensure_response([
            'deleted' => true,
            'previous' => $previous->get_data(),
        ]);
    }

    /**
     * @return array
     */
    protected function getAvailableActions(Review $review, WP_REST_Request $request)
    {
        if ('edit' !== $request['context']) {
            return [];
        }
        $rels = [];
        $taxonomy = get_taxonomy(glsr()->taxonomy);
        if (glsr()->can('publish_posts')) {
            $rels[] = 'https://api.w.org/action-publish';
        }
        if (glsr()->can('edit_others_posts')) {
            $rels[] = 'https://api.w.org/action-assign-author';
        }
        if (current_user_can($taxonomy->cap->edit_terms)) {
            $rels[] = 'https://api.w.org/action-create-'.glsr()->taxonomy;
        }
        if (current_user_can($taxonomy->cap->assign_terms)) {
            $rels[] = 'https://api.w.org/action-assign-'.glsr()->taxonomy;
        }
        return $rels;
    }

    /**
     * @return array
     */
    protected function normalizedArgs(WP_REST_Request $request)
    {
        $args = [];
        $registered = $this->get_collection_params();
        foreach ($registered as $key => $params) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }
        if (empty($args['date'])) {
            $args['date'] = [];
            if (isset($args['after'])) {
                $args['date']['after'] = $args['after'];
            }
            if (isset($args['before'])) {
                $args['date']['before'] = $args['before'];
            }
        }
        return $args;
    }

    /**
     * @return array
     */
    protected function prepareLinks(Review $review)
    {
        $base = $this->namespace.'/'.$this->rest_base;
        $revisions = wp_get_post_revisions($review->ID, ['fields' => 'ids']);
        $revisionCount = count($revisions);
        $links = [
            'self' => [
                'href' => rest_url(trailingslashit($base).$review->ID),
            ],
            'collection' => [
                'href' => rest_url($base),
            ],
            'about' => [
                'href' => rest_url('wp/v2/types/'.glsr()->post_type),
            ],
            'https://api.w.org/attachment' => [
                'href' => add_query_arg('parent', $review->ID, rest_url('wp/v2/media')),
            ],
            'https://api.w.org/term' => [
                'embeddable' => true,
                'href' => add_query_arg('post', $review->ID, rest_url('wp/v2/'.glsr()->taxonomy)),
                'taxonomy' => glsr()->taxonomy,
            ],
            'version-history' => [
                'count' => $revisionCount,
                'href' => rest_url(trailingslashit($base).$review->ID.'/revisions'),
            ],
        ];
        if ($revisionCount > 0) {
            $lastRevision = array_shift($revisions);
            $links['predecessor-version'] = [
                'href' => rest_url(trailingslashit($base).$review->ID.'/revisions/'.$lastRevision),
                'id' => $lastRevision,
            ];
        }
        if (!empty($review->user_id)) {
            $links['author'] = [
                'embeddable' => true,
                'href' => rest_url('wp/v2/users/'.$review->user_id),
            ];
        }
        if (post_type_supports(glsr()->post_type, 'comments')) {
            $links['replies'] = [
                'embeddable' => true,
                'href' => add_query_arg('post', $review->ID, rest_url('wp/v2/comments')),
            ];
        }
        return $links;
    }

    /**
     * @return \WP_REST_Response
     */
    protected function prepareResponse(WP_REST_Response $response, WP_REST_Request $request, Reviews $reviews)
    {
        $page = $reviews->args['page'];
        $ratings = glsr_get_ratings($this->normalizedArgs($request));
        $response->header('X-GLSR-Average', (string) $ratings->average);
        $response->header('X-GLSR-Ranking', (string) $ratings->ranking);
        $response->header('X-WP-Total', (string) $reviews->total);
        $response->header('X-WP-TotalPages', (string) $reviews->max_num_pages);
        $parameters = $request->get_query_params();
        $base = add_query_arg(urlencode_deep($parameters), rest_url(sprintf('%s/%s', $this->namespace, $this->rest_base)));
        if ($page > 1) {
            $prevPage = $page - 1;
            if ($prevPage > $reviews->max_num_pages) {
                $prevPage = $reviews->max_num_pages;
            }
            $prevLink = add_query_arg('page', $prevPage, $base);
            $response->link_header('prev', $prevLink);
        }
        if ($reviews->max_num_pages > $page) {
            $nextPage = $page + 1;
            $nextLink = add_query_arg('page', $nextPage, $base);
            $response->link_header('next', $nextLink);
        }
        return $response;
    }

    protected function renderedItem(WP_REST_Request $request)
    {
        $review = glsr_get_review($request['id']);
        return rest_ensure_response([
            'rendered' => (string) $review->build(),
        ]);
    }

    protected function renderedItems(WP_REST_Request $request)
    {
        $html = glsr(SiteReviewsShortcode::class)->buildReviewsHtml($this->normalizedArgs($request));
        $response = rest_ensure_response([
            'pagination' => $html->getPagination($wrap = false),
            'rendered' => $html->getReviews(),
        ]);
        return $this->prepareResponse($response, $request, $html->reviews);
    }
}
