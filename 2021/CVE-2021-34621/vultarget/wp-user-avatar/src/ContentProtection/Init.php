<?php

namespace ProfilePress\Core\ContentProtection;

use ProfilePress\Core\ContentProtection\Frontend\PostContent;
use ProfilePress\Core\ContentProtection\Frontend\Redirect;

class Init
{
    public function __construct()
    {
        add_filter('ppress_admin_hooks', function () {
            SettingsPage::get_instance();
        });

        add_action('wp_ajax_ppress_content_condition_field', [$this, 'get_content_condition_field']);
        add_action('wp_ajax_ppress_cr_object_search', [$this, 'get_content_condition_search']);

        PostContent::get_instance();
        Redirect::get_instance();
    }

    public function get_content_condition_field()
    {
        check_ajax_referer('ppress_cr_nonce', 'nonce');

        $instance = ContentConditions::get_instance();

        if ( ! empty($_POST['field_type']) && ! empty($_POST['facetId']) && ! empty($_POST['facetListId'])) {

            $condition_id = sanitize_text_field($_POST['condition_id']);

            $field = $instance->rule_value_field(
                $condition_id,
                sanitize_text_field($_POST['facetListId']),
                sanitize_text_field($_POST['facetId'])
            );

            if (false !== $field) wp_send_json_success($field);
        }

        wp_send_json_error();
    }

    public function get_content_condition_search()
    {
        $results['results'] = [];

        $object_type = sanitize_text_field($_REQUEST['object_type']);

        switch ($object_type) {

            case 'post_type':

                $post_type = ! empty($_REQUEST['object_key']) ? sanitize_text_field($_REQUEST['object_key']) : 'post';

                $search = ! empty($_REQUEST['search']) ? sanitize_text_field($_REQUEST['search']) : false;

                $query = $this->post_type_query($post_type, ['s' => $search]);

                foreach ($query as $post) {
                    $results['results'][] = array(
                        'id'   => $post->ID,
                        'text' => $post->post_title,
                    );
                }

                break;
            case 'taxonomy':

                $taxonomy = ! empty($_REQUEST['object_key']) ? sanitize_text_field($_REQUEST['object_key']) : 'category';

                $search = ! empty($_REQUEST['search']) ? sanitize_text_field($_REQUEST['search']) : false;

                $query = $this->taxonomy_query($taxonomy, ['search' => $search]);

                foreach ($query as $term) {
                    $results['results'][] = array(
                        'id'   => $term->term_id,
                        'text' => $term->name,
                    );
                }
        }

        wp_send_json($results, 200);
    }

    /**
     * @param string|array $post_type
     * @param array $args
     *
     * @return int[]|\WP_Post[]
     */
    public static function post_type_query($post_type, $args = [])
    {
        $default_args = [
            'numberposts' => 50,
            'post_type'   => $post_type
        ];

        return get_posts(array_filter(wp_parse_args($args, $default_args)));
    }

    public function taxonomy_query($taxonomy, $args = [])
    {
        $args = wp_parse_args($args, array(
            'hide_empty' => false,
            'number'     => 50,
            'taxonomy'   => $taxonomy,
        ));

        return get_terms($args);
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
