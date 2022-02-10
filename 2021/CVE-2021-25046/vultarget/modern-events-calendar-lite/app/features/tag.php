<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_tag extends MEC_base
{
    public $factory;
    public $main;
    public $settings;
    public $PT;
    public $taxonomy;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();

        // Main Library
        $this->main = $this->getMain();

        // Settings
        $this->settings = $this->main->get_settings();

        // Post Type
        $this->PT = $this->main->get_main_post_type();

        // Taxonomy
        $this->taxonomy = (isset($this->settings['tag_method']) ? $this->settings['tag_method'] : 'post_tag');
    }
    
    /**
     * Initialize search feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        $this->factory->action('init', array($this, 'register_taxonomy'), 100);
        $this->factory->filter('mec_taxonomy_tag', array($this, 'taxonomy'), 10);

        // Toggle Tag Method
        $this->factory->action('mec_tag_method_changed', array($this, 'toggle'), 10, 2);
    }

    public function register_taxonomy($taxonomy = NULL)
    {
        if(!$taxonomy) $taxonomy = $this->taxonomy;

        if($taxonomy === 'post_tag') register_taxonomy_for_object_type('post_tag', $this->PT);
        else
        {
            $singular_label = $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite'));
            $plural_label = $this->main->m('taxonomy_tags', __('Tags', 'modern-events-calendar-lite'));

            register_taxonomy(
                'mec_tag',
                $this->PT,
                array(
                    'label'=>$plural_label,
                    'labels'=>array(
                        'name'=>$plural_label,
                        'singular_name'=>$singular_label,
                        'all_items'=>sprintf(__('All %s', 'modern-events-calendar-lite'), $plural_label),
                        'edit_item'=>sprintf(__('Edit %s', 'modern-events-calendar-lite'), $singular_label),
                        'view_item'=>sprintf(__('View %s', 'modern-events-calendar-lite'), $singular_label),
                        'update_item'=>sprintf(__('Update %s', 'modern-events-calendar-lite'), $singular_label),
                        'add_new_item'=>sprintf(__('Add New %s', 'modern-events-calendar-lite'), $singular_label),
                        'new_item_name'=>sprintf(__('New %s Name', 'modern-events-calendar-lite'), $singular_label),
                        'popular_items'=>sprintf(__('Popular %s', 'modern-events-calendar-lite'), $plural_label),
                        'search_items'=>sprintf(__('Search %s', 'modern-events-calendar-lite'), $plural_label),
                        'back_to_items'=>sprintf(__('← Back to %s', 'modern-events-calendar-lite'), $plural_label),
                        'not_found'=>sprintf(__('no %s found.', 'modern-events-calendar-lite'), strtolower($plural_label)),
                    ),
                    'rewrite'=>array('slug'=>'events-tag'),
                    'public'=>false,
                    'show_ui'=>true,
                    'hierarchical'=>false,
                )
            );

            register_taxonomy_for_object_type('mec_tag', $this->PT);
        }
    }

    public function taxonomy($taxonomy)
    {
        return $this->taxonomy;
    }

    public function toggle($new_method, $old_method)
    {
        // Register New Taxonomy
        $this->register_taxonomy($new_method);

        $events = get_posts(array(
            'post_type' => $this->PT,
            'post_status' => array('publish', 'pending', 'draft', 'future', 'private', 'trash'),
            'numberposts' => -1
        ));

        foreach($events as $event)
        {
            $old_terms = get_the_terms($event, $old_method);
            if(!is_array($old_terms) or (is_array($old_terms) and !count($old_terms))) continue;

            $new_term_ids = array();
            foreach($old_terms as $old_term)
            {
                $term = wp_create_term($old_term->name, $new_method);
                $new_term_id = (int) (is_array($term) ? $term['term_id'] : (is_numeric($term) ? $term : false));

                if(!$new_term_id) continue;
                $new_term_ids[] = $new_term_id;
            }

            if(count($new_term_ids)) wp_set_object_terms($event->ID, $new_term_ids, $new_method);
            wp_delete_object_term_relationships($event->ID, $old_method);
        }
    }
}