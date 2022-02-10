<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

class RestCategoryController extends \WP_REST_Terms_Controller
{
    public function __construct()
    {
        $this->meta = new \WP_REST_Term_Meta_Fields(glsr()->taxonomy);
        $this->namespace = glsr()->id.'/v1';
        $this->rest_base = 'categories';
        $this->taxonomy = glsr()->taxonomy;
    }

    /**
     * @param string $taxonomy
     * @return bool
     */
    protected function check_is_taxonomy_allowed($taxonomy)
    {
        // if (!is_user_logged_in()) {
        //     return false;
        // }
        return parent::check_is_taxonomy_allowed($taxonomy);
    }
}
