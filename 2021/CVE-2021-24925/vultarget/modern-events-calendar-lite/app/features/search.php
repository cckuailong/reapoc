<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_search extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_main
     */
    public $main;

    /**
     * @var MEC_search
     */
    public $search;

    /**
     * @var array
     */
    public $settings;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();
        
        // Import MEC Main
        $this->main = $this->getMain();

        // MEC Settings
        $this->settings = $this->main->get_settings();

        // Search Library
        $this->search = $this->getSearch();
    }
    
    /**
     * Initialize search feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // Search Shortcode
        $this->factory->shortcode('MEC_search_bar', array($this, 'search'));

        if(isset($this->settings['search_bar_ajax_mode']) && $this->settings['search_bar_ajax_mode'] == '1')
        {
            $this->factory->action('wp_ajax_mec_get_ajax_search_data', array($this, 'mec_get_ajax_search_data'));
            $this->factory->action('wp_ajax_nopriv_mec_get_ajax_search_data', array($this, 'mec_get_ajax_search_data'));
        }
        else
        {
            $this->factory->filter('pre_get_posts', array($this, 'mec_search_filter'));
        }

        // Search Narrow
        $this->factory->action('wp_ajax_mec_refine_search_items', array($this->search, 'refine'));
        $this->factory->action('wp_ajax_nopriv_mec_refine_search_items', array($this->search, 'refine'));
    }

    /**
     * Show taxonomy
     * @param string $taxonomy
     * @param string $icon
     * @return boolean|string
     */
    public function show_taxonomy($taxonomy, $icon)
    {
        $terms = get_terms($taxonomy, array('hide_empty' => false));
        $out = '';
        
        if(is_wp_error($terms) || empty($terms)) return false;
        $taxonomy_name = ($taxonomy == apply_filters('mec_taxonomy_tag', '')) ? 'tag' : str_replace('mec_', '', $taxonomy);

        switch($taxonomy_name)
        {
            // Message Category
            case 'category':
                $taxonomy_name = $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite'));
                $taxonomy_key = 'category';
                break;

            // Message Location
            case 'location':
                $taxonomy_name = $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite'));
                $taxonomy_key = 'location';
                break;

            // Message Organizer
            case 'organizer':
                $taxonomy_name = $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite'));
                $taxonomy_key = 'organizer';
                break;

            // Message Organizer
            case 'speaker':
                $taxonomy_name = $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite'));
                $taxonomy_key = 'speaker';
                break;

            // Message Tag
            case 'tag':
                $taxonomy_name =  __('Tag', 'modern-events-calendar-lite');
                $taxonomy_key = 'tag';
                break;

            // Message label
            case 'label':
                $taxonomy_name = $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite'));
                $taxonomy_key = 'label';
                break;

            // Default Screen
            default:
                $taxonomy_name = str_replace('mec_', '', $taxonomy);
                $taxonomy_key = $taxonomy_name;
                break;
        }

        $out .= '<div class="mec-dropdown-search"><i class="mec-sl-'.$icon.'"></i>';
        $args = array(
            'show_option_none'   => $taxonomy_name,
            'option_none_value'  => '',
            'orderby'            => 'name',
            'order'              => 'ASC',
            'show_count'         => 0,
            'hide_empty'         => 0,
            'include'            => ((isset($taxonomy_name) and trim($taxonomy_name)) ? $taxonomy_name : ''),
            'echo'               => false,
            'selected'           => 0,
            'hierarchical'       => true,
            'name'               => $taxonomy_key,
            'taxonomy'           => $taxonomy,
        );

        $out .= wp_dropdown_categories($args);
        $out .= '</div>';

        return $out;
    }

    public function mec_get_ajax_search_data()
    {
        if($_POST['length'] < '3')
        {
            _e('Please enter at least 3 characters and try again', 'modern-events-calendar-lite');
            die();
        }

        $mec_tag_query = NULL;
        $mec_queries = array();

        if(!empty($_POST['location']))
        {
            $location = sanitize_text_field($_POST['location']);
            $mec_queries[] = array(
                'taxonomy'  => 'mec_location',
                'field'     => 'id',
                'terms'     => array($location),
                'operator'  => 'IN'
            );
        }

        if(!empty($_POST['category']))
        {
            $category = sanitize_text_field($_POST['category']);
            $mec_queries[] = array(
                'taxonomy'  => 'mec_category',
                'field'     => 'id',
                'terms'     => array($category),
                'operator'  => 'IN'
            );
        }

        if(!empty($_POST['organizer']))
        {
            $organizer = sanitize_text_field($_POST['organizer']);
            $mec_queries[] = array(
                'taxonomy'  => 'mec_organizer',
                'field'     => 'id',
                'terms'     => array($organizer),
                'operator'  => 'IN'
            );
        }

        if(!empty($_POST['speaker']))
        {
            $speaker = sanitize_text_field($_POST['speaker']);
            $mec_queries[] = array(
                'taxonomy'  => 'mec_speaker',
                'field'     => 'id',
                'terms'     => array($speaker),
                'operator'  => 'IN'
            );
        }

        if(!empty($_POST['tag']))
        {
            $term = get_term_by('id', sanitize_text_field($_POST['tag']), apply_filters('mec_taxonomy_tag', ''));
            if($term) $mec_tag_query = $term->slug;
        }

        if(!empty($_POST['label']))
        {
            $label = sanitize_text_field($_POST['label']);
            $mec_queries[] = array(
                'taxonomy'  => 'mec_label',
                'field'     => 'id',
                'terms'     => array($label),
                'operator'  => 'IN'
            );
        }

        $args = array(
            'tax_query' => $mec_queries,
            's' => esc_attr($_POST['keyword']),
            'post_type' => $this->main->get_main_post_type(),
            'post_status' => array('publish'),
        );

        if($mec_tag_query) $args['tag'] = $mec_tag_query;
        $the_query = new WP_Query($args);

        if($the_query->have_posts())
        {
            while($the_query->have_posts())
            {
                $the_query->the_post();
                include MEC::import('app.features.search_bar.search_result', true, true);
            }

            wp_reset_postdata();
        }
        else
        {
            include MEC::import('app.features.search_bar.search_noresult', true, true);
        }

        die();
    }

    /**
     * Search Filter
     * @param object $query
     * @return string
     */
    public function mec_search_filter($query)
    {
        // Do not change Query if it is not search page!
        if(!$query->is_search) return $query;

        // Do not do anything in Backend
        if(is_admin()) return $query;

        // Do not change anything in Rest API
        if(defined('REST_REQUEST')) return $query;

        // Do not change Query if it is not a search related to MEC!
        if((is_array($query->get('post_type')) and !in_array($this->main->get_main_post_type(), $query->get('post_type'))) or (!is_array($query->get('post_type')) and $query->get('post_type') != 'mec-events')) return $query;

        $mec_tag_query = NULL;
        $mec_queries = array();

        if(!empty($_GET['location']))
        {
            $mec_queries[] = array(
                'taxonomy' => 'mec_location',
                'field' => 'id',
                'terms' => array($_GET['location']),
                'operator'=> 'IN'
            );
        }

        if(!empty($_GET['category']))
        {
            $mec_queries[] = array(
                'taxonomy' => 'mec_category',
                'field' => 'id',
                'terms' => array($_GET['category']),
                'operator'=> 'IN'
            );
        }

        if(!empty($_GET['organizer']))
        {
            $mec_queries[] = array(
                'taxonomy' => 'mec_organizer',
                'field' => 'id',
                'terms' => array($_GET['organizer']),
                'operator'=> 'IN'
            );
        }

        if(!empty($_GET['speaker']))
        {
            $mec_queries[] = array(
                'taxonomy' => 'mec_speaker',
                'field' => 'id',
                'terms' => array($_GET['speaker']),
                'operator'=> 'IN'
            );
        }

        if(!empty($_GET['tag']))
        {
            $term = get_term_by('id', $_GET['tag'], apply_filters('mec_taxonomy_tag', ''));
            if($term) $mec_tag_query = $term->slug;
        }

        if(!empty($_GET['label']))
        {
            $mec_queries[] = array(
                'taxonomy' => 'mec_label',
                'field' => 'id',
                'terms' => array($_GET['label']),
                'operator'=> 'IN'
            );
        }

        if($mec_tag_query) $query->set('tag', $mec_tag_query);
        if(count($mec_queries)) $query->set('tax_query', $mec_queries);

        return $query;
    }

    /**
     * Show user search bar
     * @return string
     */
    public function search()
    {
        $path = MEC::import('app.features.search_bar.search_bar', true, true);

        ob_start();
        include $path;
        return ob_get_clean();
    }
}