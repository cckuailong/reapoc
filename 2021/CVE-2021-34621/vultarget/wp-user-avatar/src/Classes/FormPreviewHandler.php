<?php

namespace ProfilePress\Core\Classes;

class FormPreviewHandler
{
    protected $_form_id = '';
    protected $_form_type = '';

    public function __construct()
    {
        if ( ! isset($_GET['pp_preview_form'], $_GET['type'])) return;

        $this->_form_id   = absint($_GET['pp_preview_form']);
        $this->_form_type = sanitize_text_field($_GET['type']);

        add_action('pre_get_posts', array($this, 'pre_get_posts'));

        add_filter('the_title', array($this, 'the_title'));
        remove_filter('the_content', 'wpautop');
        remove_filter('the_excerpt', 'wpautop');
        add_filter('the_content', array($this, 'the_content'), 9001);
        add_filter('get_the_excerpt', array($this, 'the_content'));
        add_action('template_redirect', array($this, 'template_include'));
        add_filter('post_thumbnail_html', array($this, 'post_thumbnail_html'));
    }

    public function pre_get_posts($query)
    {
        if ( $query->is_main_query()) {
            $query->set('posts_per_page', 1);
            $query->set('ignore_sticky_posts', true);
        }
    }

    /**
     * @return string
     */
    function the_title($title)
    {
        if ( ! in_the_loop()) return $title;

        $form_title = FormRepository::get_name($this->_form_id, $this->_form_type);

        return $form_title . " " . esc_html__('Preview', 'wp-user-avatar');
    }

    /**
     * @return string
     */
    function the_content()
    {
        if ( ! is_user_logged_in()) return esc_html__('You must be logged in to preview a form.', 'wp-user-avatar');

        return do_shortcode(sprintf("[profilepress-%s id='%d']", $this->_form_type, $this->_form_id));
    }

    /**
     * @return string
     */
    function template_include()
    {
        require(locate_template(array('page.php', 'single.php', 'index.php')));
        exit;
    }

    function post_thumbnail_html()
    {
        return '';
    }

    public static function get_instance()
    {
        static $instance = false;

        if ( ! $instance) {
            $instance = new self;
        }

        return $instance;
    }
}