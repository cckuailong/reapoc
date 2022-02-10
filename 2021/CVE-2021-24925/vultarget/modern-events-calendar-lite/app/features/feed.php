<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC feed class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_feed extends MEC_base
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
     * @var MEC_feed
     */
    public $feed;
    public $PT;
    public $events;
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
        
        // Import MEC Feed
        $this->feed = $this->getFeed();
        
        // MEC Post Type Name
        $this->PT = $this->main->get_main_post_type();

        // General Settings
        $this->settings = $this->main->get_settings();
    }
    
    /**
     * Initialize feed feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        remove_all_actions('do_feed_rss2');
        $this->factory->action('do_feed_rss2', array($this, 'rss2'), 10, 1);

        // Include Featured Image
        if(!isset($this->settings['include_image_in_feed']) or (isset($this->settings['include_image_in_feed']) and $this->settings['include_image_in_feed']))
        {
            add_filter('get_the_excerpt', array($this, 'include_featured_image'), 10, 2);
        }

        $this->factory->action('init', array($this, 'ical'));
    }
    
    /**
     * Do the feed
     * @author Webnus <info@webnus.biz>
     * @param string $for_comments
     */
    public function rss2($for_comments)
    {
        $rss2 = MEC::import('app.features.feed.rss2', true, true);

        if(get_query_var('post_type') == $this->PT)
        {
            // Fetch Events
            $this->events = $this->fetch();

            // Include Feed template
            include_once $rss2;
        }
        elseif(get_query_var('taxonomy') == 'mec_category')
        {
            $q = get_queried_object();
            $term_id = $q->term_id;

            // Fetch Events
            $this->events = $this->fetch($term_id);

            // Include Feed template
            include_once $rss2;
        }
        else do_feed_rss2($for_comments); // Call default function
    }
    
    /**
     * Returns the events
     * @author Webnus <info@webnus.biz>
     * @param $category
     * @return array
     */
    public function fetch($category = NULL)
    {
        $args = array(
            'sk-options'=>array(
                'list'=>array(
                    'limit'=>get_option('posts_per_rss', 12),
                )
            ),
            'category'=>$category
        );

        $EO = new MEC_skin_list(); // Events Object
        $EO->initialize($args);
        $EO->search();
        
        return $EO->fetch();
    }

    /**
     * @param string $excerpt
     * @param WP_Post $post
     * @return string
     */
    public function include_featured_image($excerpt, $post = NULL)
    {
        // Only RSS
        if(!is_feed()) return $excerpt;

        // Get Current Post
        if(!$post) $post = get_post();
        if(!$post) return $excerpt;

        // It's not event
        if($post->post_type != $this->main->get_main_post_type()) return $excerpt;

        $image = get_the_post_thumbnail($post);
        if(trim($image)) $excerpt = $image.' '.$excerpt;

        return $excerpt;
    }

    public function ical()
    {
        $ical_feed = (isset($_GET['mec-ical-feed']) and $_GET['mec-ical-feed']);
        if(!$ical_feed) return false;

        // Feed is not enabled
        if(!isset($this->settings['ical_feed']) or (isset($this->settings['ical_feed']) and !$this->settings['ical_feed'])) return false;

        $events = $this->main->get_events('-1');

        $output = '';
        foreach($events as $event) $output .= $this->main->ical_single($event->ID);

        // Include in iCal
        $ical_calendar = $this->main->ical_calendar($output);

        // Content Type
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename="mec-events-'.date('YmdTHi').'.ics"');

        // Print the Calendar
        echo $ical_calendar;
        exit;
    }
}