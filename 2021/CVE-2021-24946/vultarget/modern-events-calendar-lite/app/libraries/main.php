<?php
/** no direct access **/
defined('MECEXEC') or die();

use ICal\ICal;
use MEC\Settings\Settings;

/**
 * Webnus MEC main class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_main extends MEC_base
{
    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
    }

    /**
     * Returns the archive URL of events for provided skin
     * @author Webnus <info@webnus.biz>
     * @param string $skin
     * @return string
     */
    public function archive_URL($skin)
    {
        return $this->URL('site').$this->get_main_slug().'/'.$skin.'/';
    }

    /**
     * Returns full current URL of WordPress
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_full_url()
	{
		// get $_SERVER
		$server = $this->getRequest()->get('SERVER');

        // Check protocol
		$page_url = 'http';
		if(isset($server['HTTPS']) and $server['HTTPS'] == 'on') $page_url .= 's';

        // Get domain
        $site_domain = (isset($server['HTTP_HOST']) and trim($server['HTTP_HOST']) != '') ? $server['HTTP_HOST'] : $server['SERVER_NAME'];

		$page_url .= '://';
		$page_url .= $site_domain.$server['REQUEST_URI'];

        // Return full URL
		return $page_url;
	}

    /**
     * Get domain of a certain URL
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @return string
     */
    public function get_domain($url = NULL)
	{
        // Get current URL
        if(is_null($url)) $url = $this->get_full_url();

		$url = str_replace('http://', '', $url);
		$url = str_replace('https://', '', $url);
		$url = str_replace('ftp://', '', $url);
		$url = str_replace('svn://', '', $url);
        $url = str_replace('www.', '', $url);

		$ex = explode('/', $url);
		$ex2 = explode('?', $ex[0]);

		return $ex2[0];
	}

    /**
     * Remove query string from the URL
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param string $url
     * @return string
     */
    public function remove_qs_var($key, $url = '')
	{
		if(trim($url) == '') $url = $this->get_full_url();

		$url = preg_replace('/(.*)(\?|&)'.$key.'=[^&]+?(&)(.*)/i', '$1$2$4', $url .'&');
		$url = substr($url, 0, -1);

		return $url;
	}

    /**
     * Add query string to the URL
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param string $value
     * @param string $url
     * @return string
     */
	public function add_qs_var($key, $value, $url = '')
	{
		if(trim($url) == '') $url = $this->get_full_url();

		$url = preg_replace('/(.*)(\?|&)'.$key.'=[^&]+?(&)(.*)/i', '$1$2$4', $url.'&');
		$url = substr($url, 0, -1);

		if(strpos($url, '?') === false)
			return $url.'?'.$key.'='.$value;
		else
			return $url.'&'.$key.'='.$value;
	}

    /**
     * Add multiple query strings to the URL
     * @author Webnus <info@webnus.biz>
     * @param array $vars
     * @param string $url
     * @return string
     */
    public function add_qs_vars($vars, $url = '')
	{
		if(trim($url) == '') $url = $this->get_full_url();

		foreach($vars as $key=>$value) $url = $this->add_qs_var($key, $value, $url);
        return $url;
	}

    /**
     * Returns WordPress authors
     * @author Webnus <info@webnus.biz>
     * @param array $args
     * @return array
     */
    public function get_authors($args = array())
	{
		return get_users($args);
	}

    /**
     * Returns full URL of an asset
     * @author Webnus <info@webnus.biz>
     * @param string $asset
     * @param boolean $override
     * @return string
     */
	public function asset($asset, $override = true)
	{
		$url = $this->URL('MEC').'assets/'.$asset;

		if($override)
        {
            // Search the file in the main theme
            $theme_path = get_template_directory() .DS. 'webnus' .DS. MEC_DIRNAME .DS. 'assets' .DS. $asset;

            /**
             * If overridden file exists on the main theme, then use it instead of normal file
             * For example you can override /path/to/plugin/assets/js/frontend.js file in your theme by adding a file into the /path/to/theme/webnus/modern-events-calendar/assets/js/frontend.js
             */
            if(file_exists($theme_path)) $url = get_template_directory_uri().'/webnus/'.MEC_DIRNAME.'/assets/'.$asset;

            // If the theme is a child theme then search the file in child theme
            if(get_template_directory() != get_stylesheet_directory())
            {
                // Child theme overriden file
                $child_theme_path = get_stylesheet_directory() .DS. 'webnus' .DS. MEC_DIRNAME .DS. 'assets' .DS. $asset;

                /**
                 * If overridden file exists on the child theme, then use it instead of normal or main theme file
                 * For example you can override /path/to/plugin/assets/js/frontend.js file in your theme by adding a file into the /path/to/child/theme/webnus/modern-events-calendar/assets/js/frontend.js
                 */
                if(file_exists($child_theme_path)) $url = get_stylesheet_directory_uri().'/webnus/'.MEC_DIRNAME.'/assets/'.$asset;
            }
        }

		return $url;
	}

    /**
     * Returns URL of WordPress items such as site, admin, plugins, MEC plugin etc.
     * @author Webnus <info@webnus.biz>
     * @param string $type
     * @return string
     */
	public function URL($type = 'site')
	{
		// Make it lowercase
		$type = strtolower($type);

        // Frontend
		if(in_array($type, array('frontend','site'))) $url = home_url().'/';
        // Backend
		elseif(in_array($type, array('backend','admin'))) $url = admin_url();
        // WordPress Content directory URL
		elseif($type == 'content') $url = content_url().'/';
        // WordPress plugins directory URL
		elseif($type == 'plugin') $url = plugins_url().'/';
        // WordPress include directory URL
		elseif($type == 'include') $url = includes_url();
        // Webnus MEC plugin URL
		elseif($type == 'mec')
		{
            // If plugin installed regularly on plugins directory
			if(!defined('MEC_IN_THEME')) $url = plugins_url().'/'.MEC_DIRNAME.'/';
            // If plugin embeded into one theme
			else $url = get_template_directory_uri().'/plugins/'.MEC_DIRNAME.'/';
		}

		return $url;
	}

    /**
     * Returns plugin absolute path
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_plugin_path()
    {
        return MEC_ABSPATH;
    }

    /**
     * Returns a WordPress option
     * @author Webnus <info@webnus.biz>
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    public function get_option($option, $default = NULL)
    {
        return get_option($option, $default);
    }

    /**
     * Returns WordPress categories based on arguments
     * @author Webnus <info@webnus.biz>
     * @param array $args
     * @return array
     */
    public function get_categories($args = array())
    {
        return get_categories($args);
    }

    /**
     * Returns WordPress tags based on arguments
     * @author Webnus <info@webnus.biz>
     * @param array $args
     * @return array
     */
    public function get_tags($args = array())
    {
        return get_tags($args);
    }

    /**
     * Convert location string to latitude and longitude
     * @author Webnus <info@webnus.biz>
     * @param string $address
     * @return array
     */
    public function get_lat_lng($address)
	{
		$address = urlencode($address);
		if(!trim($address)) return array(0, 0);

        // MEC Settings
        $settings = $this->get_settings();

		$url1 = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address.((isset($settings['google_maps_api_key']) and trim($settings['google_maps_api_key']) != '') ? '&key='.$settings['google_maps_api_key'] : '');
		$url2 = 'http://www.datasciencetoolkit.org/maps/api/geocode/json?sensor=false&address='.$address;

		// Get Latitide and Longitude by First URL
        $JSON = wp_remote_retrieve_body(wp_remote_get($url1, array(
            'body' => null,
            'timeout' => '10',
            'redirection' => '10',
        )));

		$data = json_decode($JSON, true);

		$location_point = isset($data['results'][0]) ? $data['results'][0]['geometry']['location'] : array();
		if((isset($location_point['lat']) and $location_point['lat']) and (isset($location_point['lng']) and $location_point['lng']))
		{
			return array($location_point['lat'], $location_point['lng']);
		}

        // Get Latitide and Longitude by Second URL
        $JSON = wp_remote_retrieve_body(wp_remote_get($url2, array(
            'body' => null,
            'timeout' => '10',
            'redirection' => '10',
        )));

        $data = json_decode($JSON, true);

		$location_point = isset($data['results'][0]) ? $data['results'][0]['geometry']['location'] : array();
		if((isset($location_point['lat']) and $location_point['lat']) and (isset($location_point['lng']) and $location_point['lng']))
		{
			return array($location_point['lat'], $location_point['lng']);
		}

		return array(0, 0);
	}

    /**
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_default_label_color()
    {
        return apply_filters('mec_default_label_color', '#fefefe');
    }

    /**
     * @author Webnus <info@webnus.biz>
     * @param mixed $event
     * @return string
     */
    public function get_post_content($event)
    {
        if(is_object($event)) $event_id = $event->data->ID;
        else $event_id = $event;

        $post = get_post($event_id);
        if(!$post) return NULL;

        $content = apply_filters('the_content', str_replace('[MEC ', '', $post->post_content));
        return str_replace(']]>', ']]&gt;', do_shortcode($content));
    }

    /**
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return array
     */
    public function get_post_meta($post_id)
    {
        $raw_data = get_post_meta($post_id, '', true);
        $data = array();

        // Invalid Raw Data
        if(!is_array($raw_data)) return $data;

        foreach($raw_data as $key=>$val) $data[$key] = isset($val[0]) ? (!is_serialized($val[0]) ? $val[0] : unserialize($val[0])) : NULL;

        return $data;
    }

    /**
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_skins()
    {
        $skins = array
        (
            'list'=>__('List View', 'modern-events-calendar-lite'),
            'grid'=>__('Grid View', 'modern-events-calendar-lite'),
            'agenda'=>__('Agenda View', 'modern-events-calendar-lite'),
            'full_calendar'=>__('Full Calendar', 'modern-events-calendar-lite'),
            'yearly_view'=>__('Yearly View', 'modern-events-calendar-lite'),
            'monthly_view'=>__('Calendar/Monthly View', 'modern-events-calendar-lite'),
            'daily_view'=>__('Daily View', 'modern-events-calendar-lite'),
            'weekly_view'=>__('Weekly View', 'modern-events-calendar-lite'),
            'timetable'=>__('Timetable View', 'modern-events-calendar-lite'),
            'masonry'=>__('Masonry View', 'modern-events-calendar-lite'),
            'map'=>__('Map View', 'modern-events-calendar-lite'),
            'cover'=>__('Cover View', 'modern-events-calendar-lite'),
            'countdown'=>__('Countdown View', 'modern-events-calendar-lite'),
            'available_spot'=>__('Available Spot', 'modern-events-calendar-lite'),
            'carousel'=>__('Carousel View', 'modern-events-calendar-lite'),
            'slider'=>__('Slider View', 'modern-events-calendar-lite'),
            'timeline'=>__('Timeline View', 'modern-events-calendar-lite'),
            'tile'=>__('Tile View', 'modern-events-calendar-lite'),
            // 'general_calendar'=>__('General Calendar', 'modern-events-calendar-lite'),
        );

        return apply_filters('mec_calendar_skins', $skins);
    }

    public function get_months_labels()
    {
        $labels = array(
            1 => date_i18n('F', strtotime(date('Y').'-01-01')),
            2 => date_i18n('F', strtotime(date('Y').'-02-01')),
            3 => date_i18n('F', strtotime(date('Y').'-03-01')),
            4 => date_i18n('F', strtotime(date('Y').'-04-01')),
            5 => date_i18n('F', strtotime(date('Y').'-05-01')),
            6 => date_i18n('F', strtotime(date('Y').'-06-01')),
            7 => date_i18n('F', strtotime(date('Y').'-07-01')),
            8 => date_i18n('F', strtotime(date('Y').'-08-01')),
            9 => date_i18n('F', strtotime(date('Y').'-09-01')),
            10 => date_i18n('F', strtotime(date('Y').'-10-01')),
            11 => date_i18n('F', strtotime(date('Y').'-11-01')),
            12 => date_i18n('F', strtotime(date('Y').'-12-01')),
        );


        return apply_filters('mec_months_labels', $labels);
    }

    /**
     * Returns weekday labels
     * @author Webnus <info@webnus.biz>
     * @param integer $week_start
     * @return array
     */
    public function get_weekday_labels($week_start = NULL)
    {
        if(is_null($week_start)) $week_start = $this->get_first_day_of_week();

        /**
         * Please don't change it to translate-able strings
         */
        $raw = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

        $labels = array_slice($raw, $week_start);
        $rest = array_slice($raw, 0, $week_start);

        foreach($rest as $label) array_push($labels, $label);

        return apply_filters('mec_weekday_labels', $labels);
    }

    /**
     * Returns abbr weekday labels
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_weekday_abbr_labels()
    {
        $week_start = $this->get_first_day_of_week();
        $raw = array(
            $this->m('weekdays_su', __('SU', 'modern-events-calendar-lite')),
            $this->m('weekdays_mo', __('MO', 'modern-events-calendar-lite')),
            $this->m('weekdays_tu', __('TU', 'modern-events-calendar-lite')),
            $this->m('weekdays_we', __('WE', 'modern-events-calendar-lite')),
            $this->m('weekdays_th', __('TH', 'modern-events-calendar-lite')),
            $this->m('weekdays_fr', __('FR', 'modern-events-calendar-lite')),
            $this->m('weekdays_sa', __('SA', 'modern-events-calendar-lite'))
        );

        $labels = array_slice($raw, $week_start);
        $rest = array_slice($raw, 0, $week_start);

        foreach($rest as $label) array_push($labels, $label);

        return apply_filters('mec_weekday_abbr_labels', $labels);
    }

    /**
     * Returns translatable weekday labels
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_weekday_i18n_labels()
    {
        $week_start = $this->get_first_day_of_week();
        $raw = array(array(7, __('Sunday', 'modern-events-calendar-lite')), array(1, __('Monday', 'modern-events-calendar-lite')), array(2, __('Tuesday', 'modern-events-calendar-lite')), array(3, __('Wednesday', 'modern-events-calendar-lite')), array(4, __('Thursday', 'modern-events-calendar-lite')), array(5, __('Friday', 'modern-events-calendar-lite')), array(6, __('Saturday', 'modern-events-calendar-lite')));

        $labels = array_slice($raw, $week_start);
        $rest = array_slice($raw, 0, $week_start);

        foreach($rest as $label) array_push($labels, $label);

        return apply_filters('mec_weekday_i18n_labels', $labels);
    }

    /**
     * Flush WordPress rewrite rules
     * @author Webnus <info@webnus.biz>
     */
    public function flush_rewrite_rules()
    {
        // Register Events Post Type
        $MEC_events = MEC::getInstance('app.features.events', 'MEC_feature_events');
        $MEC_events->register_post_type();

        flush_rewrite_rules();
    }

    /**
     * Get single slug of MEC
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_single_slug()
    {
        $settings = $this->get_settings();
        $slug = (isset($settings['single_slug']) and trim($settings['single_slug']) != '') ? $settings['single_slug'] : 'event';

        return strtolower($slug);
    }

    /**
     * Returns main slug of MEC
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_main_slug()
    {
        $settings = $this->get_settings();
        $slug = (isset($settings['slug']) and trim($settings['slug']) != '') ? $settings['slug'] : 'events';

        return strtolower($slug);
    }

    /**
     * Returns category slug of MEC
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_category_slug()
    {
        $settings = $this->get_settings();
        $slug = (isset($settings['category_slug']) and trim($settings['category_slug']) != '') ? $settings['category_slug'] : 'mec-category';

        return strtolower($slug);
    }

    /**
     * Get archive page title
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_archive_title()
    {
        $settings = $this->get_settings();
        $archive_title = (isset($settings['archive_title']) and trim($settings['archive_title']) != '') ? $settings['archive_title'] : 'Events';

        return apply_filters('mec_archive_title', $archive_title);
    }

    /**
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_archive_thumbnail()
    {
        return apply_filters('mec_archive_thumbnail', '');
    }

    /**
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_single_thumbnail()
    {
        return apply_filters('mec_single_thumbnail', '');
    }

    /**
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_main_post_type()
    {
        return apply_filters('mec_post_type_name', 'mec-events');
    }

    /**
     * Returns main options of MEC
     * @author Webnus <info@webnus.biz>
     * @param string $locale
     * @return array
     */
    public function get_options($locale = NULL)
    {
        if($locale)
        {
            $options = get_option('mec_options_'.strtolower($locale), array());
            if(!is_array($options) or (is_array($options) and !count($options))) $options = get_option('mec_options', array());

            return $options;
        }
        else return get_option('mec_options', array());
    }

    /**
     * Returns MEC settings menus
     * @author Webnus <info@webnus.biz>
     * @param string $active_menu
     * @return void
     */
    public function get_sidebar_menu($active_menu = 'settings')
    {
        $options = $this->get_settings();
        $settings = apply_filters('mec-settings-items-settings', array(
            __('General', 'modern-events-calendar-lite') => 'general_option',
            __('Archive Pages', 'modern-events-calendar-lite') => 'archive_options',
            __('Slugs/Permalinks', 'modern-events-calendar-lite') => 'slug_option',
            __('Currency', 'modern-events-calendar-lite') => 'currency_option',
            __('Assets Per Page', 'modern-events-calendar-lite') => 'assets_per_page_option',
            __('Google Recaptcha', 'modern-events-calendar-lite') => 'recaptcha_option',
            __('Frontend Event Submission', 'modern-events-calendar-lite') => 'fes_option',
            __('User Profile', 'modern-events-calendar-lite') => 'user_profile_options',
            __('User Events', 'modern-events-calendar-lite') => 'user_events_options',
            __('Search Bar', 'modern-events-calendar-lite') => 'search_bar_options',
            __('Email', 'modern-events-calendar-lite') => 'email_option',
        ), $active_menu);

        $integrations = apply_filters('mec-settings-items-integrations', array(
            __('Mailchimp', 'modern-events-calendar-lite') => 'mailchimp_option',
            __('Campaign Monitor', 'modern-events-calendar-lite') => 'campaign_monitor_option',
            __('MailerLite', 'modern-events-calendar-lite') => 'mailerlite_option',
            __('Constant Contact', 'modern-events-calendar-lite') => 'constantcontact_option',
            __('Active Campaign', 'modern-events-calendar-lite') => 'active_campaign_option',
            __('AWeber', 'modern-events-calendar-lite') => 'aweber_option',
            __('MailPoet', 'modern-events-calendar-lite') => 'mailpoet_option',
            __('Sendfox', 'modern-events-calendar-lite') => 'sendfox_option',
            __('BuddyPress', 'modern-events-calendar-lite') => 'buddy_option',
            __('LearnDash', 'modern-events-calendar-lite') => 'learndash_options',
            __('PaidMembership Pro', 'modern-events-calendar-lite') => 'pmp_options',
        ), $active_menu);

        $single_event = apply_filters('mec-settings-item-single_event', array(
            __('Single Event Page', 'modern-events-calendar-lite') => 'event_options',
            __('Custom Fields', 'modern-events-calendar-lite') => 'event_form_option',
            __('Countdown', 'modern-events-calendar-lite') => 'countdown_option',
            __('Exceptional Days', 'modern-events-calendar-lite') => 'exceptional_option',
            __('Additional Organizers', 'modern-events-calendar-lite') => 'additional_organizers',
            __('Additional Locations', 'modern-events-calendar-lite') => 'additional_locations',
            __('Related Events', 'modern-events-calendar-lite') => 'related_events',
            __('Next / Previous Events', 'modern-events-calendar-lite') => 'next_previous_events',
        ), $active_menu);

        $booking = apply_filters('mec-settings-item-booking', array(
            $this->m('booking', __('Booking', 'modern-events-calendar-lite')) => 'booking_option',
            sprintf(__('%s Shortcode', 'modern-events-calendar-lite'), $this->m('booking', __('Booking', 'modern-events-calendar-lite'))) => 'booking_shortcode',
            __('Coupons', 'modern-events-calendar-lite') => 'coupon_option',
            __('Taxes / Fees', 'modern-events-calendar-lite') => 'taxes_option',
            __('Ticket Variations & Options', 'modern-events-calendar-lite') => 'ticket_variations_option',
            sprintf(__('%s Form', 'modern-events-calendar-lite'), $this->m('booking', __('Booking', 'modern-events-calendar-lite'))) => 'booking_form_option',
            __('Upload Field', 'modern-events-calendar-lite') => 'uploadfield_option',
            __('Payment Gateways', 'modern-events-calendar-lite') => 'payment_gateways_option',
        ), $active_menu);

        $modules = apply_filters('mec-settings-item-modules', array(
            __('Speakers', 'modern-events-calendar-lite') => 'speakers_option',
            __('Map', 'modern-events-calendar-lite') => 'googlemap_option',
            __('Export', 'modern-events-calendar-lite') => 'export_module_option',
            __('Local Time', 'modern-events-calendar-lite') => 'time_module_option',
            __('QR Code', 'modern-events-calendar-lite') => 'qrcode_module_option',
            __('Weather', 'modern-events-calendar-lite') => 'weather_module_option',
            __('Social Networks', 'modern-events-calendar-lite') => 'social_options',
            __('Next Event', 'modern-events-calendar-lite') => 'next_event_option',
        ), $active_menu);

        $notifications_items = array(
            __('New Event', 'modern-events-calendar-lite') => 'new_event',
            __('User Event Publishing', 'modern-events-calendar-lite') => 'user_event_publishing',
        );

        if($this->getPro())
        {
            $notifications_items = array_merge(array(
                __('Booking', 'modern-events-calendar-lite') => 'booking_notification_section',
                __('Booking Confirmation', 'modern-events-calendar-lite') => 'booking_confirmation',
                __('Booking Rejection', 'modern-events-calendar-lite') => 'booking_rejection',
                __('Booking Verification', 'modern-events-calendar-lite') => 'booking_verification',
                __('Booking Cancellation', 'modern-events-calendar-lite') => 'cancellation_notification',
                __('Booking Reminder', 'modern-events-calendar-lite') => 'booking_reminder',
                __('Event Soldout', 'modern-events-calendar-lite') => 'event_soldout',
                __('Admin', 'modern-events-calendar-lite') => 'admin_notification',
                __('Event Finished', 'modern-events-calendar-lite') => 'event_finished',
            ), $notifications_items);

            $notifications_items[__('Notifications Per Event', 'modern-events-calendar-lite')] = 'notifications_per_event';
            $notifications_items[__('Notification Template', 'modern-events-calendar-lite')] = 'notification_template';
            $single_event[__('Edit Per Occurrences', 'modern-events-calendar-lite')] = 'per_occurrences';
            $single_event[__('Only For Bookers', 'modern-events-calendar-lite')] = 'shortcode_only_bookers';
            $modules[__('Auto Emails', 'modern-events-calendar-lite')] = 'auto_emails_option';
        }

        $notifications = apply_filters('mec-settings-item-notifications', $notifications_items, $active_menu);
        ?>
        <ul class="wns-be-group-menu">

            <!-- Settings -->
            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'settings' ? 'active' : ''; ?>">
                <a href="<?php echo $this->remove_qs_var('tab'); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-settings"></i>
                    <span class="wns-be-group-menu-title"><?php  _e('Settings', 'modern-events-calendar-lite'); ?></span>
                </a>
                <ul class="<?php echo $active_menu == 'settings' ? 'subsection' : 'mec-settings-submenu'; ?>">
                <?php foreach($settings as $settings_name => $settings_link): ?>
                <?php
                if($settings_link == 'mailchimp_option' || $settings_link == 'active_campaign_option' || $settings_link == 'mailpoet_option' || $settings_link == 'sendfox_option' || $settings_link == 'aweber_option' || $settings_link == 'campaign_monitor_option' || $settings_link == 'mailerlite_option' || $settings_link == 'constantcontact_option' || $settings_link == 'buddy_option' || $settings_link == 'learndash_options' || $settings_link == 'pmp_options' ):
                    if($this->getPRO()): ?>
                    <li>
                        <a
                        <?php if($active_menu == 'settings'): ?>
                        data-id="<?php echo $settings_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                        <?php else: ?>
                        href="<?php echo $this->remove_qs_var('tab') . '#' . $settings_link; ?>"
                        <?php endif; ?>
                        >
                        <span class="pr-be-group-menu-title"><?php echo $settings_name; ?></span>
                        </a>
                    </li>
                <?php
                    endif;
                else: ?>
                <li>
                    <a
                    <?php if($active_menu == 'settings'): ?>
                    data-id="<?php echo $settings_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                    <?php else: ?>
                    href="<?php echo $this->remove_qs_var('tab') . '#' . $settings_link; ?>"
                    <?php endif; ?>
                    >
                    <span class="pr-be-group-menu-title"><?php echo $settings_name; ?></span>
                    </a>
                </li>
                <?php endif; ?>
                <?php endforeach; ?>
                </ul>
            </li>

            <!-- Integrations -->
            <?php if($this->getPRO()): ?>
            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'integrations' ? 'active' : ''; ?>">
                <a href="<?php echo $this->add_qs_var('tab', 'MEC-integrations'); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-wrench"></i>
                    <span class="wns-be-group-menu-title"><?php  _e('Integrations', 'modern-events-calendar-lite'); ?></span>
                </a>
                <ul class="<?php echo $active_menu == 'integrations' ? 'subsection' : 'mec-settings-submenu'; ?>">
                <?php foreach ($integrations as $integrations_name => $integrations_link) : ?>

                    <li>
                        <a
                        <?php if($active_menu == 'integrations'): ?>
                        data-id="<?php echo $integrations_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                        <?php else: ?>
                        href="<?php echo $this->add_qs_var('tab', 'MEC-integrations') . '#' . $integrations_link; ?>"
                        <?php endif; ?>
                        >
                        <span class="pr-be-group-menu-title"><?php echo $integrations_name; ?></span>
                        </a>
                    </li>

                <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- Single Event -->
            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'single_event' ? 'active' : ''; ?>">
                <a href="<?php echo $this->add_qs_var('tab', 'MEC-single'); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-event"></i>
                    <span class="wns-be-group-menu-title"><?php  _e('Single Event', 'modern-events-calendar-lite'); ?></span>
                </a>
                <ul class="<?php echo $active_menu == 'single_event' ? 'subsection' : 'mec-settings-submenu'; ?>">
                <?php foreach ($single_event as $single_event_name => $single_event_link) : ?>
                    <li>
                        <a
                        <?php if($active_menu == 'single_event'): ?>
                        data-id="<?php echo $single_event_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                        <?php else: ?>
                        href="<?php echo $this->add_qs_var('tab', 'MEC-single') . '#' . $single_event_link; ?>"
                        <?php endif; ?>
                        >
                        <span class="pr-be-group-menu-title"><?php echo $single_event_name; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </li>

            <!-- Booking -->
            <?php if($this->getPRO()): ?>
            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'booking' ? 'active' : ''; ?>">
                <a href="<?php echo $this->add_qs_var('tab', 'MEC-booking'); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-wallet"></i>
                    <span class="wns-be-group-menu-title"><?php echo $this->m('booking', __('Booking', 'modern-events-calendar-lite')); ?></span>
                </a>
                <ul class="<?php echo $active_menu == 'booking' ? 'subsection' : 'mec-settings-submenu'; ?>">

                <?php foreach($booking as $booking_name => $booking_link): ?>
                <?php if($booking_link == 'coupon_option' || $booking_link == 'taxes_option' || $booking_link == 'ticket_variations_option' || $booking_link == 'booking_form_option' || $booking_link == 'uploadfield_option' || $booking_link == 'payment_gateways_option' || $booking_link == 'booking_shortcode'): ?>
                    <?php if(isset($options['booking_status']) and $options['booking_status']): ?>
                    <li>
                        <a
                        <?php if($active_menu == 'booking'): ?>
                        data-id="<?php echo $booking_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                        <?php else: ?>
                        href="<?php echo $this->add_qs_var('tab', 'MEC-booking') . '#' . $booking_link; ?>"
                        <?php endif; ?>
                        >
                        <span class="pr-be-group-menu-title"><?php echo $booking_name; ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li>
                        <a
                        <?php if($active_menu == 'booking'): ?>
                        data-id="<?php echo $booking_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                        <?php else: ?>
                        href="<?php echo $this->add_qs_var('tab', 'MEC-booking') . '#' . $booking_link; ?>"
                        <?php endif; ?>
                        >
                        <span class="pr-be-group-menu-title"><?php echo $booking_name; ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>

			<!-- Custom Menus -->
			<?php do_action('mec_settings_sidebar', $active_menu ); ?>

            <!-- Modules -->
            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'modules' ? 'active' : ''; ?>">
                <a href="<?php echo $this->add_qs_var('tab', 'MEC-modules'); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-grid"></i>
                    <span class="wns-be-group-menu-title"><?php  _e('Modules', 'modern-events-calendar-lite'); ?></span>
                </a>
                <ul class="<?php echo $active_menu == 'modules' ? 'subsection' : 'mec-settings-submenu'; ?>">

                <?php foreach($modules as $modules_name => $modules_link): ?>
                <?php if($modules_link == 'googlemap_option' || $modules_link == 'qrcode_module_option' || $modules_link == 'weather_module_option' ): ?>
                    <?php if($this->getPRO()): ?>
                    <li>
                        <a
                        <?php if($active_menu == 'modules'): ?>
                        data-id="<?php echo $modules_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                        <?php else: ?>
                        href="<?php echo $this->add_qs_var('tab', 'MEC-modules') . '#' . $modules_link; ?>"
                        <?php endif; ?>
                        >
                        <span class="pr-be-group-menu-title"><?php echo $modules_name; ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li>
                        <a
                        <?php if($active_menu == 'modules'): ?>
                        data-id="<?php echo $modules_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                        <?php else: ?>
                        href="<?php echo $this->add_qs_var('tab', 'MEC-modules') . '#' . $modules_link; ?>"
                        <?php endif; ?>
                        >
                        <span class="pr-be-group-menu-title"><?php echo $modules_name; ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php endforeach; ?>
                </ul>
            </li>

            <!-- Notifications -->
            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'notifications' ? 'active' : ''; ?>">
                <a href="<?php echo $this->add_qs_var('tab', 'MEC-notifications').(!$this->getPRO() ? '#new_event' : ''); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-envelope-open"></i>
                    <span class="wns-be-group-menu-title"><?php  _e('Notifications', 'modern-events-calendar-lite'); ?></span>
                </a>
                <ul class="<?php echo $active_menu == 'notifications' ? 'subsection' : 'mec-settings-submenu'; ?>">

                <?php foreach($notifications as $notifications_name => $notifications_link): ?>
                <?php if($notifications_link != 'new_event' and $notifications_link != 'user_event_publishing' ): ?>
                    <?php if((isset($options['booking_status']) and $options['booking_status']) || false !== strpos($notifications_link,'rsvp')): ?>
                    <li>
                        <a
                        <?php if($active_menu == 'notifications'): ?>
                        data-id="<?php echo $notifications_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                        <?php else: ?>
                        href="<?php echo $this->add_qs_var('tab', 'MEC-notifications') . '#' . $notifications_link; ?>"
                        <?php endif; ?>
                        >
                        <span class="pr-be-group-menu-title"><?php echo $notifications_name; ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li>
                        <a
                        <?php if($active_menu == 'notifications'): ?>
                        data-id="<?php echo $notifications_link; ?>" class="wns-be-group-tab-link-a WnTabLinks"
                        <?php else: ?>
                        href="<?php echo $this->add_qs_var('tab', 'MEC-notifications') . '#' . $notifications_link; ?>"
                        <?php endif; ?>
                        >
                        <span class="pr-be-group-menu-title"><?php echo $notifications_name; ?></span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php endforeach; ?>
                </ul>
            </li>

            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'styling' ? 'active' : ''; ?>">
                <a href="<?php echo $this->add_qs_var('tab', 'MEC-styling'); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-equalizer"></i>
                    <span class="wns-be-group-menu-title"><?php _e('Styling Options', 'modern-events-calendar-lite'); ?></span>
                </a>
            </li>

            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'customcss' ? 'active' : ''; ?>">
                <a href="<?php echo $this->add_qs_var('tab', 'MEC-customcss'); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-pencil"></i>
                    <span class="wns-be-group-menu-title"><?php _e('Custom CSS', 'modern-events-calendar-lite'); ?></span>
                </a>
            </li>

            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'messages' ? 'active' : ''; ?>">
                <a href="<?php echo $this->add_qs_var('tab', 'MEC-messages'); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-speech"></i>
                    <span class="wns-be-group-menu-title"><?php _e('Messages', 'modern-events-calendar-lite'); ?></span>
                </a>
            </li>

            <li class="wns-be-group-menu-li mec-settings-menu <?php echo $active_menu == 'ie' ? 'active' : ''; ?>">
                <a href="<?php echo $this->add_qs_var('tab', 'MEC-ie'); ?>" id="" class="wns-be-group-tab-link-a">
                    <i class="mec-sl-refresh"></i>
                    <span class="wns-be-group-menu-title"><?php _e('Import / Export', 'modern-events-calendar-lite'); ?></span>
                </a>
            </li>
        </ul>  <!-- close wns-be-group-menu -->
        <script type="text/javascript">
        jQuery(document).ready(function()
        {
            if(jQuery('.mec-settings-menu').hasClass('active'))
            {
                jQuery('.mec-settings-menu.active').find('ul li:first-of-type').addClass('active');
            }

            jQuery('.WnTabLinks').each(function()
            {
                var ContentId = jQuery(this).attr('data-id');
                jQuery(this).click(function()
                {
                    jQuery('.wns-be-sidebar li ul li').removeClass('active');
                    jQuery(this).parent().addClass('active');
                    jQuery(".mec-options-fields").hide();
                    jQuery(".mec-options-fields").removeClass('active');
                    jQuery("#"+ContentId+"").show();
                    jQuery("#"+ContentId+"").addClass('active');
                    if(jQuery("#wns-be-infobar").hasClass("sticky"))
                    {
                        jQuery('html, body').animate({
                            scrollTop: jQuery("#"+ContentId+"").offset().top - 140
                        }, 300);
                    }
                });

                var hash = window.location.hash.replace('#', '');
                jQuery('[data-id="'+hash+'"]').trigger('click');
            });

            jQuery(".wns-be-sidebar li ul li").on('click', function(event)
            {
                jQuery(".wns-be-sidebar li ul li").removeClass('active');
                jQuery(this).addClass('active');
            });
        });
        </script>
    <?php
    }

    /**
     * Returns MEC settings
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_settings()
    {
        $options = $this->get_options();
        return (isset($options['settings']) ? $options['settings'] : array());
    }

    /**
     * Returns MEC addons message
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function addons_msg()
    {
        $get_n_option = get_option('mec_addons_notification_option');
        if($get_n_option == 'open') return '';

        return '
        <div class="w-row mec-addons-notification-wrap">
            <div class="w-col-sm-12">
                <div class="w-clearfix w-box mec-addons-notification-box-wrap">
                    <div class="w-box-head">'.__('New Addons For MEC! Now Customize MEC in Elementor', 'modern-events-calendar-lite').'<span><i class="mec-sl-close"></i></span></div>
                    <div class="w-box-content">
                        <div class="mec-addons-notification-box-image">
                            <img src="'. plugin_dir_url(__FILE__ ) . '../../assets/img/mec-addons-teaser1.png" />
                        </div>
                        <div class="mec-addons-notification-box-content">
                            <div class="w-box-content">
                                <p>'.__('The time has come at last, and the new practical add-ons for MEC have been released. This is a revolution in the world of Event Calendars. We have provided you with a wide range of features only by having the 4 add-ons below:' , 'modern-events-calendar-lite').'</p>
                                <ol>
                                    <li>'.__('<strong>WooCommerce Integration:</strong> You can now purchase ticket (as products) and Woo products at the same time.' , 'modern-events-calendar-lite').'</li>
                                    <li>'.__('<strong>Event API:</strong> display your events (shortcodes/single event) on other websites without MEC.  Use JSON output features to make your Apps compatible with MEC.' , 'modern-events-calendar-lite').'</li>
                                    <li>'.__('<strong>Multisite Event Sync:</strong> Sync events between your subsites and main websites. Changes in the main one will be inherited by the subsites. you can set these up in the admin panel.' , 'modern-events-calendar-lite').'</li>
                                    <li>'.__('<strong>User Dashboard:</strong> Create exclusive pages for users. These pages can contain ticket purchase information, information about registered events. Users can now log in to purchase tickets.', 'modern-events-calendar-lite').'</li>
                                </ol>
                                <a href="https://webnus.net/modern-events-calendar/addons/?ref=17" target="_blank">'.esc_html__('find out more', 'modern-events-calendar-lite').'</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ';
    }

        /**
     * Returns MEC custom message 2
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function mec_custom_msg_2($display_option = '', $message = '')
    {
        $get_cmsg_display_option = get_option('mec_custom_msg_2_display_option');
        $get_mec_saved_message_time = get_option('mec_saved_message_2_time');

        if(!isset($get_mec_saved_message_time)):
            $data_url = 'https://webnus.net/modern-events-calendar/addons-api/mec-extra-content-2.json';
            if(function_exists('file_get_contents') && ini_get('allow_url_fopen') )
            {
                $ctx = stream_context_create(array('http'=>
                    array(
                        'timeout' => 20,
                    )
                ));
                $get_data = file_get_contents($data_url, false, $ctx);
                if ( $get_data !== false AND !empty($get_data) )
                {
                    $obj = json_decode($get_data);
                    $i = count((array)$obj);
                }
            }
            elseif ( function_exists('curl_version') )
            {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20); //timeout in seconds
                curl_setopt($ch, CURLOPT_URL, $data_url);
                $result = curl_exec($ch);
                curl_close($ch);
                $obj = json_decode($result);
                $i = count((array)$obj);
            } else {
                $obj = '';
            }
            update_option('mec_saved_message_2_time', date("Y-m-d"));
        else:
            if ( strtotime(date("Y-m-d")) > strtotime($get_mec_saved_message_time) ) {
                $data_url = 'https://webnus.net/modern-events-calendar/addons-api/mec-extra-content-2.json';
                if(function_exists('file_get_contents') && ini_get('allow_url_fopen') )
                {
                    $ctx = stream_context_create(array('http'=>
                        array(
                            'timeout' => 20,
                        )
                    ));
                    $get_data = file_get_contents($data_url, false, $ctx);
                    if ( $get_data !== false AND !empty($get_data) )
                    {
                        $obj = json_decode($get_data);
                        $i = count((array)$obj);
                    }
                }
                elseif ( function_exists('curl_version') )
                {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 20); //timeout in seconds
                    curl_setopt($ch, CURLOPT_URL, $data_url);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $obj = json_decode($result);
                    $i = count((array)$obj);
                } else {
                    $obj = '';
                }
                update_option('mec_saved_message_2_time', date("Y-m-d"));
            } else {
                $mec_custom_msg_html = get_option('mec_custom_msg_2_html');
                $mec_custom_msg_display = get_option('mec_custom_msg_2_display');
                if ( $get_cmsg_display_option != $mec_custom_msg_display ) :
                    update_option( 'mec_custom_msg_2_display_option', $mec_custom_msg_display );
                    update_option('mec_custom_msg_2_close_option', 'close');
                    update_option('mec_saved_message_2_time', date("Y-m-d"));
                    return $mec_custom_msg_html;
                elseif ( $get_cmsg_display_option == $mec_custom_msg_display ) :
                    $get_cmsg_close_option = get_option('mec_custom_msg_2_close_option');
                    update_option('mec_saved_message_2_time', date("Y-m-d"));
                    if ( $get_cmsg_close_option == 'open' ) return;
                    return $mec_custom_msg_html;
                endif;
            }
        endif;

        if ( !empty( $obj ) ) :
            foreach ($obj as $key => $value) {
                $html = $value->html;
                update_option('mec_custom_msg_2_html', $html);
                $display = $value->display;
                update_option('mec_custom_msg_2_display', $display);
            }

            if ( $get_cmsg_display_option != $display ) :
                update_option( 'mec_custom_msg_2_display_option', $display );
                update_option('mec_custom_msg_2_close_option', 'close');
                return $html;
            elseif ( $get_cmsg_display_option == $display ) :
                $get_cmsg_close_option = get_option('mec_custom_msg_2_close_option');
                if ( $get_cmsg_close_option == 'open' ) return;
                return $html;
            endif;
        else:
            return '';
        endif;
    }

    /**
     * Returns MEC custom message
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function mec_custom_msg($display_option = '', $message = '')
    {
        $get_cmsg_display_option = get_option('mec_custom_msg_display_option');
        $get_mec_saved_message_time = get_option('mec_saved_message_time');

        if(!isset($get_mec_saved_message_time)):
            $data_url = 'https://webnus.net/modern-events-calendar/addons-api/mec-extra-content.json';
            if(function_exists('file_get_contents') && ini_get('allow_url_fopen') )
            {
                $ctx = stream_context_create(array('http'=>
                    array(
                        'timeout' => 20,
                    )
                ));
                $get_data = file_get_contents($data_url, false, $ctx);
                if ( $get_data !== false AND !empty($get_data) )
                {
                    $obj = json_decode($get_data);
                    $i = count((array)$obj);
                }
            }
            elseif ( function_exists('curl_version') )
            {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20); //timeout in seconds
                curl_setopt($ch, CURLOPT_URL, $data_url);
                $result = curl_exec($ch);
                curl_close($ch);
                $obj = json_decode($result);
                $i = count((array)$obj);
            } else {
                $obj = '';
            }
            update_option('mec_saved_message_time', date("Y-m-d"));
        else:
            if ( strtotime(date("Y-m-d")) > strtotime($get_mec_saved_message_time) ) {
                $data_url = 'https://webnus.net/modern-events-calendar/addons-api/mec-extra-content.json';
                if(function_exists('file_get_contents') && ini_get('allow_url_fopen') )
                {
                    $ctx = stream_context_create(array('http'=>
                        array(
                            'timeout' => 20,
                        )
                    ));
                    $get_data = file_get_contents($data_url, false, $ctx);
                    if ( $get_data !== false AND !empty($get_data) )
                    {
                        $obj = json_decode($get_data);
                        $i = count((array)$obj);
                    }
                }
                elseif ( function_exists('curl_version') )
                {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 20); //timeout in seconds
                    curl_setopt($ch, CURLOPT_URL, $data_url);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $obj = json_decode($result);
                    $i = count((array)$obj);
                } else {
                    $obj = '';
                }
                update_option('mec_saved_message_time', date("Y-m-d"));
            } else {
                $mec_custom_msg_html = get_option('mec_custom_msg_html');
                $mec_custom_msg_display = get_option('mec_custom_msg_display');
                if ( $get_cmsg_display_option != $mec_custom_msg_display ) :
                    update_option( 'mec_custom_msg_display_option', $mec_custom_msg_display );
                    update_option('mec_custom_msg_close_option', 'close');
                    update_option('mec_saved_message_time', date("Y-m-d"));
                    return $mec_custom_msg_html;
                elseif ( $get_cmsg_display_option == $mec_custom_msg_display ) :
                    $get_cmsg_close_option = get_option('mec_custom_msg_close_option');
                    update_option('mec_saved_message_time', date("Y-m-d"));
                    if ( $get_cmsg_close_option == 'open' ) return;
                    return $mec_custom_msg_html;
                endif;
            }
        endif;

        if ( !empty( $obj ) ) :
            foreach ($obj as $key => $value) {
                $html = $value->html;
                update_option('mec_custom_msg_html', $html);
                $display = $value->display;
                update_option('mec_custom_msg_display', $display);
            }

            if ( $get_cmsg_display_option != $display ) :
                update_option( 'mec_custom_msg_display_option', $display );
                update_option('mec_custom_msg_close_option', 'close');
                return $html;
            elseif ( $get_cmsg_display_option == $display ) :
                $get_cmsg_close_option = get_option('mec_custom_msg_close_option');
                if ( $get_cmsg_close_option == 'open' ) return;
                return $html;
            endif;
        else:
            return '';
        endif;
    }
    /**
     * Returns MEC settings
     * @author Webnus <info@webnus.biz>
     * @return array
     */
     public function get_default_form()
     {
         $options = $this->get_options();
         return (isset($options['default_form']) ? $options['default_form'] : array());
     }

    /**
     * Returns registration form fields
     * @author Webnus <info@webnus.biz>
     * @param integer $event_id
     * @param integer $translated_event_id
     * @return array
     */
    public function get_reg_fields($event_id = NULL, $translated_event_id = NULL)
    {
        $options = $this->get_options();
        $reg_fields = isset($options['reg_fields']) ? $options['reg_fields'] : array();

        // Event Booking Fields
        if($event_id)
        {
            $global_inheritance = get_post_meta($event_id, 'mec_reg_fields_global_inheritance', true);
            if(trim($global_inheritance) == '') $global_inheritance = 1;

            if(!$global_inheritance)
            {
                $event_reg_fields = get_post_meta($event_id, 'mec_reg_fields', true);
                if(is_array($event_reg_fields)) $reg_fields = $event_reg_fields;

                // We're getting fields for a translated event
                if($translated_event_id and $event_id != $translated_event_id)
                {
                    $translated_reg_fields = get_post_meta($translated_event_id, 'mec_reg_fields', true);
                    if(!is_array($translated_reg_fields)) $translated_reg_fields = array();

                    foreach($translated_reg_fields as $field_id=>$translated_reg_field)
                    {
                        if(!isset($reg_fields[$field_id])) continue;
                        if(isset($translated_reg_field['label']) and trim($translated_reg_field['label'])) $reg_fields[$field_id]['label'] = $translated_reg_field['label'];
                        if(isset($translated_reg_field['options']) and is_array($translated_reg_field['options']))$reg_fields[$field_id]['options'] = $translated_reg_field['options'];
                    }
                }
            }
        }

        return apply_filters('mec_get_reg_fields', $reg_fields, $event_id);
    }

    /**
     * Returns booking fixed fields
     * @author Webnus <info@webnus.biz>
     * @param integer $event_id
     * @param integer $translated_event_id
     * @return array
     */
    public function get_bfixed_fields($event_id = NULL, $translated_event_id = NULL)
    {
        $options = $this->get_options();
        $bfixed_fields = isset($options['bfixed_fields']) ? $options['bfixed_fields'] : array();

        // Event Fields
        if($event_id)
        {
            $global_inheritance = get_post_meta($event_id, 'mec_reg_fields_global_inheritance', true);
            if(trim($global_inheritance) == '') $global_inheritance = 1;

            if(!$global_inheritance)
            {
                $event_bfixed_fields = get_post_meta($event_id, 'mec_bfixed_fields', true);
                if(is_array($event_bfixed_fields)) $bfixed_fields = $event_bfixed_fields;

                // We're getting fields for a translated event
                if($translated_event_id and $event_id != $translated_event_id)
                {
                    $translated_bfixed_fields = get_post_meta($translated_event_id, 'mec_bfixed_fields', true);
                    if(!is_array($translated_bfixed_fields)) $translated_bfixed_fields = array();

                    foreach($translated_bfixed_fields as $field_id=>$translated_bfixed_field)
                    {
                        if(!isset($bfixed_fields[$field_id])) continue;
                        if(isset($translated_bfixed_field['label']) and trim($translated_bfixed_field['label'])) $bfixed_fields[$field_id]['label'] = $translated_bfixed_field['label'];
                        if(isset($translated_bfixed_field['options']) and is_array($translated_bfixed_field['options']))$bfixed_fields[$field_id]['options'] = $translated_bfixed_field['options'];
                    }
                }
            }
        }

        return apply_filters('mec_get_bfixed_fields', $bfixed_fields, $event_id);
    }

    /**
     * Returns event form fields
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_event_fields()
    {
        $options = $this->get_options();
        $event_fields = isset($options['event_fields']) ? $options['event_fields'] : array();

        return apply_filters('mec_get_event_fields', $event_fields);
    }

    /**
     * Returns Ticket Variations
     * @author Webnus <info@webnus.biz>
     * @param integer $event_id
     * @param integer $ticket_id
     * @return array
     */
    public function ticket_variations($event_id = NULL, $ticket_id = NULL)
    {
        $settings = $this->get_settings();
        $ticket_variations = (isset($settings['ticket_variations']) and is_array($settings['ticket_variations'])) ? $settings['ticket_variations'] : array();

        // Event Ticket Variations
        if($event_id)
        {
            $global_inheritance = get_post_meta($event_id, 'mec_ticket_variations_global_inheritance', true);
            if(trim($global_inheritance) == '') $global_inheritance = 1;

            if(!$global_inheritance)
            {
                $event_ticket_variations = get_post_meta($event_id, 'mec_ticket_variations', true);
                if(is_array($event_ticket_variations)) $ticket_variations = $event_ticket_variations;
            }

            // Variations Per Ticket
            if($ticket_id)
            {
                $tickets = get_post_meta($event_id, 'mec_tickets', true);
                $ticket = ((isset($tickets[$ticket_id]) and is_array($tickets[$ticket_id])) ? $tickets[$ticket_id] : array());

                $event_inheritance = (isset($ticket['variations_event_inheritance']) ? $ticket['variations_event_inheritance'] : 1);
                if(!$event_inheritance and isset($ticket['variations']) and is_array($ticket['variations'])) $ticket_variations = $ticket['variations'];
            }
        }

        // Clean
        if(isset($ticket_variations[':i:'])) unset($ticket_variations[':i:']);
        if(isset($ticket_variations[':v:'])) unset($ticket_variations[':v:']);

        return $ticket_variations;
    }

    public function has_variations_per_ticket($event_id, $ticket_id)
    {
        $has = false;

        $tickets = get_post_meta($event_id, 'mec_tickets', true);
        $ticket = ((isset($tickets[$ticket_id]) and is_array($tickets[$ticket_id])) ? $tickets[$ticket_id] : array());

        $event_inheritance = (isset($ticket['variations_event_inheritance']) ? $ticket['variations_event_inheritance'] : 1);
        if(!$event_inheritance and isset($ticket['variations']) and is_array($ticket['variations'])) $has = true;

        return $has;
    }

    /**
     * Returns Messages Options
     * @author Webnus <info@webnus.biz>
     * @param string $locale
     * @return array
     */
    public function get_messages_options($locale = NULL)
    {
        if($this->is_multilingual() and !$locale) $locale = $this->get_current_language();

        $options = $this->get_options($locale);
        return (isset($options['messages']) ? $options['messages'] : array());
    }

    /**
     * Returns gateways options
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_gateways_options()
    {
        $options = $this->get_options();
        return (isset($options['gateways']) ? $options['gateways'] : array());
    }
    /**
     * Returns notifications settings of MEC
     * @author Webnus <info@webnus.biz>
     * @param string $locale
     * @return array
     */
    public function get_notifications($locale = NULL)
    {
        if($this->is_multilingual() and !$locale) $locale = $this->get_current_language();

        $options = $this->get_options($locale);
        return (isset($options['notifications']) ? $options['notifications'] : array());
    }

    /**
     * Returns Import/Export options of MEC
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_ix_options()
    {
        $options = $this->get_options();
        return (isset($options['ix']) ? $options['ix'] : array());
    }

    /**
     * Returns style settings of MEC
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_styles()
    {
        $options = $this->get_options();
        return (isset($options['styles']) ? $options['styles'] : array());
    }

    /**
     * Returns styling option of MEC
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_styling()
    {
        $options = $this->get_options();
        return (isset($options['styling']) ? $options['styling'] : array());
    }

    /**
     * Saves MEC settings
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function save_options()
    {
        // MEC Request library
        $request = $this->getRequest();

        $wpnonce = $request->getVar('_wpnonce', NULL);

        // Check if our nonce is set.
        if(!trim($wpnonce)) $this->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'mec_options_form')) $this->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        // Current User is not Permitted
        if(!current_user_can('mec_settings') and !current_user_can('administrator')) $this->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));

        // Get mec options
        $mec = $request->getVar('mec', array());
        if(isset($mec['reg_fields']) and !is_array($mec['reg_fields'])) $mec['reg_fields'] = array();
        if(isset($mec['bfixed_fields']) and !is_array($mec['bfixed_fields'])) $mec['bfixed_fields'] = array();
        if(isset($mec['event_fields']) and !is_array($mec['event_fields'])) $mec['event_fields'] = array();

        $filtered = array();
        foreach($mec as $key=>$value) $filtered[$key] = (is_array($value) ? $value : array());

        // Get current MEC options
        $current = get_option('mec_options', array());
        if(is_string($current) and trim($current) == '') $current = array();

        // Validations
        if(isset($filtered['settings']) and isset($filtered['settings']['slug'])) $filtered['settings']['slug'] = strtolower(str_replace(' ', '-', $filtered['settings']['slug']));
        if(isset($filtered['settings']) and isset($filtered['settings']['category_slug'])) $filtered['settings']['category_slug'] = strtolower(str_replace(' ', '-', $filtered['settings']['category_slug']));
        if(isset($filtered['settings']) and isset($filtered['settings']['custom_archive'])) $filtered['settings']['custom_archive'] = isset($filtered['settings']['custom_archive']) ? str_replace('\"', '"', $filtered['settings']['custom_archive']) : '';

        // Bellow conditional block codes is used for sortable booking form items.
        if(isset($filtered['reg_fields']))
        {
            if(!is_array($filtered['reg_fields'])) $filtered['reg_fields'] = array();
        }

        if(isset($current['reg_fields']) and isset($filtered['reg_fields']))
        {
            $current['reg_fields'] = array();
            $current['reg_fields'] = $filtered['reg_fields'];
        }

        // Bellow conditional block codes is used for sortable booking fixed form items.
        if(isset($filtered['bfixed_fields']))
        {
            if(!is_array($filtered['bfixed_fields'])) $filtered['bfixed_fields'] = array();
        }

        if(isset($current['bfixed_fields']) and isset($filtered['bfixed_fields']))
        {
            $current['bfixed_fields'] = array();
            $current['bfixed_fields'] = $filtered['bfixed_fields'];
        }

        // Bellow conditional block codes is used for sortable event form items.
        if(isset($filtered['event_fields']))
        {
            if(!is_array($filtered['event_fields'])) $filtered['event_fields'] = array();
        }

        if(isset($current['event_fields']) and isset($filtered['event_fields']))
        {
            $current['event_fields'] = array();
            $current['event_fields'] = $filtered['event_fields'];
        }

        // Tag Method Changed
        $old_tag_method = ((isset($current['settings']) and isset($current['settings']['tag_method'])) ? $current['settings']['tag_method'] : 'post_tag');
        if(isset($filtered['settings']) and isset($filtered['settings']['tag_method']) and $filtered['settings']['tag_method'] != $old_tag_method)
        {
            do_action('mec_tag_method_changed', $filtered['settings']['tag_method'], $old_tag_method);
        }

        // Generate New Options
        $final = $current;

        // Merge new options with previous options
        foreach($filtered as $key=>$value)
        {
            if(is_array($value))
            {
                foreach($value as $k=>$v)
                {
                    // Define New Array
                    if(!isset($final[$key])) $final[$key] = array();

                    // Overwrite Old Value
                    $final[$key][$k] = $v;
                }
            }
            // Overwrite Old Value
            else $final[$key] = $value;
        }

        $final = apply_filters('mec_save_options_final', $final);

        // MEC Save Options
        do_action('mec_save_options', $final);

        // Save final options
        update_option('mec_options', $final);

        // Refresh WordPress rewrite rules
        $this->flush_rewrite_rules();

        // Print the response
        $this->response(array('success'=>1));
    }

    /**
     * Saves MEC Notifications
     * @author Webnus <info@webnus.biz>
     */
    public function save_notifications()
    {
        // MEC Request library
        $request = $this->getRequest();

        $wpnonce = $request->getVar('_wpnonce', NULL);

        // Check if our nonce is set.
        if(!trim($wpnonce)) $this->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'mec_options_form')) $this->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        // Current User is not Permitted
        if(!current_user_can('mec_settings') and !current_user_can('administrator')) $this->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));

        // Locale
        $locale = $request->getVar('mec_locale', NULL);

        // MEC Request library
        $request = $this->getRequest();

        // Get mec options
        $mec = $request->getVar('mec', 'POST');
        $notifications = isset($mec['notifications']) ? $mec['notifications'] : array();
        $settings = isset($mec['settings']) ? $mec['settings'] : array();

        // Get current MEC notifications
        $current = $this->get_notifications($locale);
        if(is_string($current) and trim($current) == '') $current = array();

        // Merge new options with previous options
        $final_notifications = array();
        $final_notifications['notifications'] = array_merge($current, $notifications);

        $core_options = get_option('mec_options', array());
        if(isset($core_options['settings']) and is_array($core_options['settings'])) $final_notifications['settings'] = array_merge($core_options['settings'], $settings);

        // Get current MEC options
        $options = array();

        if($this->is_multilingual() and $locale) $options = get_option('mec_options_'.strtolower($locale), array());
        if(!is_array($options) or (is_array($options) and !count($options))) $options = get_option('mec_options', array());
        if(is_string($options) and trim($options) == '') $options = array();

        // Merge new options with previous options
        $final = array_merge($options, $final_notifications);

        if($this->is_multilingual() and $locale)
        {
            // Save final options
            update_option('mec_options_'.strtolower($locale), $final);

            $default_locale = $this->get_current_language();
            if($default_locale === $locale) update_option('mec_options', $final);
        }
        else
        {
            // Save final options
            update_option('mec_options', $final);
        }

        // Save final options
        update_option('mec_options', $final);

        // Print the response
        $this->response(array('success'=>1));
    }

    /**
     * Saves MEC settings
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function save_messages()
    {
        // MEC Request library
        $request = $this->getRequest();

        // Security Nonce
        $wpnonce = $request->getVar('_wpnonce', NULL);

        // Check if our nonce is set.
        if(!trim($wpnonce)) $this->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'mec_options_form')) $this->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        // Current User is not Permitted
        if(!current_user_can('mec_settings') and !current_user_can('administrator')) $this->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));

        // Locale
        $locale = $request->getVar('mec_locale', NULL);

        // Get mec options
        $mec = $request->getVar('mec', array());
        $messages = isset($mec['messages']) ? $mec['messages'] : array();

        // Get current MEC options
        $current = $this->get_messages_options($locale);
        if(is_string($current) and trim($current) == '') $current = array();

        // Merge new options with previous options
        $final_messages = array();
        $final_messages['messages'] = array_merge($current, $messages);

        // Get current MEC options
        $options = array();

        if($this->is_multilingual() and $locale) $options = get_option('mec_options_'.strtolower($locale), array());
        if(!is_array($options) or (is_array($options) and !count($options))) $options = get_option('mec_options', array());
        if(is_string($options) and trim($options) == '') $options = array();

        // Merge new options with previous options
        $final = array_merge($options, $final_messages);

        // Multilingual
        if($this->is_multilingual() and $locale)
        {
            // Save final options
            update_option('mec_options_'.strtolower($locale), $final);

            $default_locale = $this->get_current_language();
            if($default_locale === $locale) update_option('mec_options', $final);
        }
        else
        {
            // Save final options
            update_option('mec_options', $final);
        }

        // Print the response
        $this->response(array('success'=>1));
    }

    /**
     * Saves MEC Import/Export options
     * @author Webnus <info@webnus.biz>
     * @param array $ix_options
     * @return boolean
     */
    public function save_ix_options($ix_options = array())
    {
        // Current User is not Permitted
        if(!current_user_can('mec_import_export')) $this->response(array('success'=>0, 'code'=>'ADMIN_ONLY'));

        // Get current MEC ix options
        $current = $this->get_ix_options();
        if(is_string($current) and trim($current) == '') $current = array();

        // Merge new options with previous options
        $final_ix = array();
        $final_ix['ix'] = array_merge($current, $ix_options);

        // Get current MEC options
        $options = get_option('mec_options', array());
        if(is_string($options) and trim($options) == '') $options = array();

        // Merge new options with previous options
        $final = array_merge($options, $final_ix);

        // Save final options
        update_option('mec_options', $final);

        return true;
    }

    /**
     * Get first day of week from WordPress
     * @author Webnus <info@webnus.biz>
     * @return int
     */
    public function get_first_day_of_week()
    {
        return get_option('start_of_week', 1);
    }

    /**
     * @author Webnus <info@webnus.biz>
     * @param array $response
     * @return void
     */
    public function response($response)
    {
        wp_send_json($response);
    }

    /**
     * Check if a date passed or not
     * @author Webnus <info@webnus.biz>
     * @param mixed $end
     * @param mixed $now
     * @return int
     */
    public function is_past($end, $now)
    {
        if(!is_numeric($end)) $end = strtotime($end);
        if(!is_numeric($now)) $now = strtotime($now);

        // Never End
        if($end <= 0) return 0;

        return (int) ($now > $end);
    }

    /**
     * @author Webnus <info@webnus.biz>
     * @param int $id
     * @return string
     */
    public function get_weekday_name_by_day_id($id)
    {
        // These names will be used in PHP functions so they mustn't translate
        $days = array(1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday');
        return $days[$id];
    }

    /**
     * Spilts 2 dates to weeks
     * @author Webnus <info@webnus.biz>
     * @param DateTime|String $start
     * @param DateTime|String $end
     * @param int $first_day_of_week
     * @return array
     */
    public function split_to_weeks($start, $end, $first_day_of_week = NULL)
    {
        if(is_null($first_day_of_week)) $first_day_of_week = $this->get_first_day_of_week();

        $end_day_of_week = ($first_day_of_week-1 >= 0) ? $first_day_of_week-1 : 6;

        $start_time = strtotime($start);
        $end_time = strtotime($end);

        $start = new DateTime(date('Y-m-d', $start_time));
        $end = new DateTime(date('Y-m-d 23:59', $end_time));

        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($start, $interval, $end);

        $weekday = 0;
        $weekNumber = 1;
        $weeks = array();
        foreach($dateRange as $date)
        {
            // Fix the PHP notice
            if(!isset($weeks[$weekNumber])) $weeks[$weekNumber] = array();

            // It's first week and the week is not started from first weekday
            if($weekNumber == 1 and $weekday == 0 and $date->format('w') != $first_day_of_week)
            {
                $remained_days = $date->format('w');

                if($first_day_of_week == 0) $remained_days = $date->format('w'); // Sunday
                elseif($first_day_of_week == 1) // Monday
                {
                    if($remained_days != 0) $remained_days = $remained_days - 1;
                    else $remained_days = 6;
                }
                elseif($first_day_of_week == 6) // Saturday
                {
                    if($remained_days != 6) $remained_days = $remained_days + 1;
                    else $remained_days = 0;
                }
                elseif($first_day_of_week == 5) // Friday
                {
                    if($remained_days < 4) $remained_days = $remained_days + 2;
                    elseif($remained_days == 5) $remained_days = 0;
                    elseif($remained_days == 6) $remained_days = 1;
                }

                $interval = new DateInterval('P'.$remained_days.'D');
                $interval->invert = 1;
                $date->add($interval);

                for($i = $remained_days; $i > 0; $i--)
                {
                    $weeks[$weekNumber][] = $date->format('Y-m-d');
                    $date->add(new DateInterval('P1D'));
                }
            }

            $weeks[$weekNumber][] = $date->format('Y-m-d');
            $weekday++;

            if($date->format('w') == $end_day_of_week)
            {
                $weekNumber++;
                $weekday = 0;
            }
        }

        // Month is finished but week is not finished
        if($weekday > 0 and $weekday < 7)
        {
            $remained_days = (6 - $weekday);
            for($i = 0; $i <= $remained_days; $i++)
            {
                $date->add(new DateInterval('P1D'));
                $weeks[$weekNumber][] = $date->format('Y-m-d');

                if($date->format('w') == $end_day_of_week) $weekNumber++;
            }
        }

        return $weeks;
    }

    /**
     * Returns MEC Container Width
     * @author Webnus <info@webnus.biz>
     */
    public function get_container_width()
    {
        $settings = $this->get_settings();
        $container_width = (isset($settings['container_width']) and trim($settings['container_width']) != '') ? $settings['container_width'] : '';
        update_option('mec_container_width', $container_width);
    }

    /**
     * Returns MEC colors
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_available_colors()
    {
        $colors = get_option('mec_colors', $this->get_default_colors());
        return apply_filters('mec_available_colors', $colors);
    }

    /**
     * Returns MEC default colors
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_default_colors()
    {
        return apply_filters('mec_default_colors', array('fdd700','00a0d2','e14d43','dd823b','a3b745'));
    }

    /**
     * Add a new color to MEC available colors
     * @author Webnus <info@webnus.biz>
     * @param string $color
     */
    public function add_to_available_colors($color)
    {
        $colors = $this->get_available_colors();
        $colors[] = $color;

        $colors = array_unique($colors);
        update_option('mec_colors', $colors);
    }

    /**
     * Returns available googlemap styles
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_googlemap_styles()
    {
        $styles = array(
            array('key'=>'light-dream.json', 'name'=>'Light Dream'),
            array('key'=>'intown-map.json', 'name'=>'inTown Map'),
            array('key'=>'midnight.json', 'name'=>'Midnight'),
            array('key'=>'pale-down.json', 'name'=>'Pale Down'),
            array('key'=>'blue-essence.json', 'name'=>'Blue Essence'),
            array('key'=>'blue-water.json', 'name'=>'Blue Water'),
            array('key'=>'apple-maps-esque.json', 'name'=>'Apple Maps Esque'),
            array('key'=>'CDO.json', 'name'=>'CDO'),
            array('key'=>'shades-of-grey.json', 'name'=>'Shades of Grey'),
            array('key'=>'subtle-grayscale.json', 'name'=>'Subtle Grayscale'),
            array('key'=>'ultra-light.json', 'name'=>'Ultra Light'),
            array('key'=>'facebook.json', 'name'=>'Facebook'),
        );

        return apply_filters('mec_googlemap_styles', $styles);
    }

    /**
     * Filters provided google map styles
     * @author Webnus <info@webnus.biz>
     * @param string $style
     * @return string
     */
    public function get_googlemap_style($style)
    {
        return apply_filters('mec_get_googlemap_style', $style);
    }

    /**
     * Fetchs googlemap styles from file
     * @author Webnus <info@webnus.biz>
     * @param string $style
     * @return string
     */
    public function fetch_googlemap_style($style)
    {
        $path = $this->get_plugin_path().'app'.DS.'modules'.DS.'googlemap'.DS.'styles'.DS.$style;

        // MEC file library
        $file = $this->getFile();

        if($file->exists($path)) return trim($file->read($path));
        else return '';
    }

    /**
     * Get marker infowindow for showing on the map
     * @author Webnus <info@webnus.biz>
     * @param array $marker
     * @return string
     */
    public function get_marker_infowindow($marker)
    {
        $count = count($marker['event_ids']);

        $content = '
        <div class="mec-marker-infowindow-wp">
            <div class="mec-marker-infowindow-count">'.$count.'</div>
            <div class="mec-marker-infowindow-content">
                <span>'.($count > 1 ? __('Events at this location', 'modern-events-calendar-lite') : __('Event at this location', 'modern-events-calendar-lite')).'</span>
                <span>'.(trim($marker['address']) ? $marker['address'] : $marker['name']).'</span>
            </div>
        </div>';

        return apply_filters('mec_get_marker_infowindow', $content);
    }

    /**
     * Get marker Lightbox for showing on the map
     * @author Webnus <info@webnus.biz>
     * @param object $event
     * @param string $date_format
     * @return string
     */
    public function get_marker_lightbox($event, $date_format = 'M d Y')
    {
        $link = $this->get_event_date_permalink($event, (isset($event->date['start']) ? $event->date['start']['date'] : NULL));
        $infowindow_thumb = trim($event->data->featured_image['thumbnail']) ? '<div class="mec-event-image"><a data-event-id="'.$event->data->ID.'" href="'.$link.'"><img src="'.$event->data->featured_image['thumbnail'].'" alt="'.$event->data->title.'" /></a></div>' : '';
        $event_start_date = !empty($event->date['start']['date']) ? $event->date['start']['date'] : '';
        $event_start_date_day = !empty($event->date['start']['date']) ? $this->date_i18n('d', strtotime($event->date['start']['date'])) : '';
        $event_start_date_month = !empty($event->date['start']['date']) ? $this->date_i18n('M', strtotime($event->date['start']['date'])) : '';
        $event_start_date_year = !empty($event->date['start']['date']) ? $this->date_i18n('Y', strtotime($event->date['start']['date'])) : '';
        $start_time = !empty($event->data->time['start']) ? $event->data->time['start'] : '';
        $end_time = !empty($event->data->time['end']) ? $event->data->time['end'] : '';

        $content = '
		<div class="mec-wrap">
			<div class="mec-map-lightbox-wp mec-event-list-classic">
				<article class="'.((isset($event->data->meta['event_past']) and trim($event->data->meta['event_past'])) ? 'mec-past-event ' : '').'mec-event-article mec-clear">
					'.$infowindow_thumb.'
                    <a data-event-id="'.$event->data->ID.'" href="'.$link.'"><div class="mec-event-date mec-color"><i class="mec-sl-calendar"></i> <span class="mec-map-lightbox-month">'.$event_start_date_month. '</span><span class="mec-map-lightbox-day"> ' . $event_start_date_day . '</span><span class="mec-map-lightbox-year"> ' . $event_start_date_year .  '</span></div></a>
                    <h4 class="mec-event-title">
                    <div class="mec-map-time" style="display: none">'.$this->display_time($start_time, $end_time).'</div>
                    <a data-event-id="'.$event->data->ID.'" class="mec-color-hover" href="'.$link.'">'.$event->data->title.'</a>
                    '.$this->get_flags($event).'
                    </h4>
				</article>
			</div>
		</div>';

        return apply_filters('mec_get_marker_lightbox', $content);
    }

    /**
     * Returns available social networks
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_social_networks()
    {
        $social_networks = array(
            'facebook'=>array('id'=>'facebook', 'name'=>__('Facebook', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_facebook')),
            'twitter'=>array('id'=>'twitter', 'name'=>__('Twitter', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_twitter')),
            'linkedin'=>array('id'=>'linkedin', 'name'=>__('Linkedin', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_linkedin')),
            'vk'=>array('id'=>'vk', 'name'=>__('VK', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_vk')),
            'tumblr'=>array('id'=>'tumblr', 'name'=>__('Tumblr', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_tumblr')),
            'pinterest'=>array('id'=>'pinterest', 'name'=>__('Pinterest', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_pinterest')),
            'flipboard'=>array('id'=>'flipboard', 'name'=>__('Flipboard', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_flipboard')),
            'pocket'=>array('id'=>'pocket', 'name'=>__('GetPocket', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_pocket')),
            'reddit'=>array('id'=>'reddit', 'name'=>__('Reddit', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_reddit')),
            'whatsapp'=>array('id'=>'whatsapp', 'name'=>__('WhatsApp', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_whatsapp')),
            'telegram'=>array('id'=>'telegram', 'name'=>__('Telegram', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_telegram')),
            'email'=>array('id'=>'email', 'name'=>__('Email', 'modern-events-calendar-lite'), 'function'=>array($this, 'sn_email')),
        );

        return apply_filters('mec_social_networks', $social_networks);
    }

    /**
     * Do facebook link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_facebook($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($url).'" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600\'); return false;" title="'.__('Share on Facebook', 'modern-events-calendar-lite').'"><i class="mec-fa-facebook"></i></a></li>';
    }

    /**
     * Do twitter link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_twitter($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="twitter" href="https://twitter.com/share?url='.rawurlencode($url).'" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=500\'); return false;" target="_blank" title="'.__('Tweet', 'modern-events-calendar-lite').'"><i class="mec-fa-twitter"></i></a></li>';
    }

    /**
     * Do linkedin link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_linkedin($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url='.rawurlencode($url).'" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=500\'); return false;" target="_blank" title="'.__('Linkedin', 'modern-events-calendar-lite').'"><i class="mec-fa-linkedin"></i></a></li>';
    }

    /**
     * Do email link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_email($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        $event->data->title = str_replace('&#8211;', '-', $event->data->title);
        $event->data->title = str_replace('&#8221;', '’’', $event->data->title);
        $event->data->title = str_replace('&#8217;', "’", $event->data->title);
        $event->data->title = str_replace('&', '%26', $event->data->title);
        $event->data->title = str_replace('#038;', '', $event->data->title);

        return '<li class="mec-event-social-icon"><a class="email" href="mailto:?subject='.wp_specialchars_decode($event->data->title).'&body='.rawurlencode($url).'" title="'.__('Email', 'modern-events-calendar-lite').'"><i class="mec-fa-envelope"></i></a></li>';
    }

    /**
     * Do VK link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_vk($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="vk" href=" http://vk.com/share.php?url='.rawurlencode($url).'" title="'.__('VK', 'modern-events-calendar-lite').'" target="_blank"><i class="mec-fa-vk"></i></a></li>';
    }


    /**
     * Do tumblr link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_tumblr($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);
        return '<li class="mec-event-social-icon"><a class="tumblr" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl='.rawurlencode($url).'&title'.wp_specialchars_decode($event->data->title).'&caption='.wp_specialchars_decode($event->data->title).'" title="'.__('Share on Tumblr', 'modern-events-calendar-lite').'"><i class="mec-fa-tumblr"></i></a></li>';

    }

    /**
     * Do pinterest link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_pinterest($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="pinterest" href="http://pinterest.com/pin/create/button/?url='.rawurlencode($url).'" title="'.__('Share on Pinterest', 'modern-events-calendar-lite').'"><i class="mec-fa-pinterest"></i></a></li>';

    }

    /**
     * Do flipboard link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_flipboard($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="flipboard" href="https://share.flipboard.com/bookmarklet/popout?v=2&title'.wp_specialchars_decode($event->data->title).'=&url='.rawurlencode($url).'" title="'.__('Share on Flipboard', 'modern-events-calendar-lite').'">
        <svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="flipboard" class="svg-inline--fa fa-flipboard fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M0 32v448h448V32H0zm358.4 179.2h-89.6v89.6h-89.6v89.6H89.6V121.6h268.8v89.6z"></path></svg>
        </a></li>';

    }

    /**
     * Do pocket link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_pocket($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="pocket" href="https://getpocket.com/edit?url='.rawurlencode($url).'" title="'.__('Share on GetPocket', 'modern-events-calendar-lite').'"><i class="mec-fa-get-pocket"></i></a></li>';

    }

    /**
     * Do reddit link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_reddit($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="reddit" href="https://reddit.com/submit?url='.rawurlencode($url).'&title='.wp_specialchars_decode($event->data->title).'" title="'.__('Share on Reddit', 'modern-events-calendar-lite').'"><i class="mec-fa-reddit"></i></a></li>';

    }

    /**
     * Do telegram link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_telegram($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="telegram" href="https://telegram.me/share/url?url='.rawurlencode($url).'&text='.wp_specialchars_decode($event->data->title).'" title="'.__('Share on Telegram', 'modern-events-calendar-lite').'">
        <svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="telegram" class="svg-inline--fa fa-telegram fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><path fill="currentColor" d="M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm121.8 169.9l-40.7 191.8c-3 13.6-11.1 16.9-22.4 10.5l-62-45.7-29.9 28.8c-3.3 3.3-6.1 6.1-12.5 6.1l4.4-63.1 114.9-103.8c5-4.4-1.1-6.9-7.7-2.5l-142 89.4-61.2-19.1c-13.3-4.2-13.6-13.3 2.8-19.7l239.1-92.2c11.1-4 20.8 2.7 17.2 19.5z"></path></svg>
        </a></li>';

    }

    /**
     * Do whatsapp link for social networks
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param object $event
     * @return string
     */
    public function sn_whatsapp($url, $event)
    {
        $occurrence = (isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '');
        if(trim($occurrence) != '') $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return '<li class="mec-event-social-icon"><a class="whatsapp" href="whatsapp://send/?text='.rawurlencode($url).'" title="'.__('Share on WhatsApp', 'modern-events-calendar-lite').'"><i class="mec-fa-whatsapp"></i></a></li>';

    }

    /**
     * Get available skins for archive page
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_archive_skins()
    {
        $archive_skins = array(
            array('skin'=>'full_calendar', 'name'=>__('Full Calendar', 'modern-events-calendar-lite')),
            array('skin'=>'yearly_view', 'name'=>__('Yearly View', 'modern-events-calendar-lite')),
            array('skin'=>'monthly_view', 'name'=>__('Calendar/Monthly View', 'modern-events-calendar-lite')),
            array('skin'=>'weekly_view', 'name'=>__('Weekly View', 'modern-events-calendar-lite')),
            array('skin'=>'daily_view', 'name'=>__('Daily View', 'modern-events-calendar-lite')),
            array('skin'=>'timetable', 'name'=>__('Timetable View', 'modern-events-calendar-lite')),
            array('skin'=>'masonry', 'name'=>__('Masonry View', 'modern-events-calendar-lite')),
            array('skin'=>'list', 'name'=>__('List View', 'modern-events-calendar-lite')),
            array('skin'=>'grid', 'name'=>__('Grid View', 'modern-events-calendar-lite')),
            array('skin'=>'agenda', 'name'=>__('Agenda View', 'modern-events-calendar-lite')),
            array('skin'=>'map', 'name'=>__('Map View', 'modern-events-calendar-lite')),
            // array('skin'=>'general_calendar', 'name'=>__('General Calendar', 'modern-events-calendar-lite')),
            array('skin'=>'custom', 'name'=>__('Custom Shortcode', 'modern-events-calendar-lite')),
        );

        return apply_filters('mec_archive_skins', $archive_skins);
    }

    /**
     * Get available skins for archive page
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_category_skins()
    {
        $category_skins = array(
            array('skin'=>'full_calendar', 'name'=>__('Full Calendar', 'modern-events-calendar-lite')),
            array('skin'=>'yearly_view', 'name'=>__('Yearly View', 'modern-events-calendar-lite')),
            array('skin'=>'monthly_view', 'name'=>__('Calendar/Monthly View', 'modern-events-calendar-lite')),
            array('skin'=>'weekly_view', 'name'=>__('Weekly View', 'modern-events-calendar-lite')),
            array('skin'=>'daily_view', 'name'=>__('Daily View', 'modern-events-calendar-lite')),
            array('skin'=>'timetable', 'name'=>__('Timetable View', 'modern-events-calendar-lite')),
            array('skin'=>'masonry', 'name'=>__('Masonry View', 'modern-events-calendar-lite')),
            array('skin'=>'list', 'name'=>__('List View', 'modern-events-calendar-lite')),
            array('skin'=>'grid', 'name'=>__('Grid View', 'modern-events-calendar-lite')),
            array('skin'=>'agenda', 'name'=>__('Agenda View', 'modern-events-calendar-lite')),
            array('skin'=>'map', 'name'=>__('Map View', 'modern-events-calendar-lite')),
            // array('skin'=>'general_calendar', 'name'=>__('General Calendar', 'modern-events-calendar-lite')),
            array('skin'=>'custom', 'name'=>__('Custom Shortcode', 'modern-events-calendar-lite')),
        );

        return apply_filters('mec_category_skins', $category_skins);
    }

    /**
     * Get events posts
     * @author Webnus <info@webnus.biz>
     * @param int $limit
     * @return array list of posts
     */
    public function get_events($limit = -1)
    {
        return get_posts(array('post_type'=>$this->get_main_post_type(), 'numberposts'=>$limit, 'post_status'=>'publish'));
    }

    /**
     * Get id of upcoming events
     * @author Webnus <info@webnus.biz>
     * @param int $now
     * @return array
     */
    public function get_upcoming_event_ids($now = NULL)
    {
        // Database Object
        $db = $this->getDB();

        // Current Timestamp
        $start = (($now and is_numeric($now)) ? $now : current_time('timestamp', 0));

        $ids = $db->select("SELECT `post_id` FROM `#__mec_dates` WHERE `tstart` >= ".$start, 'loadColumn');
        return array_unique($ids);
    }

    /**
     * Get id of events by period
     * @author Webnus <info@webnus.biz>
     * @param string|int $start
     * @param string|int $end
     * @return array
     */
    public function get_event_ids_by_period($start, $end)
    {
        if(!is_numeric($start)) $start = strtotime($start);
        if(!is_numeric($end)) $end = strtotime($end);

        // Database Object
        $db = $this->getDB();

        $ids = $db->select("SELECT `post_id` FROM `#__mec_dates` WHERE (`tstart` <= ".$start." AND `tend` >= ".$end.") OR (`tstart` > ".$start." AND `tend` < ".$end.") OR (`tstart` > ".$start." AND `tstart` < ".$end." AND `tend` >= ".$end.") OR (`tstart` <= ".$start." AND `tend` > ".$start." AND `tend` < ".$end.")", 'loadColumn');
        return $ids;
    }

    /**
     * Get method of showing for multiple days events
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_multiple_days_method()
    {
        $settings = $this->get_settings();

        $method = isset($settings['multiple_day_show_method']) ? $settings['multiple_day_show_method'] : 'first_day_listgrid';
        return apply_filters('mec_multiple_days_method', $method);
    }

    /**
     * Get method of showing/hiding events based on event time
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_hide_time_method()
    {
        $settings = $this->get_settings();

        $method = isset($settings['hide_time_method']) ? $settings['hide_time_method'] : 'start';
        return apply_filters('mec_hide_time_method', $method);
    }

    /**
     * Get hour format of MEC
     * @author Webnus <info@webnus.biz>
     * @return int|string
     */
    public function get_hour_format()
    {
        $settings = $this->get_settings();

        $format = isset($settings['time_format']) ? $settings['time_format'] : 12;
        return apply_filters('mec_hour_format', $format);
    }

    /**
     * Get formatted hour based on configurations
     * @author Webnus <info@webnus.biz>
     * @param int $hour
     * @param int $minutes
     * @param string $ampm
     * @return string
     */
    public function get_formatted_hour($hour, $minutes, $ampm)
    {
        // Hour Format of MEC (12/24)
        $hour_format = $this->get_hour_format();

        $formatted = '';
        if($hour_format == '12')
        {
            $formatted = sprintf("%02d", $hour).':'.sprintf("%02d", $minutes).' '.__($ampm, 'modern-events-calendar-lite');
        }
        elseif($hour_format == '24')
        {
            if(strtoupper($ampm) == 'PM' and $hour != 12) $hour += 12;
            if(strtoupper($ampm) == 'AM' and $hour == 12) $hour += 12;

            $formatted = sprintf("%02d", $hour).':'.sprintf("%02d", $minutes);
        }

        return $formatted;
    }

    /**
     * Get formatted time based on WordPress Time Format
     * @author Webnus <info@webnus.biz>
     * @param int $seconds
     * @return string
     */
    public function get_time($seconds)
    {
        $format = get_option('time_format');
        return gmdate($format, $seconds);
    }

    /**
     * Renders a module such as links or googlemap
     * @author Webnus <info@webnus.biz>
     * @param string $module
     * @param array $params
     * @return string
     */
    public function module($module, $params = array())
    {
        // Get module path
        $path = MEC::import('app.modules.'.$module, true, true);

        // MEC libraries
        $render = $this->getRender();
        $factory = $this->getFactory();

        // Extract Module Params
        extract($params);

        ob_start();
        include $path;
        return $output = ob_get_clean();
    }

    /**
     * Returns MEC currencies
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_currencies()
    {
        $currencies = array(
            '$'=>'USD',
            '€'=>'EUR',
            '£'=>'GBP',
            'CHF'=>'CHF',
            'CAD'=>'CAD',
            'AUD'=>'AUD',
            'JPY'=>'JPY',
            'SEK'=>'SEK',
            'GEL'=>'GEL',
            'AFN'=>'AFN',
            'ALL'=>'ALL',
            'DZD'=>'DZD',
            'AOA'=>'AOA',
            'ARS'=>'ARS',
            'AMD'=>'AMD',
            'AWG'=>'AWG',
            'AZN'=>'AZN',
            'BSD'=>'BSD',
            'BHD'=>'BHD',
            'BBD'=>'BBD',
            'BYR'=>'BYR',
            'BZD'=>'BZD',
            'BMD'=>'BMD',
            'BTN'=>'BTN',
            'BOB'=>'BOB',
            'BAM'=>'BAM',
            'BWP'=>'BWP',
            'BRL'=>'BRL',
            'BND'=>'BND',
            'BGN'=>'BGN',
            'BIF'=>'BIF',
            'KHR'=>'KHR',
            'CVE'=>'CVE',
            'KYD'=>'KYD',
            'XAF'=>'XAF',
            'CLP'=>'CLP',
            'COP'=>'COP',
            'KMF'=>'KMF',
            'CDF'=>'CDF',
            'NZD'=>'NZD',
            'CRC'=>'CRC',
            'HRK'=>'HRK',
            'CUC'=>'CUC',
            'CUP'=>'CUP',
            'CZK'=>'CZK',
            'DKK'=>'DKK',
            'DJF'=>'DJF',
            'DOP'=>'DOP',
            'XCD'=>'XCD',
            'EGP'=>'EGP',
            'ERN'=>'ERN',
            'EEK'=>'EEK',
            'ETB'=>'ETB',
            'FKP'=>'FKP',
            'FJD'=>'FJD',
            'GMD'=>'GMD',
            'GHS'=>'GHS',
            'GIP'=>'GIP',
            'GTQ'=>'GTQ',
            'GNF'=>'GNF',
            'GYD'=>'GYD',
            'HTG'=>'HTG',
            'HNL'=>'HNL',
            'HKD'=>'HKD',
            'HUF'=>'HUF',
            'ISK'=>'ISK',
            'INR'=>'INR',
            'IDR'=>'IDR',
            'IRR'=>'IRR',
            'IQD'=>'IQD',
            'ILS'=>'ILS',
            'NIS' => 'NIS',
            'JMD'=>'JMD',
            'JOD'=>'JOD',
            'KZT'=>'KZT',
            'KES'=>'KES',
            'KWD'=>'KWD',
            'KGS'=>'KGS',
            'LAK'=>'LAK',
            'LVL'=>'LVL',
            'LBP'=>'LBP',
            'LSL'=>'LSL',
            'LRD'=>'LRD',
            'LYD'=>'LYD',
            'LTL'=>'LTL',
            'MOP'=>'MOP',
            'MKD'=>'MKD',
            'MGA'=>'MGA',
            'MWK'=>'MWK',
            'MYR'=>'MYR',
            'MVR'=>'MVR',
            'MRO'=>'MRO',
            'MUR'=>'MUR',
            'MXN'=>'MXN',
            'MDL'=>'MDL',
            'MNT'=>'MNT',
            'MAD'=>'MAD',
            'MZN'=>'MZN',
            'MMK'=>'MMK',
            'NAD'=>'NAD',
            'NPR'=>'NPR',
            'ANG'=>'ANG',
            'TWD'=>'TWD',
            'NIO'=>'NIO',
            'NGN'=>'NGN',
            'KPW'=>'KPW',
            'NOK'=>'NOK',
            'OMR'=>'OMR',
            'PKR'=>'PKR',
            'PAB'=>'PAB',
            'PGK'=>'PGK',
            'PYG'=>'PYG',
            'PEN'=>'PEN',
            'PHP'=>'PHP',
            'PLN'=>'PLN',
            'QAR'=>'QAR',
            'CNY'=>'CNY',
            'RON'=>'RON',
            'RUB'=>'RUB',
            'RWF'=>'RWF',
            'SHP'=>'SHP',
            'SVC'=>'SVC',
            'WST'=>'WST',
            'SAR'=>'SAR',
            'RSD'=>'RSD',
            'SCR'=>'SCR',
            'SLL'=>'SLL',
            'SGD'=>'SGD',
            'SBD'=>'SBD',
            'SOS'=>'SOS',
            'ZAR'=>'ZAR',
            'KRW'=>'KRW',
            'LKR'=>'LKR',
            'SDG'=>'SDG',
            'SRD'=>'SRD',
            'SZL'=>'SZL',
            'SYP'=>'SYP',
            'STD'=>'STD',
            'TJS'=>'TJS',
            'TZS'=>'TZS',
            'THB'=>'THB',
            'TOP'=>'TOP',
            'PRB'=>'PRB',
            'TTD'=>'TTD',
            'TND'=>'TND',
            'TRY'=>'TRY',
            'TMT'=>'TMT',
            'TVD'=>'TVD',
            'UGX'=>'UGX',
            'UAH'=>'UAH',
            'AED'=>'AED',
            'UYU'=>'UYU',
            'UZS'=>'UZS',
            'VUV'=>'VUV',
            'VEF'=>'VEF',
            'VND'=>'VND',
            'XOF'=>'XOF',
            'YER'=>'YER',
            'ZMK'=>'ZMK',
            'ZWL'=>'ZWL',
        );

        return apply_filters('mec_currencies', $currencies);
    }

    /**
     * Returns MEC version
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_version()
    {
        $version = MEC_VERSION;

        if(defined('WP_DEBUG') and WP_DEBUG) $version .= '.'.time();
        return $version;
    }

    /**
     * Set endpoint vars to true
     * @author Webnus <info@webnus.biz>
     * @param array $vars
     * @return array
     */
    public function filter_request($vars)
    {
        if(isset($vars['gateway-cancel'])) $vars['gateway-cancel'] = true;
        if(isset($vars['gateway-return'])) $vars['gateway-return'] = true;
        if(isset($vars['gateway-notify'])) $vars['gateway-notify'] = true;

        return $vars;
    }

    /**
     * Do the jobs after endpoints and show related output
     * @author Webnus <info@webnus.biz>
     * @return boolean
     */
    public function do_endpoints()
    {
        if(get_query_var('verify'))
        {
            $key = sanitize_text_field(get_query_var('verify'));

            $db = $this->getDB();
            $book_id = $db->select("SELECT `post_id` FROM `#__postmeta` WHERE `meta_key`='mec_verification_key' AND `meta_value`='$key'", 'loadResult');

            if(!$book_id) return false;

            $status = get_post_meta($book_id, 'mec_verified', true);
            if($status == '1')
            {
                $status_user = get_post_meta($book_id, 'mec_verified_user', true);
                if(trim($status_user) == '') $status_user = 0;

                if(!$status_user)
                {
                    // User Status
                    update_post_meta($book_id, 'mec_verified_user', 1);

                    echo '<p class="mec-success">'.__('Your booking has been verified successfully!', 'modern-events-calendar-lite').'</p>';
                    return false;
                }
                else
                {
                    echo '<p class="mec-success">'.__('Your booking already verified!', 'modern-events-calendar-lite').'</p>';
                    return false;
                }
            }

            $book = $this->getBook();
            if($book->verify($book_id)) echo '<p class="mec-success">'.__('Your booking has been verified successfully!', 'modern-events-calendar-lite').'</p>';
            else echo '<p class="mec-error">'.__('Your booking cannot verify!', 'modern-events-calendar-lite').'</p>';
        }
        elseif(get_query_var('cancel'))
        {
            $key = sanitize_text_field(get_query_var('cancel'));

            $db = $this->getDB();
            $book_id = $db->select("SELECT `post_id` FROM `#__postmeta` WHERE `meta_key`='mec_cancellation_key' AND `meta_value`='$key'", 'loadResult');

            if(!$book_id) return false;

            $status = get_post_meta($book_id, 'mec_verified', true);
            if($status == '-1')
            {
                $status_user = get_post_meta($book_id, 'mec_canceled_user', true);
                if(trim($status_user) == '') $status_user = 0;

                if(!$status_user)
                {
                    // User Status
                    update_post_meta($book_id, 'mec_canceled_user', 1);

                    echo '<p class="mec-success">'.__('Your booking successfully canceled.', 'modern-events-calendar-lite').'</p>';
                    return false;
                }
                else
                {
                    echo '<p class="mec-success">'.__('Your booking already canceled!', 'modern-events-calendar-lite').'</p>';
                    return false;
                }
            }

            $timestamps = explode(':', get_post_meta($book_id, 'mec_date', true));
            $start = $timestamps[0];
            $end = $timestamps[1];

            $right_now = current_time('timestamp', 0);
            if($right_now >= $end)
            {
                echo '<p class="mec-error">'.__('The event is already finished!', 'modern-events-calendar-lite').'</p>';
                return false;
            }

            // MEC Settings
            $settings = $this->get_settings();

            $cancellation_period_from = isset($settings['cancellation_period_from']) ? $settings['cancellation_period_from'] : 0;
            $cancellation_period_to = isset($settings['cancellation_period_time']) ? $settings['cancellation_period_time'] : 0;
            $cancellation_period_p = isset($settings['cancellation_period_p']) ? $settings['cancellation_period_p'] : 'hour';
            $cancellation_period_type = isset($settings['cancellation_period_type']) ? $settings['cancellation_period_type'] : 'before';

            if($cancellation_period_from or $cancellation_period_to)
            {
                if($cancellation_period_from)
                {
                    if($cancellation_period_type == 'before') $min_time = ($start - ($cancellation_period_from * ($cancellation_period_p == 'hour' ? 3600 : 86400)));
                    else $min_time = ($start + ($cancellation_period_from * ($cancellation_period_p == 'hour' ? 3600 : 86400)));

                    if($right_now < $min_time)
                    {
                        echo '<p class="mec-error">'.__("The cancelation window is not started yet.", 'modern-events-calendar-lite').'</p>';
                        return false;
                    }
                }

                if($cancellation_period_to)
                {
                    if($cancellation_period_type == 'before') $max_time = ($start - ($cancellation_period_to * ($cancellation_period_p == 'hour' ? 3600 : 86400)));
                    else $max_time = ($start + ($cancellation_period_to * ($cancellation_period_p == 'hour' ? 3600 : 86400)));

                    if($right_now > $max_time)
                    {
                        echo '<p class="mec-error">'.__("The cancelation window is passed.", 'modern-events-calendar-lite').'</p>';
                        return false;
                    }
                }
            }

            $book = $this->getBook();
            if($book->cancel($book_id))
            {
                echo '<p class="mec-success">'.__('Your booking successfully canceled.', 'modern-events-calendar-lite').'</p>';

                $cancel_page = (isset($settings['booking_cancel_page']) and trim($settings['booking_cancel_page'])) ? $settings['booking_cancel_page'] : NULL;
                $cancel_page_url = get_permalink($cancel_page);
                $cancel_page_time = (isset($settings['booking_cancel_page_time']) and trim($settings['booking_cancel_page_time']) != '') ? $settings['booking_cancel_page_time'] : 2500;

                if($cancel_page and $cancel_page_url) echo '<script>setTimeout(function(){window.location.replace("'.esc_js($cancel_page_url).'");}, '.$cancel_page_time.');</script>';
            }
            else echo '<p class="mec-error">'.__('Your booking cannot be canceled.', 'modern-events-calendar-lite').'</p>';
        }
        elseif(get_query_var('gateway-cancel'))
        {
            echo '<p class="mec-success">'.__('You canceled the payment successfully.', 'modern-events-calendar-lite').'</p>';
        }
        elseif(get_query_var('gateway-return'))
        {
            echo '<p class="mec-success">'.__('You returned from payment gateway successfully.', 'modern-events-calendar-lite').'</p>';
        }
        elseif(get_query_var('gateway-notify'))
        {
            // TODO
        }

        // Trigget Actions
        do_action('mec_gateway_do_endpoints', $this);
    }

    public function booking_invoice()
    {
        // Booking Invoice
        if(isset($_GET['method']) and sanitize_text_field($_GET['method']) == 'mec-invoice')
        {
            $settings = $this->get_settings();
            if(isset($settings['booking_invoice']) and !$settings['booking_invoice'])
            {
                wp_die(__('Cannot find the invoice!', 'modern-events-calendar-lite'), __('Invoice is invalid.', 'modern-events-calendar-lite'));
                exit;
            }

            $transaction_id = sanitize_text_field($_GET['id']);

            // Libraries
            $book = $this->getBook();
            $render = $this->getRender();
            $db = $this->getDB();

            $transaction = $book->get_transaction($transaction_id);
            $event_id = isset($transaction['event_id']) ? $transaction['event_id'] : 0;

            // Dont Show PDF If Booking is Pending
            $book_id = $db->select("SELECT `post_id` FROM `#__postmeta` WHERE `meta_value`='".$transaction_id."' AND `meta_key`='mec_transaction_id'", 'loadResult');
            $mec_confirmed = get_post_meta($book_id, 'mec_confirmed', true);

            if(!$mec_confirmed and (!current_user_can('administrator') and !current_user_can('editor')))
            {
                wp_die(__('Your booking still is not confirmed. You can download it after confirmation!', 'modern-events-calendar-lite'), __('Booking Not Confirmed.', 'modern-events-calendar-lite'));
                exit;
            }

            if(!$event_id)
            {
                wp_die(__('Cannot find the booking!', 'modern-events-calendar-lite'), __('Booking is invalid.', 'modern-events-calendar-lite'));
                exit;
            }

            // Invoice Key
            $invoice_key = isset($transaction['invoice_key']) ? $transaction['invoice_key'] : NULL;
            if($invoice_key and (!isset($_GET['mec-key']) or (isset($_GET['mec-key']) and $_GET['mec-key'] != $invoice_key)))
            {
                wp_die(__("You don't have access to view this invoice!", 'modern-events-calendar-lite'), __('Key is invalid.', 'modern-events-calendar-lite'));
                exit;
            }

            $event = $render->data($event_id);

            $location_id = $this->get_master_location_id($event);
            $location = isset($event->locations[$location_id]) ? (trim($event->locations[$location_id]['address']) ? $event->locations[$location_id]['address'] : $event->locations[$location_id]['name']) : '';

            $dates = isset($transaction['date']) ? explode(':', $transaction['date']) : array(time(), time());

            // Multiple Dates
            $all_dates = ((isset($transaction['all_dates']) and is_array($transaction['all_dates'])) ? $transaction['all_dates'] : array());

            // Get Booking Post
            $booking = $book->get_bookings_by_transaction_id($transaction_id);

            $booking_time = isset($booking[0]) ? get_post_meta($booking[0]->ID, 'mec_booking_time', true) : NULL;
            if(!$booking_time and is_numeric($dates[0])) $booking_time = date('Y-m-d', $dates[0]);

            $booking_time = date('Y-m-d', strtotime($booking_time));

            // Coupon Code
            $coupon_code = isset($booking[0]) ? get_post_meta($booking[0]->ID, 'mec_coupon_code', true) : '';

            // Include the tFPDF Class
            if(!class_exists('tFPDF')) require_once MEC_ABSPATH.'app'.DS.'api'.DS.'TFPDF'.DS.'tfpdf.php';

            $pdf = new tFPDF();
            $pdf->AddPage();

            // Add a Unicode font (uses UTF-8)
            $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
            $pdf->AddFont('DejaVuBold', '', 'DejaVuSansCondensed-Bold.ttf', true);

            $pdf->SetTitle(sprintf(__('%s Invoice', 'modern-events-calendar-lite'), $transaction_id));
            $pdf->SetAuthor(get_bloginfo('name'), true);

            // Event Information
            $pdf->SetFont('DejaVuBold', '', 18);
            $pdf->Write(25, html_entity_decode(get_the_title($event->ID)));
            $pdf->Ln();

            if(trim($location))
            {
                $pdf->SetFont('DejaVuBold', '', 12);
                $pdf->Write(6, __('Location', 'modern-events-calendar-lite').': ');
                $pdf->SetFont('DejaVu', '', 12);
                $pdf->Write(6, $location);
                $pdf->Ln();
            }

            $date_format = (isset($settings['booking_date_format1']) and trim($settings['booking_date_format1'])) ? $settings['booking_date_format1'] : 'Y-m-d';
            $time_format = get_option('time_format');

            if(is_numeric($dates[0]) and is_numeric($dates[1]))
            {
                $start_datetime = date($date_format.' '.$time_format, $dates[0]);
                $end_datetime = date($date_format.' '.$time_format, $dates[1]);
            }
            else
            {
                $start_datetime = $dates[0].' '.$event->data->time['start'];
                $end_datetime = $dates[1].' '.$event->data->time['end'];
            }

            $booking_options = isset($event->meta['mec_booking']) ? $event->meta['mec_booking'] : array();
            $bookings_all_occurrences = isset($booking_options['bookings_all_occurrences']) ? $booking_options['bookings_all_occurrences'] : 0;

            if(count($all_dates))
            {
                $pdf->SetFont('DejaVuBold', '', 12);
                $pdf->Write(6, __('Date & Times', 'modern-events-calendar-lite'));
                $pdf->Ln();
                $pdf->SetFont('DejaVu', '', 12);

                foreach($all_dates as $one_date)
                {
                    $other_timestamps = explode(':', $one_date);
                    if(isset($other_timestamps[0]) and isset($other_timestamps[1]))
                    {
                        $pdf->Write(6, sprintf(__('%s to %s', 'modern-events-calendar-lite'), $this->date_i18n($date_format.' '.$time_format, $other_timestamps[0]), $this->date_i18n($date_format.' '.$time_format, $other_timestamps[1])));
                        $pdf->Ln();
                    }
                }

                $pdf->Ln();
            }
            elseif(!$bookings_all_occurrences)
            {
                $pdf->SetFont('DejaVuBold', '', 12);
                $pdf->Write(6, __('Date & Time', 'modern-events-calendar-lite').': ');
                $pdf->SetFont('DejaVu', '', 12);
                $pdf->Write(6, trim($start_datetime).' - '.(($start_datetime != $end_datetime) ? $end_datetime.' ' : ''), '- ');
                $pdf->Ln();
            }

            $pdf->SetFont('DejaVuBold', '', 12);
            $pdf->Write(6, __('Transaction ID', 'modern-events-calendar-lite').': ');
            $pdf->SetFont('DejaVu', '', 12);
            $pdf->Write(6, $transaction_id);
            $pdf->Ln();

            $bfixed_fields = $this->get_bfixed_fields($event_id);

            if(is_array($bfixed_fields) and count($bfixed_fields) and isset($transaction['fields']) and is_array($transaction['fields']) and count($transaction['fields']))
            {
                $pdf->SetFont('DejaVuBold', '', 16);
                $pdf->Write(20, sprintf(__('%s Fields', 'modern-events-calendar-lite'), $this->m('booking', __('Booking', 'modern-events-calendar-lite'))));
                $pdf->Ln();

                foreach($bfixed_fields as $bfixed_field_id => $bfixed_field)
                {
                    if(!is_numeric($bfixed_field_id)) continue;

                    $bfixed_value = isset($transaction['fields'][$bfixed_field_id]) ? $transaction['fields'][$bfixed_field_id] : NULL;
                    if(!$bfixed_value) continue;

                    $bfixed_type = isset($bfixed_field['type']) ? $bfixed_field['type'] : NULL;
                    $bfixed_label = isset($bfixed_field['label']) ? $bfixed_field['label'] : '';

                    if($bfixed_type != 'agreement')
                    {
                        $pdf->SetFont('DejaVu', '', 12);
                        $pdf->Write(6, $bfixed_label.": ".(is_array($bfixed_value) ? implode(',', $bfixed_value) : $bfixed_value));
                        $pdf->Ln();
                    }
                }
            }

            // Attendees
            if(isset($transaction['tickets']) and is_array($transaction['tickets']) and count($transaction['tickets']))
            {
                $pdf->SetFont('DejaVuBold', '', 16);
                $pdf->Write(20, __('Attendees', 'modern-events-calendar-lite'));
                $pdf->Ln();

                $i = 1;
                foreach($transaction['tickets'] as $attendee)
                {
                    if(!isset($attendee['id'])) continue;

                    $pdf->SetFont('DejaVuBold', '', 12);
                    $pdf->Write(6, $attendee['name']);
                    $pdf->Ln();

                    $pdf->SetFont('DejaVu', '', 10);
                    $pdf->Write(6, $attendee['email']);
                    $pdf->Ln();

                    $pdf->Write(6, ((isset($event->tickets[$attendee['id']]) ? __($this->m('ticket', __('Ticket', 'modern-events-calendar-lite'))).': '.$event->tickets[$attendee['id']]['name'] : '').' '.(isset($event->tickets[$attendee['id']]) ? $book->get_ticket_price_label($event->tickets[$attendee['id']], $booking_time, $event_id) : '')));

                    // Ticket Variations
                    if(isset($attendee['variations']) and is_array($attendee['variations']) and count($attendee['variations']))
                    {
                        $ticket_variations = $this->ticket_variations($event_id, $attendee['id']);

                        foreach($attendee['variations'] as $variation_id=>$variation_count)
                        {
                            if(!$variation_count or ($variation_count and $variation_count < 0)) continue;

                            $variation_title = (isset($ticket_variations[$variation_id]) and isset($ticket_variations[$variation_id]['title'])) ? $ticket_variations[$variation_id]['title'] : '';
                            if(!trim($variation_title)) continue;

                            $pdf->Ln();
                            $pdf->Write(6, '+ '.$variation_title.' ('.$variation_count.')');
                        }
                    }

                    if($i != count($transaction['tickets'])) $pdf->Ln(12);
                    else $pdf->Ln();

                    $i++;
                }
            }

            // Billing Information
            if(isset($transaction['price_details']) and isset($transaction['price_details']['details']) and is_array($transaction['price_details']['details']) and count($transaction['price_details']['details']))
            {
                $pdf->SetFont('DejaVuBold', '', 16);
                $pdf->Write(20, __('Billing', 'modern-events-calendar-lite'));
                $pdf->Ln();

                $pdf->SetFont('DejaVu', '', 12);
                foreach($transaction['price_details']['details'] as $price_row)
                {
                    $pdf->Write(6, $price_row['description'].": ".$this->render_price($price_row['amount'], $event_id));
                    $pdf->Ln();
                }

                if($coupon_code)
                {
                    $pdf->Write(6, __('Coupon Code', 'modern-events-calendar-lite').": ".$coupon_code);
                    $pdf->Ln();
                }

                $pdf->SetFont('DejaVuBold', '', 12);
                $pdf->Write(10, __('Total', 'modern-events-calendar-lite').': ');
                $pdf->Write(10, $this->render_price($transaction['price'], $event_id));
                $pdf->Ln();
            }

            // Geteway
            $pdf->SetFont('DejaVuBold', '', 16);
            $pdf->Write(20, __('Payment', 'modern-events-calendar-lite'));
            $pdf->Ln();

            $pdf->SetFont('DejaVu', '', 12);
            $pdf->Write(6, __('Gateway', 'modern-events-calendar-lite').': ');
            $pdf->Write(6, get_post_meta($book_id, 'mec_gateway_label', true));
            $pdf->Ln();

            $pdf->SetFont('DejaVu', '', 12);
            $pdf->Write(6, __('Transaction ID', 'modern-events-calendar-lite').': ');
            $pdf->Write(6, ((isset($transaction['gateway_transaction_id']) and trim($transaction['gateway_transaction_id'])) ? $transaction['gateway_transaction_id'] : $transaction_id));
            $pdf->Ln();

            $date_format = get_option('date_format');
            $time_format = get_option('time_format');

            $pdf->SetFont('DejaVu', '', 12);
            $pdf->Write(6, __('Payment Time', 'modern-events-calendar-lite').': ');
            $pdf->Write(6, date($date_format.' '.$time_format, strtotime(get_post_meta($book_id, 'mec_booking_time', true))));
            $pdf->Ln();

            $image = $this->module('qrcode.invoice', array('event'=>$event));
            if(trim($image))
            {
                // QR Code
                $pdf->SetX(-50);
                $pdf->Image($image);
                $pdf->Ln();
            }

            $pdf->Output();
            exit;
        }
    }

    public function print_calendar()
    {
        // Print Calendar
        if(isset($_GET['method']) and sanitize_text_field($_GET['method']) == 'mec-print' and $this->getPro())
        {
            $year = isset($_GET['mec-year']) ? sanitize_text_field($_GET['mec-year']) : NULL;
            $month = isset($_GET['mec-month']) ? sanitize_text_field($_GET['mec-month']) : NULL;

            // Month and Year are required!
            if(!trim($year) or !trim($month)) return;

            $start = $year.'-'.$month.'-01';
            $end = date('Y-m-t', strtotime($start));

            $atts = array();
            $atts['sk-options']['agenda']['start_date_type'] = 'date';
            $atts['sk-options']['agenda']['start_date'] = $start;
            $atts['sk-options']['agenda']['maximum_date_range'] = $end;
            $atts['sk-options']['agenda']['style'] = 'clean';
            $atts['sk-options']['agenda']['limit'] = 1000;
            $atts['sf_status'] = false;
            $atts['sf_display_label'] = false;

            // Create Skin Object Class
            $SKO = new MEC_skin_agenda();

            // Initialize the skin
            $SKO->initialize($atts);

            // Fetch the events
            $SKO->fetch();

            ob_start();
            ?>
            <html>
                <head>
                    <?php wp_head(); ?>
                </head>
                <body class="<?php body_class('mec-print'); ?>">
                    <?php echo $SKO->output(); ?>
                </body>
            </html>
            <?php
            $html = ob_get_clean();

            echo $html;
            exit;
        }
    }

    public function booking_modal()
    {
        // Print Calendar
        if(isset($_GET['method']) and sanitize_text_field($_GET['method']) == 'mec-booking-modal' and $this->getPro())
        {
            global $post;

            // Current Post is not Event
            if($post->post_type != $this->get_main_post_type()) return;

            $occurrence = isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : NULL;
            $time = isset($_GET['time']) ? sanitize_text_field($_GET['time']) : NULL;

            ob_start();
            ?>
            <html>
            <head>
                <?php wp_head(); ?>
            </head>
            <body <?php body_class('mec-booking-modal'); ?>>
                <?php echo do_shortcode('[mec-booking event-id="'.$post->ID.'"]'); ?>
                <?php wp_footer(); ?>
            </body>
            </html>
            <?php
            $html = ob_get_clean();

            echo $html;
            exit;
        }
    }

    /**
     * Generates ical output
     * @author Webnus <info@webnus.biz>
     */
    public function ical()
    {
        // ical export
        if(isset($_GET['method']) and sanitize_text_field($_GET['method']) == 'ical')
        {
            $id = sanitize_text_field($_GET['id']);
            $post = get_post($id);

            if($post->post_type == $this->get_main_post_type() and $post->post_status == 'publish')
            {
                $occurrence = isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '';

                $events = $this->ical_single($id, $occurrence);
                $ical_calendar = $this->ical_calendar($events);

                header('Content-type: application/force-download; charset=utf-8');
                header('Content-Disposition: attachment; filename="mec-event-'.$id.'.ics"');

                echo $ical_calendar;
                exit;
            }
        }
    }

    /**
     * Generates ical output in email
     * @author Webnus <info@webnus.biz>
     */
    public function ical_email()
    {
        // ical export
        if(isset($_GET['method']) and sanitize_text_field($_GET['method']) == 'ical-email')
        {
            $id = sanitize_text_field($_GET['id']);
            $book_id = sanitize_text_field($_GET['book_id']);
            $key = sanitize_text_field($_GET['key']);

            if($key != md5($book_id))
            {
                wp_die(__('Request is not valid.', 'modern-events-calendar-lite'), __('iCal export stopped!', 'modern-events-calendar-lite'), array('back_link'=>true));
                exit;
            }

            $occurrence = isset($_GET['occurrence']) ? sanitize_text_field($_GET['occurrence']) : '';

            $events = $this->ical_single_email($id, $book_id, $occurrence);
            $ical_calendar = $this->ical_calendar($events);

            header('Content-type: application/force-download; charset=utf-8');
            header('Content-Disposition: attachment; filename="mec-booking-'.$book_id.'.ics"');

            echo $ical_calendar;
            exit;
        }
    }

    /**
     * Returns the iCal URL of event
     * @author Webnus <info@webnus.biz>
     * @param $event_id
     * @param string $occurrence
     * @return string
     */
    public function ical_URL($event_id, $occurrence = '')
    {
        $url = $this->URL('site');
        $url = $this->add_qs_var('method', 'ical', $url);
        $url = $this->add_qs_var('id', $event_id, $url);

        // Add Occurrence Date if passed
        if(trim($occurrence)) $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return $url;
    }

    public function ical_URL_email($event_id, $book_id, $occurrence = '')
    {
        $url = $this->URL('site');
        $url = $this->add_qs_var('method', 'ical-email', $url);
        $url = $this->add_qs_var('id', $event_id, $url);
        $url = $this->add_qs_var('book_id', $book_id, $url);
        $url = $this->add_qs_var('key', md5($book_id), $url);

        // Add Occurrence Date if passed
        if(trim($occurrence)) $url = $this->add_qs_var('occurrence', $occurrence, $url);

        return $url;
    }

    /**
     * Returns iCal export for one event
     * @author Webnus <info@webnus.biz>
     * @param int $event_id
     * @param string $occurrence
     * @param string $occurrence_time
     * @return string
     */
    public function ical_single($event_id, $occurrence = '', $occurrence_time = '')
    {
        // Valid Line Separator
        $crlf = "\r\n";

        // MEC Render Library
        $render = $this->getRender();

        // Event Data
        $event = $render->data($event_id);

        $occurrence_end_date = (trim($occurrence) ? $this->get_end_date_by_occurrence($event_id, $occurrence) : '');

        // Event Dates
        $dates = $this->get_event_next_occurrences($event, $occurrence, 2, $occurrence_time);

        $start_time = strtotime(((isset($dates[0]) and trim($dates[0]['start']['date'])) ? $dates[0]['start']['date'] : $occurrence).' '.sprintf("%02d", $dates[0]['start']['hour']).':'.sprintf("%02d", $dates[0]['start']['minutes']).' '.$dates[0]['start']['ampm']);
        $end_time = strtotime((trim($occurrence_end_date) ? $occurrence_end_date : $dates[0]['end']['date']).' '.sprintf("%02d", $dates[0]['end']['hour']).':'.sprintf("%02d", $dates[0]['end']['minutes']).' '.$dates[0]['end']['ampm']);

        $gmt_offset_seconds = $this->get_gmt_offset_seconds($start_time, $event);
        $stamp = strtotime($event->post->post_date);
        $modified = strtotime($event->post->post_modified);

        $rrules = $this->get_ical_rrules($event);
        $time_format = 'Ymd\\THi00\\Z';

        // All Day Event
        if(isset($event->meta['mec_date']) and isset($event->meta['mec_date']['allday']) and $event->meta['mec_date']['allday'])
        {
            $time_format = 'Ymd\\T000000\\Z';
            $end_time = strtotime('+1 Day', $end_time);
        }

        $ical  = "BEGIN:VEVENT".$crlf;
        $ical .= "CLASS:PUBLIC".$crlf;
        $ical .= "UID:MEC-".md5($event_id)."@".$this->get_domain().$crlf;
        $ical .= "DTSTART:".gmdate($time_format, ($start_time - $gmt_offset_seconds)).$crlf;
        $ical .= "DTEND:".gmdate($time_format, ($end_time - $gmt_offset_seconds)).$crlf;
        $ical .= "DTSTAMP:".gmdate($time_format, ($stamp - $gmt_offset_seconds)).$crlf;

        if(is_array($rrules) and count($rrules))
        {
            foreach($rrules as $rrule) $ical .= $rrule.$crlf;
        }

        $event_content = strip_tags($event->content);
        $event_content = str_replace("\r\n", "\\n", $event_content);
        $event_content = str_replace("\n", "\\n", $event_content);
        $event_content = preg_replace('/(<script[^>]*>.+?<\/script>|<style[^>]*>.+?<\/style>)/s', '', $event_content);

        $ical .= "CREATED:".date('Ymd', $stamp).$crlf;
        $ical .= "LAST-MODIFIED:".date('Ymd', $modified).$crlf;
        $ical .= "PRIORITY:5".$crlf;
        $ical .= "TRANSP:OPAQUE".$crlf;
        $ical .= "SUMMARY:".html_entity_decode(apply_filters('mec_ical_single_summary', $event->title, $event_id), ENT_NOQUOTES, 'UTF-8').$crlf;
        $ical .= "DESCRIPTION:".html_entity_decode(apply_filters('mec_ical_single_description', $event_content, $event_id), ENT_NOQUOTES, 'UTF-8').$crlf;
        $ical .= "URL:".apply_filters('mec_ical_single_url', $event->permalink, $event_id).$crlf;

        // Organizer
        $organizer_id = $this->get_master_organizer_id($event->ID, $start_time);
        $organizer = isset($event->organizers[$organizer_id]) ? $event->organizers[$organizer_id] : array();
        $organizer_name = (isset($organizer['name']) and trim($organizer['name'])) ? $organizer['name'] : NULL;
        $organizer_email = (isset($organizer['email']) and trim($organizer['email'])) ? $organizer['email'] : NULL;

        if($organizer_name or $organizer_email) $ical .= "ORGANIZER;CN=".$organizer_name.":MAILTO:".$organizer_email.$crlf;

        // Categories
        $categories = '';
        if(isset($event->categories) and is_array($event->categories) and count($event->categories))
        {
            foreach($event->categories as $category) $categories .= $category['name'].',';
        }

        if(trim($categories) != '') $ical .= "CATEGORIES:".trim($categories, ', ').$crlf;

        // Location
        $location_id = $this->get_master_location_id($event->ID, $start_time);
        $location = isset($event->locations[$location_id]) ? $event->locations[$location_id] : array();
        $address = ((isset($location['address']) and trim($location['address'])) ? $location['address'] : (isset($location['name']) ? $location['name'] : ''));

        if(trim($address) != '') $ical .= "LOCATION:".$address.$crlf;

        // Featured Image
        if(trim($event->featured_image['full']) != '')
        {
            $ex = explode('/', $event->featured_image['full']);
            $filename = end($ex);
            $ical .= "ATTACH;FMTTYPE=".$this->get_mime_content_type($filename).":".$event->featured_image['full'].$crlf;
        }

        $ical .= "END:VEVENT".$crlf;

        return $ical;
    }

    /**
     * Returns iCal export for email
     * @author Webnus <info@webnus.biz>
     * @param int $event_id
     * @param int $book_id
     * @param string $occurrence
     * @return string
     */
    public function ical_single_email($event_id, $book_id, $occurrence = '')
    {
        // Valid Line Separator
        $crlf = "\r\n";

        $date = get_post_meta($book_id, 'mec_date', true);
        $timestamps = explode(':', $date);

        // MEC Render Library
        $render = $this->getRender();
        $event = $render->data($event_id);

        $start_time = (isset($timestamps[0]) ? $timestamps[0] : strtotime(get_the_date($book_id)));
        $end_time = (isset($timestamps[1]) ? $timestamps[1] : strtotime(get_the_date($book_id)));

        $location_id = $this->get_master_location_id($event->ID, $start_time);
        $location = isset($event->locations[$location_id]) ? $event->locations[$location_id] : array();
        $address = (isset($location['address']) and trim($location['address'])) ? $location['address'] : $location['name'];

        $gmt_offset_seconds = $this->get_gmt_offset_seconds($start_time, $event);

        $stamp = strtotime($event->post->post_date);
        $modified = strtotime($event->post->post_modified);
        $time_format = (isset($event->meta['mec_date']) and isset($event->meta['mec_date']['allday']) and $event->meta['mec_date']['allday']) ? 'Ymd' : 'Ymd\\THi00\\Z';

        $event_content = strip_tags($event->content);
        $event_content = str_replace("\r\n", "\\n", $event_content);
        $event_content = str_replace("\n", "\\n", $event_content);
        $event_content = preg_replace('/(<script[^>]*>.+?<\/script>|<style[^>]*>.+?<\/style>)/s', '', $event_content);

        $ical  = "BEGIN:VEVENT".$crlf;
        $ical .= "CLASS:PUBLIC".$crlf;
        $ical .= "UID:MEC-".md5($event_id)."@".$this->get_domain().$crlf;
        $ical .= "DTSTART:".gmdate($time_format, ($start_time - $gmt_offset_seconds)).$crlf;
        $ical .= "DTEND:".gmdate($time_format, ($end_time - $gmt_offset_seconds)).$crlf;
        $ical .= "DTSTAMP:".gmdate($time_format, ($stamp - $gmt_offset_seconds)).$crlf;
        $ical .= "CREATED:".date('Ymd', $stamp).$crlf;
        $ical .= "LAST-MODIFIED:".date('Ymd', $modified).$crlf;
        $ical .= "PRIORITY:5".$crlf;
        $ical .= "TRANSP:OPAQUE".$crlf;
        $ical .= "SUMMARY:".html_entity_decode(apply_filters('mec_ical_single_summary', $event->title, $event_id), ENT_NOQUOTES, 'UTF-8').$crlf;
        $ical .= "DESCRIPTION:".html_entity_decode(apply_filters('mec_ical_single_description', $event_content, $event_id), ENT_NOQUOTES, 'UTF-8').$crlf;
        $ical .= "URL:".apply_filters('mec_ical_single_url', $event->permalink, $event_id).$crlf;

        // Location
        if(trim($address) != '') $ical .= "LOCATION:".$address.$crlf;

        // Featured Image
        if(trim($event->featured_image['full']) != '')
        {
            $ex = explode('/', $event->featured_image['full']);
            $filename = end($ex);
            $ical .= "ATTACH;FMTTYPE=".$this->get_mime_content_type($filename).":".$event->featured_image['full'].$crlf;
        }

        $ical .= "END:VEVENT".$crlf;

        return $ical;
    }

    /**
     * Returns iCal export for some events
     * @author Webnus <info@webnus.biz>
     * @param string $events
     * @return string
     */
    public function ical_calendar($events)
    {
        // Valid Line Separator
        $crlf = "\r\n";

        $ical  = "BEGIN:VCALENDAR".$crlf;
        $ical .= "VERSION:2.0".$crlf;
        $ical .= "METHOD:PUBLISH".$crlf;
        $ical .= "CALSCALE:GREGORIAN".$crlf;
        $ical .= "PRODID:-//WordPress - MECv".$this->get_version()."//EN".$crlf;
        $ical .= "X-ORIGINAL-URL:".$this->URL('site').$crlf;
        $ical .= "X-WR-CALNAME:".get_bloginfo('name').$crlf;
        $ical .= "X-WR-CALDESC:".get_bloginfo('description').$crlf;
        $ical .= "REFRESH-INTERVAL;VALUE=DURATION:PT1H".$crlf;
        $ical .= "X-PUBLISHED-TTL:PT1H".$crlf;
        $ical .= "X-MS-OLK-FORCEINSPECTOROPEN:TRUE".$crlf;
        $ical .= $events;
        $ical .= "END:VCALENDAR".$crlf;

        return $ical;
    }

    /**
     * Get mime type of a file
     * @author Webnus <info@webnus.biz>
     * @param string $filename
     * @return string
     */
    public function get_mime_content_type($filename)
    {
        // Remove query string from the image name
        if(strpos($filename, '?') !== false)
        {
            $ex = explode('?', $filename);
            $filename = $ex[0];
        }

        $mime_types = array
        (
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ex = explode('.', $filename);
        $ext = strtolower(array_pop($ex));
        if(array_key_exists($ext, $mime_types))
        {
            return $mime_types[$ext];
        }
        elseif(function_exists('finfo_open'))
        {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);

            return $mimetype;
        }
        else
        {
            return 'application/octet-stream';
        }
    }

    /**
     * Returns book post type slug
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_book_post_type()
    {
        return apply_filters('mec_book_post_type_name', 'mec-books');
    }

    /**
     * Returns shortcode post type slug
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_shortcode_post_type()
    {
        return apply_filters('mec_shortcode_post_type_name', 'mec_calendars');
    }

    /**
     * Returns email post type
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_email_post_type()
    {
        return apply_filters('mec_email_post_type_name', 'mec-emails');
    }

    /**
     * Show text field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_text($key, $values = array(), $prefix = 'reg')
    {
        return '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Text', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="text" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
                '.($prefix == 'reg' ? $this->get_wp_user_fields_dropdown('mec['.$prefix.'_fields]['.$key.'][mapping]', (isset($values['mapping']) ? $values['mapping'] : '')) : '').'
            </div>
        </li>';
    }

    /**
     * Show text field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
     public function field_name($key, $values = array(), $prefix = 'reg')
     {
         return '<li id="mec_'.$prefix.'_fields_'.$key.'">
             <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
             <span class="mec_'.$prefix.'_field_type">'.__('MEC Name', 'modern-events-calendar-lite').'</span>
             '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
             '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
             <p class="mec_'.$prefix.'_field_options" style="display:none">
                 <label>
                     <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" />
                     <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" checked="checked" disabled />
                     '.__('Required Field', 'modern-events-calendar-lite').'
                 </label>
             </p>
             <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
             <div>
                 <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="name" />
                 <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
             </div>
         </li>';
     }

     /**
     * Show text field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
     public function field_mec_email($key, $values = array(), $prefix = 'reg')
     {
         return '<li id="mec_'.$prefix.'_fields_'.$key.'">
             <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
             <span class="mec_'.$prefix.'_field_type">'.__('MEC Email', 'modern-events-calendar-lite').'</span>
             '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
             '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
             <p class="mec_'.$prefix.'_field_options" style="display:none">
                 <label>
                     <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" />
                     <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" checked="checked" disabled />
                     '.__('Required Field', 'modern-events-calendar-lite').'
                 </label>
             </p>
             <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
             <div>
                 <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="mec_email" />
                 <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
             </div>
         </li>';
     }

    /**
     * Show email field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_email($key, $values = array(), $prefix = 'reg')
    {
        return '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Email', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="email" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
                '.($prefix == 'reg' ? $this->get_wp_user_fields_dropdown('mec['.$prefix.'_fields]['.$key.'][mapping]', (isset($values['mapping']) ? $values['mapping'] : '')) : '').'
            </div>
        </li>';
    }

    /**
     * Show URL field options in forms
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_url($key, $values = array(), $prefix = 'reg')
    {
        return '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('URL', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="url" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
                '.($prefix == 'reg' ? $this->get_wp_user_fields_dropdown('mec['.$prefix.'_fields]['.$key.'][mapping]', (isset($values['mapping']) ? $values['mapping'] : '')) : '').'
            </div>
        </li>';
    }

    /**
    * Show file field options in booking form
    * @author Webnus <info@webnus.biz>
    * @param string $key
    * @param array $values
    * @param string $prefix
    * @return string
    */
    public function field_file($key, $values = array(), $prefix = 'reg')
    {
        return '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('File', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="file" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
            </div>
        </li>';
    }

    /**
    * Show date field options in booking form
    * @author Webnus <info@webnus.biz>
    * @param string $key
    * @param array $values
    * @param string $prefix
    * @return string
    */
    public function field_date($key, $values = array(), $prefix = 'reg')
    {
        return '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Date', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="date" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
                '.($prefix == 'reg' ? $this->get_wp_user_fields_dropdown('mec['.$prefix.'_fields]['.$key.'][mapping]', (isset($values['mapping']) ? $values['mapping'] : '')) : '').'
            </div>
        </li>';
    }

    /**
     * Show tel field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_tel($key, $values = array(), $prefix = 'reg')
    {
        return '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Tel', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="tel" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
                '.($prefix == 'reg' ? $this->get_wp_user_fields_dropdown('mec['.$prefix.'_fields]['.$key.'][mapping]', (isset($values['mapping']) ? $values['mapping'] : '')) : '').'
            </div>
        </li>';
    }

    /**
     * Show textarea field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_textarea($key, $values = array(), $prefix = 'reg')
    {
        return '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Textarea', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <div id="mec_'.$prefix.'_field_options_'.$key.'_mandatory_wrapper" class="'.((isset($values['editor']) and $values['editor']) ? 'mec-util-hidden' : '').'">
                    <label>
                        <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                        <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                        '.__('Required Field', 'modern-events-calendar-lite').'
                    </label>
                </div>
                '.($prefix == 'event' ? '<br><label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][editor]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][editor]" value="1" onchange="jQuery(\'#mec_'.$prefix.'_field_options_'.$key.'_mandatory_wrapper\').toggleClass(\'mec-util-hidden\');" '.((isset($values['editor']) and $values['editor']) ? 'checked="checked"' : '').' />
                    '.__('HTML Editor', 'modern-events-calendar-lite').'
                </label>' : '').'
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="textarea" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
                '.($prefix == 'reg' ? $this->get_wp_user_fields_dropdown('mec['.$prefix.'_fields]['.$key.'][mapping]', (isset($values['mapping']) ? $values['mapping'] : '')) : '').'
            </div>
        </li>';
    }

    /**
     * Show paragraph field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_p($key, $values = array(), $prefix = 'reg')
    {
        return '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Paragraph', 'modern-events-calendar-lite').'</span>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="p" />
                <textarea name="mec['.$prefix.'_fields]['.$key.'][content]">'.(isset($values['content']) ? htmlentities(stripslashes($values['content'])) : '').'</textarea>
                <p class="description">'.__('HTML and shortcode are allowed.').'</p>
            </div>
        </li>';
    }

    /**
     * Show checkbox field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_checkbox($key, $values = array(), $prefix = 'reg')
    {
        $i = 0;
        $field = '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Checkboxes', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="checkbox" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
                '.($prefix == 'reg' ? $this->get_wp_user_fields_dropdown('mec['.$prefix.'_fields]['.$key.'][mapping]', (isset($values['mapping']) ? $values['mapping'] : '')) : '').'
                <ul id="mec_'.$prefix.'_fields_'.$key.'_options_container" class="mec_'.$prefix.'_fields_options_container">';

        if(isset($values['options']) and is_array($values['options']) and count($values['options']))
        {
            foreach($values['options'] as $option_key=>$option)
            {
                $i = max($i, $option_key);
                $field .= $this->field_option($key, $option_key, $values, $prefix);
            }
        }

        $field .= '</ul>
                <button type="button" class="mec-'.$prefix.'-field-add-option" data-field-id="'.$key.'">'.__('Option', 'modern-events-calendar-lite').'</button>
                <input type="hidden" id="mec_new_'.$prefix.'_field_option_key_'.$key.'" value="'.($i+1).'" />
            </div>
        </li>';

        return $field;
    }

    /**
     * Show radio field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_radio($key, $values = array(), $prefix = 'reg')
    {
        $i = 0;
        $field = '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Radio Buttons', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="radio" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
                '.($prefix == 'reg' ? $this->get_wp_user_fields_dropdown('mec['.$prefix.'_fields]['.$key.'][mapping]', (isset($values['mapping']) ? $values['mapping'] : '')) : '').'
                <ul id="mec_'.$prefix.'_fields_'.$key.'_options_container" class="mec_'.$prefix.'_fields_options_container">';

        if(isset($values['options']) and is_array($values['options']) and count($values['options']))
        {
            foreach($values['options'] as $option_key=>$option)
            {
                $i = max($i, $option_key);
                $field .= $this->field_option($key, $option_key, $values, $prefix);
            }
        }

        $field .= '</ul>
                <button type="button" class="mec-'.$prefix.'-field-add-option" data-field-id="'.$key.'">'.__('Option', 'modern-events-calendar-lite').'</button>
                <input type="hidden" id="mec_new_'.$prefix.'_field_option_key_'.$key.'" value="'.($i+1).'" />
            </div>
        </li>';

        return $field;
    }

    /**
     * Show select field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_select($key, $values = array(), $prefix = 'reg')
    {
        $i = 0;
        $field = '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Dropdown', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((isset($values['mandatory']) and $values['mandatory']) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][ignore]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][ignore]" value="1" '.((isset($values['ignore']) and $values['ignore']) ? 'checked="checked"' : '').' />
                    '.__('Consider the first item as a placeholder', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="select" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : '').'" />
                '.($prefix == 'reg' ? $this->get_wp_user_fields_dropdown('mec['.$prefix.'_fields]['.$key.'][mapping]', (isset($values['mapping']) ? $values['mapping'] : '')) : '').'
                <ul id="mec_'.$prefix.'_fields_'.$key.'_options_container" class="mec_'.$prefix.'_fields_options_container">';

        if(isset($values['options']) and is_array($values['options']) and count($values['options']))
        {
            foreach($values['options'] as $option_key=>$option)
            {
                $i = max($i, $option_key);
                $field .= $this->field_option($key, $option_key, $values, $prefix);
            }
        }

        $field .= '</ul>
                <button type="button" class="mec-'.$prefix.'-field-add-option" data-field-id="'.$key.'">'.__('Option', 'modern-events-calendar-lite').'</button>
                <input type="hidden" id="mec_new_'.$prefix.'_field_option_key_'.$key.'" value="'.($i+1).'" />
            </div>
        </li>';

        return $field;
    }

    /**
     * Show agreement field options in booking form
     * @author Webnus <info@webnus.biz>
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_agreement($key, $values = array(), $prefix = 'reg')
    {
        // WordPress Pages
        $pages = get_pages();

        $i = 0;
        $field = '<li id="mec_'.$prefix.'_fields_'.$key.'">
            <span class="mec_'.$prefix.'_field_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span class="mec_'.$prefix.'_field_type">'.__('Agreement', 'modern-events-calendar-lite').'</span>
            '.($prefix == 'event' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%event_field_'.$key.'%%</span>' : ($prefix == 'bfixed' ? '<span class="mec_'.$prefix.'_notification_placeholder">%%booking_field_'.$key.'%%</span>' : '')).'
            '. apply_filters( 'mec_form_field_description', '', $key, $values, $prefix ) .'
            <p class="mec_'.$prefix.'_field_options">
                <label>
                    <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="0" />
                    <input type="checkbox" name="mec['.$prefix.'_fields]['.$key.'][mandatory]" value="1" '.((!isset($values['mandatory']) or (isset($values['mandatory']) and $values['mandatory'])) ? 'checked="checked"' : '').' />
                    '.__('Required Field', 'modern-events-calendar-lite').'
                </label>
            </p>
            <span onclick="mec_'.$prefix.'_fields_remove('.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <div>
                <input type="hidden" name="mec['.$prefix.'_fields]['.$key.'][type]" value="agreement" />
                <input type="text" name="mec['.$prefix.'_fields]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this field', 'modern-events-calendar-lite').'" value="'.(isset($values['label']) ? stripslashes($values['label']) : 'I agree with %s').'" /><p class="description">'.__('Instead of %s, the page title with a link will be show.', 'modern-events-calendar-lite').'</p>
                <div>
                    <label for="mec_'.$prefix.'_fields_'.$key.'_page">'.__('Agreement Page', 'modern-events-calendar-lite').'</label>
                    <select id="mec_'.$prefix.'_fields_'.$key.'_page" name="mec['.$prefix.'_fields]['.$key.'][page]">';

                    $page_options = '';
                    foreach($pages as $page) $page_options .= '<option '.((isset($values['page']) and $values['page'] == $page->ID) ? 'selected="selected"' : '').' value="'.$page->ID.'">'.$page->post_title.'</option>';

                    $field .= $page_options.'</select>
                </div>
                <div>
                    <label for="mec_'.$prefix.'_fields_'.$key.'_status">'.__('Status', 'modern-events-calendar-lite').'</label>
                    <select id="mec_'.$prefix.'_fields_'.$key.'_status" name="mec['.$prefix.'_fields]['.$key.'][status]">
                        <option value="checked" '.((isset($values['status']) and $values['status'] == 'checked') ? 'selected="selected"' : '').'>'.__('Checked by default', 'modern-events-calendar-lite').'</option>
                        <option value="unchecked" '.((isset($values['status']) and $values['status'] == 'unchecked') ? 'selected="selected"' : '').'>'.__('Unchecked by default', 'modern-events-calendar-lite').'</option>
                    </select>
                </div>
                <input type="hidden" id="mec_new_'.$prefix.'_field_option_key_'.$key.'" value="'.($i+1).'" />
            </div>
        </li>';

        return $field;
    }

    /**
     * Show option tag parameters in booking form for select, checkbox and radio tags
     * @author Webnus <info@webnus.biz>
     * @param string $field_key
     * @param string $key
     * @param array $values
     * @param string $prefix
     * @return string
     */
    public function field_option($field_key, $key, $values = array(), $prefix = 'reg')
    {
        return '<li id="mec_'.$prefix.'_fields_option_'.$field_key.'_'.$key.'">
            <span class="mec_'.$prefix.'_field_option_sort">'.__('Sort', 'modern-events-calendar-lite').'</span>
            <span onclick="mec_'.$prefix.'_fields_option_remove('.$field_key.','.$key.');" class="mec_'.$prefix.'_field_remove">'.__('Remove', 'modern-events-calendar-lite').'</span>
            <input type="text" name="mec['.$prefix.'_fields]['.$field_key.'][options]['.$key.'][label]" placeholder="'.esc_attr__('Insert a label for this option', 'modern-events-calendar-lite').'" value="'.((isset($values['options']) and isset($values['options'][$key])) ? esc_attr(stripslashes($values['options'][$key]['label'])) : '').'" />
        </li>';
    }

    /**
     * Render raw price and return its output
     * @param int|object $event
     * @author Webnus <info@webnus.biz>
     * @param int $price
     * @return string
     */
    public function render_price($price, $event = NULL)
    {
        // return Free if price is 0
        if($price == '0') return __('Free', 'modern-events-calendar-lite');

        $custom_settings_for_event = (bool)Settings::getInstance()->get_settings('currency_per_event');
        if($custom_settings_for_event){

            if( is_numeric( $event ) ){
                $event_id = $event;
            }elseif( is_object( $event ) ){
                $event_id = $event->ID;
            }else{
                $event_id = get_the_ID();
            }
            $currency_settings = get_post_meta( $event_id,'mec_currency',true );

            $thousand_separator = isset($currency_settings['thousand_separator']) ? $currency_settings['thousand_separator'] : '';
            $decimal_separator = isset($currency_settings['decimal_separator']) ? $currency_settings['decimal_separator'] : '';
            if( isset($currency_settings['currency_symptom']) && !empty( $currency_settings['currency_symptom'] ) ){

                $currency = $currency_settings['currency_symptom'];
            }else{

                $currency = isset($currency_settings['currency']) ? $currency_settings['currency'] : '';
            }
            $currency_sign_position = isset($currency_settings['currency_sign_position']) ? $currency_settings['currency_sign_position'] : '';
        }else{

            $thousand_separator = $this->get_thousand_separator($event);
            $decimal_separator = $this->get_decimal_separator($event);

            $currency = $this->get_currency_sign($event);
            $currency_sign_position = $this->get_currency_sign_position($event);
        }

        // Force to double
        if(is_string($price)) $price = (double) $price;

        $rendered = number_format($price, ($decimal_separator === false ? 0 : 2), ($decimal_separator === false ? '' : $decimal_separator), $thousand_separator);

        if($currency_sign_position == 'after') $rendered = $rendered.$currency;
        elseif($currency_sign_position == 'after_space') $rendered = $rendered.' '.$currency;
        elseif($currency_sign_position == 'before_space') $rendered = $currency.' '.$rendered;
        else $rendered = $currency.$rendered;

        return $rendered;
    }

    /**
     * Returns thousand separator
     * @param int|object $event
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_thousand_separator($event = NULL)
    {
        $settings = $this->get_settings();

        // Separator
        $separator = (isset($settings['thousand_separator']) ? $settings['thousand_separator'] : ',');

        // Currency Per Event
        if($event and isset($settings['currency_per_event']) and $settings['currency_per_event'])
        {
            $options = $this->get_event_currency_options($event);
            if(isset($options['thousand_separator']) and trim($options['thousand_separator'])) $separator = $options['thousand_separator'];
        }

        return apply_filters('mec_thousand_separator', $separator);
    }

    /**
     * Returns decimal separator
     * @param int|object $event
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_decimal_separator($event = NULL)
    {
        $settings = $this->get_settings();

        // Separator
        $separator = (isset($settings['decimal_separator']) ? $settings['decimal_separator'] : '.');

        // Status
        $disabled = (isset($settings['decimal_separator_status']) and $settings['decimal_separator_status'] == 0);

        // Currency Per Event
        if($event and isset($settings['currency_per_event']) and $settings['currency_per_event'])
        {
            $options = $this->get_event_currency_options($event);
            if(isset($options['decimal_separator']) and trim($options['decimal_separator'])) $separator = $options['decimal_separator'];
            if(isset($options['decimal_separator_status']) and $options['decimal_separator_status'] == 0) $disabled = true;
        }

        return apply_filters('mec_decimal_separator', ($disabled ? false : $separator));
    }

    /**
     * @param int|object $event
     * @return array
     */
    public function get_event_currency_options($event)
    {
        $event_id = (is_object($event) ? $event->ID : $event);

        $options = get_post_meta($event_id, 'mec_currency', true);
        if(!is_array($options)) $options = array();

        return $options;
    }

    /**
     * Returns currency of MEC
     * @param int|object $event
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_currency($event = NULL)
    {
        $settings = $this->get_settings();
        $currency = (isset($settings['currency']) ? $settings['currency'] : '');

        // Currency Per Event
        if($event and isset($settings['currency_per_event']) and $settings['currency_per_event'])
        {
            $options = $this->get_event_currency_options($event);
            if(isset($options['currency']) and trim($options['currency'])) $currency = $options['currency'];
        }

        return apply_filters('mec_currency', $currency);
    }

    /**
     * Returns currency sign of MEC
     * @param int|object $event
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_currency_sign($event = NULL)
    {
        $settings = $this->get_settings();

        // Get Currency Symptom
        $currency = $this->get_currency($event);
        if(isset($settings['currency_symptom']) and trim($settings['currency_symptom'])) $currency = $settings['currency_symptom'];

        // Currency Per Event
        if($event and isset($settings['currency_per_event']) and $settings['currency_per_event'])
        {
            $options = $this->get_event_currency_options($event);
            if(isset($options['currency_symptom']) and trim($options['currency_symptom'])) $currency = $options['currency_symptom'];
        }

        return apply_filters('mec_currency_sign', $currency);
    }

    /**
     * Returns currency code of MEC
     * @param int|object $event
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_currency_code($event = NULL)
    {
        $currency = $this->get_currency($event);
        $currencies = $this->get_currencies();

        return isset($currencies[$currency]) ? $currencies[$currency] : 'USD';
    }

    /**
     * Returns currency sign position of MEC
     * @param int|object $event
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_currency_sign_position($event = NULL)
    {
        $settings = $this->get_settings();

        // Currency Position
        $position = (isset($settings['currency_sign']) ? $settings['currency_sign'] : '');

        // Currency Per Event
        if($event and isset($settings['currency_per_event']) and $settings['currency_per_event'])
        {
            $options = $this->get_event_currency_options($event);
            if(isset($options['currency_sign']) and trim($options['currency_sign'])) $position = $options['currency_sign'];
        }

        return apply_filters('mec_currency_sign_position', $position);
    }

    /**
     * Returns MEC Payment Gateways
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_gateways()
    {
        return apply_filters('mec_gateways', array());
    }

    /**
     * Check to see if user exists by its username
     * @author Webnus <info@webnus.biz>
     * @param string $username
     * @return boolean
     */
    public function username_exists($username)
    {
        /** first validation **/
        if(!trim($username)) return true;

        return username_exists($username);
    }

    /**
     * Check to see if user exists by its email
     * @author Webnus <info@webnus.biz>
     * @param string $email
     * @return boolean
     */
    public function email_exists($email)
    {
        /** first validation **/
        if(!trim($email)) return true;

        return email_exists($email);
    }

    /**
     * Register a user in WordPress
     * @author Webnus <info@webnus.biz>
     * @param string $username
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function register_user($username, $email, $password = NULL)
    {
        /** first validation **/
        if(!trim($username) or !trim($email)) return false;

        return wp_create_user($username, $password, $email);
    }

    /**
     * Convert a formatted date into standard format
     * @author Webnus <info@webnus.biz>
     * @param string $date
     * @return string
     */
    public function to_standard_date($date)
    {
        $date = str_replace('-', '/', $date);
        $date = str_replace('.', '/', $date);

        return date('Y-m-d', strtotime($date));
    }

    /**
     * Render the date
     * @author Webnus <info@webnus.biz>
     * @param string $date
     * @return string
     */
    public function render_date($date)
    {
        return $date;
    }

    /**
     * Generate output of MEC Dashboard
     * @author Webnus <info@webnus.biz>
     */
    public function dashboard()
    {
        // Import dashboard page of MEC
        $path = $this->import('app.features.mec.dashboard', true, true);

        // Create mec_events table if it's removed for any reason
        $this->create_mec_tables();

        ob_start();
        include $path;
        echo $output = ob_get_clean();
    }

    /**
     * Redirect on plugin activation
     * @author Webnus <info@webnus.biz>
     */
    public function mec_redirect_after_activate()
    {
        $do_redirection = apply_filters('mec_do_redirection_after_activation', true);
        if(!$do_redirection) return false;

        // No need to redirect
        if(!get_option('mec_activation_redirect', false)) return true;

        // Delete the option to don't do it always
        delete_option('mec_activation_redirect');

        // Redirect to MEC Dashboard
        wp_redirect(admin_url('/admin.php?page=MEC-wizard'));
        exit;
    }

    /**
     * Check if we can show booking module or not
     * @author Webnus <info@webnus.biz>
     * @param object $event
     * @return boolean
     */
    public function can_show_booking_module($event)
    {
        // PRO Version is required
        if(!$this->getPRO()) return false;

        // MEC Settings
        $settings = $this->get_settings();

        // Booking on single page is disabled
        if(!isset($settings['booking_status']) or (isset($settings['booking_status']) and !$settings['booking_status'])) return false;

        // Who Can Book
        $wcb_all = (isset($settings['booking_wcb_all']) and is_numeric($settings['booking_wcb_all'])) ? $settings['booking_wcb_all'] : 1;
        if(!$wcb_all)
        {
            $user_id = get_current_user_id();

            // Guest User is not Permitted
            if(!$user_id) return false;

            // User
            $user = get_user_by('id', $user_id);

            // Invalid User ID
            if(!$user or ($user and !isset($user->roles))) return false;

            $roles = (array) $user->roles;

            $can = false;
            foreach($roles as $role) if(isset($settings['booking_wcb_'.$role]) and $settings['booking_wcb_'.$role]) $can = true;

            if(!$can) return false;
        }

        $tickets = isset($event->data->tickets) ? $event->data->tickets : array();
        $dates = isset($event->dates) ? $event->dates : (isset($event->date) ? $event->date : array());
        $next_date = isset($dates[0]) ? $dates[0] : (isset($event->date) ? $event->date : array());

        // No Dates or no Tickets
        if(!count($dates) or !count($tickets)) return false;

        // Booking Options
        $booking_options = (isset($event->data->meta['mec_booking']) and is_array($event->data->meta['mec_booking'])) ? $event->data->meta['mec_booking'] : array();

        $book_all_occurrences = 0;
        if(isset($event->data) and isset($event->data->meta) and isset($booking_options['bookings_all_occurrences'])) $book_all_occurrences = (int) $booking_options['bookings_all_occurrences'];

        $bookings_stop_selling_after_first_occurrence = isset($booking_options['stop_selling_after_first_occurrence']) ? $booking_options['stop_selling_after_first_occurrence'] : 0;
        if($bookings_stop_selling_after_first_occurrence and $this->is_first_occurrence_passed($event)) return false;

        $show_booking_form_interval = (isset($settings['show_booking_form_interval'])) ? $settings['show_booking_form_interval'] : 0;
        if(isset($booking_options['show_booking_form_interval']) and trim($booking_options['show_booking_form_interval']) != '') $show_booking_form_interval = $booking_options['show_booking_form_interval'];

        // Check Show Booking Form Time
        if($show_booking_form_interval)
        {
            if($book_all_occurrences)
            {
                $db = $this->getDB();
                $first_timestamp = $db->select("SELECT `tstart` FROM `#__mec_dates` WHERE `post_id`=".$event->data->ID." ORDER BY `tstart` ASC LIMIT 1", 'loadResult');
                $render_date = date('Y-m-d h:i a', $first_timestamp);
            }
            else
            {
                $render_date = (isset($next_date['start']['date']) ? trim($next_date['start']['date']) : date('Y-m-d')) .' '. (isset($next_date['start']['hour']) ? trim(sprintf('%02d', $next_date['start']['hour'])) : date('h', current_time('timestamp', 0))) .':'
                . (isset($next_date['start']['minutes']) ? trim(sprintf('%02d', $next_date['start']['minutes'])) : date('i', current_time('timestamp', 0))) . ' '.(isset($next_date['start']['ampm']) ? trim($next_date['start']['ampm']) : date('a', current_time('timestamp', 0)));
            }

            if($this->check_date_time_validation('Y-m-d h:i a', strtolower($render_date)))
            {
                $date_diff = $this->date_diff(date('Y-m-d h:i a', current_time('timestamp', 0)), $render_date);
                if(isset($date_diff->days) and !$date_diff->invert)
                {
                    $minute = $date_diff->days * 24 * 60;
                    $minute += $date_diff->h * 60;
                    $minute += $date_diff->i;

                    if($minute > $show_booking_form_interval) return false;
                }
            }
        }

        // Booking OnGoing Event Option
        $ongoing_event_book = (isset($settings['booking_ongoing']) and $settings['booking_ongoing'] == '1') ? true : false;

        // The event is Expired/Passed
        if($ongoing_event_book)
        {
            if(!isset($next_date['end']) or (isset($next_date['end']) and $this->is_past($next_date['end']['date'], current_time('Y-m-d')))) return false;
            if(isset($next_date['end']) and isset($next_date['end']['timestamp']) and $next_date['end']['timestamp'] < current_time('timestamp', 0)) return false;
        }
        else
        {
            $time_format = 'Y-m-d';
            $render_date = isset($next_date['start']) ? trim($next_date['start']['date']) : false;

            if(!trim($event->data->meta['mec_repeat_status']))
            {
                if(isset($next_date['start']['hour'])) $render_date .= ' ' . sprintf('%02d', $next_date['start']['hour']) . ':' . sprintf('%02d', $next_date['start']['minutes']) . trim($next_date['start']['ampm']);
                else $render_date .= ' '.date('h:ia', $event->data->time['start_timestamp']);

                $time_format .= ' h:ia';
            }

            if(!$render_date or ($render_date and $this->is_past($render_date, current_time($time_format)))) return false;
        }

        // MEC payment gateways
        $gateways = $this->get_gateways();
        $is_gateway_enabled = false;

        foreach($gateways as $gateway)
        {
            if($gateway->enabled())
            {
                $is_gateway_enabled = true;
                break;
            }
        }

        $wc_status = ((isset($settings['wc_status']) and class_exists('WooCommerce')) ? (boolean) $settings['wc_status'] : false);

        // No Payment gateway is enabled
        if(!$is_gateway_enabled and !$wc_status) return false;

        return true;
    }

    /**
     * Check if we can show countdown module or not
     * @author Webnus <info@webnus.biz>
     * @param object $event
     * @return boolean
     */
    public function can_show_countdown_module($event)
    {
        // MEC Settings
        $settings = $this->get_settings();

        // Countdown on single page is disabled
        if(!isset($settings['countdown_status']) or (isset($settings['countdown_status']) and !$settings['countdown_status'])) return false;

        $date = $event->date;
        $start_date = (isset($date['start']) and isset($date['start']['date'])) ? $date['start']['date'] : date('Y-m-d');

        $countdown_method = get_post_meta($event->ID, 'mec_countdown_method', true);
        if(trim($countdown_method) == '') $countdown_method = 'global';

        if($countdown_method == 'global') $ongoing = (isset($settings['hide_time_method']) and trim($settings['hide_time_method']) == 'end') ? true : false;
        else $ongoing = ($countdown_method == 'end') ? true : false;

        // The event is Expired/Passed
        if($this->is_past($start_date, date('Y-m-d')) and !$ongoing) return false;

        return true;
    }

    /**
    * @param null $event
    * @return DateTimeZone
    */
    public function get_TZO($event = NULL)
    {
        $timezone = $this->get_timezone($event);
        return new DateTimeZone($timezone);
    }

    /**
     * Get default timezone of WordPress
     * @author Webnus <info@webnus.biz>
     * @param mixed $event
     * @return string
     */
    public function get_timezone($event = NULL)
    {
        if(!is_null($event))
        {
            $event_id = ((is_object($event) and isset($event->ID)) ? $event->ID : $event);
            $timezone = get_post_meta($event_id, 'mec_timezone', true);

            if(trim($timezone) != '' and $timezone != 'global') $timezone_string = $timezone;
            else $timezone_string = get_option('timezone_string');
        }
        else $timezone_string = get_option('timezone_string');

        $gmt_offset = get_option('gmt_offset');

        if(trim($timezone_string) == '' and trim($gmt_offset)) $timezone_string = $this->get_timezone_by_offset($gmt_offset);
        elseif(trim($timezone_string) == '' and trim($gmt_offset) == '0')
        {
            $timezone_string = 'UTC';
        }

        return $timezone_string;
    }

    /**
     * Get GMT offset based on hours:minutes
     * @author Webnus <info@webnus.biz>
     * @param mixed $event
     * @param $date
     * @return string
     */
    public function get_gmt_offset($event = NULL, $date = NULL)
    {
        // Timezone
        $timezone = $this->get_timezone($event);

        // Convert to Date
        if($date and is_numeric($date)) $date = date('Y-m-d', $date);
        elseif(!$date) $date = current_time('Y-m-d');

        $UTC = new DateTimeZone('UTC');
        $TZ = new DateTimeZone($timezone);

        $gmt_offset_seconds = $TZ->getOffset((new DateTime($date, $UTC)));
        $gmt_offset = ($gmt_offset_seconds / HOUR_IN_SECONDS);

        $minutes = $gmt_offset*60;
        $hour_minutes = sprintf("%02d", $minutes%60);

        // Convert the hour into two digits format
        $h = ($minutes-$hour_minutes)/60;
        $hours = sprintf("%02d", abs($h));

        // Add - sign to the first of hour if it's negative
        if($h < 0) $hours = '-'.$hours;

        return (substr($hours, 0, 1) == '-' ? '' : '+').$hours.':'.(((int) $hour_minutes < 0) ? abs($hour_minutes) : $hour_minutes);
    }

    /**
     * Get GMT offset based on seconds
     * @author Webnus <info@webnus.biz>
     * @param $date
     * @param mixed $event
     * @return string
     */
    public function get_gmt_offset_seconds($date = NULL, $event = NULL)
    {
        if($date)
        {
            $timezone = new DateTimeZone($this->get_timezone($event));

            // Convert to Date
            if(is_numeric($date)) $date = date('Y-m-d', $date);

            $target = new DateTime($date, $timezone);
            return $timezone->getOffset($target);
        }
        else
        {
            $gmt_offset = get_option('gmt_offset');
            $seconds = $gmt_offset * HOUR_IN_SECONDS;

            return (substr($gmt_offset, 0, 1) == '-' ? '' : '+').$seconds;
        }
    }

    public function get_timezone_by_offset($offset)
    {
        $seconds = $offset*3600;

        $timezone = timezone_name_from_abbr('', $seconds, 0);
        if($timezone === false)
        {
            $timezones = array(
                '-12' => 'Pacific/Auckland',
                '-11.5' => 'Pacific/Auckland', // Approx
                '-11' => 'Pacific/Apia',
                '-10.5' => 'Pacific/Apia', // Approx
                '-10' => 'Pacific/Honolulu',
                '-9.5' => 'Pacific/Honolulu', // Approx
                '-9' => 'America/Anchorage',
                '-8.5' => 'America/Anchorage', // Approx
                '-8' => 'America/Los_Angeles',
                '-7.5' => 'America/Los_Angeles', // Approx
                '-7' => 'America/Denver',
                '-6.5' => 'America/Denver', // Approx
                '-6' => 'America/Chicago',
                '-5.5' => 'America/Chicago', // Approx
                '-5' => 'America/New_York',
                '-4.5' => 'America/New_York', // Approx
                '-4' => 'America/Halifax',
                '-3.5' => 'America/Halifax', // Approx
                '-3' => 'America/Sao_Paulo',
                '-2.5' => 'America/Sao_Paulo', // Approx
                '-2' => 'America/Sao_Paulo',
                '-1.5' => 'Atlantic/Azores', // Approx
                '-1' => 'Atlantic/Azores',
                '-0.5' => 'UTC', // Approx
                '0' => 'UTC',
                '0.5' => 'UTC', // Approx
                '1' => 'Europe/Paris',
                '1.5' => 'Europe/Paris', // Approx
                '2' => 'Europe/Helsinki',
                '2.5' => 'Europe/Helsinki', // Approx
                '3' => 'Europe/Moscow',
                '3.5' => 'Europe/Moscow', // Approx
                '4' => 'Asia/Dubai',
                '4.5' => 'Asia/Tehran',
                '5' => 'Asia/Karachi',
                '5.5' => 'Asia/Kolkata',
                '5.75' => 'Asia/Katmandu',
                '6' => 'Asia/Yekaterinburg',
                '6.5' => 'Asia/Yekaterinburg', // Approx
                '7' => 'Asia/Krasnoyarsk',
                '7.5' => 'Asia/Krasnoyarsk', // Approx
                '8' => 'Asia/Shanghai',
                '8.5' => 'Asia/Shanghai', // Approx
                '8.75' => 'Asia/Tokyo', // Approx
                '9' => 'Asia/Tokyo',
                '9.5' => 'Asia/Tokyo', // Approx
                '10' => 'Australia/Melbourne',
                '10.5' => 'Australia/Adelaide',
                '11' => 'Australia/Melbourne', // Approx
                '11.5' => 'Pacific/Auckland', // Approx
                '12' => 'Pacific/Auckland',
                '12.75' => 'Pacific/Apia', // Approx
                '13' => 'Pacific/Apia',
                '13.75' => 'Pacific/Honolulu', // Approx
                '14' => 'Pacific/Honolulu',
            );

            $timezone = isset($timezones[$offset]) ? $timezones[$offset] : NULL;
        }

        return $timezone;
    }

    /**
     * Get status of Google recaptcha
     * @author Webnus <info@webnus.biz>
     * @param string $section
     * @return boolean
     */
    public function get_recaptcha_status($section = '')
    {
        // MEC Settings
        $settings = $this->get_settings();

        $status = false;

        // Check if the feature is enabled
        if(isset($settings['google_recaptcha_status']) and $settings['google_recaptcha_status']) $status = true;

        // Check if the feature is enabled for certain section
        if($status and trim($section) and (!isset($settings['google_recaptcha_'.$section]) or (isset($settings['google_recaptcha_'.$section]) and !$settings['google_recaptcha_'.$section]))) $status = false;

        // Check if site key and secret key is not empty
        if($status and (!isset($settings['google_recaptcha_sitekey']) or (isset($settings['google_recaptcha_sitekey']) and trim($settings['google_recaptcha_sitekey']) == ''))) $status = false;
        if($status and (!isset($settings['google_recaptcha_secretkey']) or (isset($settings['google_recaptcha_secretkey']) and trim($settings['google_recaptcha_secretkey']) == ''))) $status = false;

        return $status;
    }

    /**
     * Get re-captcha verification from Google servers
     * @author Webnus <info@webnus.biz>
     * @param string $remote_ip
     * @param string $response
     * @return boolean
     */
    public function get_recaptcha_response($response, $remote_ip = NULL)
    {
        // get the IP
        if(is_null($remote_ip)) $remote_ip = (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '');

        // MEC Settings
        $settings = $this->get_settings();

        $data = array('secret'=>(isset($settings['google_recaptcha_secretkey']) ? $settings['google_recaptcha_secretkey'] : ''), 'remoteip'=>$remote_ip, 'v'=>'php_1.0', 'response'=>$response);

        $req = "";
        foreach($data as $key=>$value) $req .= $key.'='.urlencode(stripslashes($value)).'&';

        // Validate the re-captcha
        $getResponse = $this->get_web_page("https://www.google.com/recaptcha/api/siteverify?".trim($req, '& '));

        $answers = json_decode($getResponse, true);

        if(isset($answers['success']) and trim($answers['success'])) return true;
        else return false;
    }

    /**
     * Get current language of WordPress
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_current_language()
    {
        return apply_filters('plugin_locale', get_locale(), 'modern-events-calendar-lite');
    }

    /**
     * Write to a log file
     * @author Webnus <info@webnus.biz>
     * @param string|array $log_msg
     * @param string $path
     */
    public function debug_log($log_msg, $path = '')
	{
		if(trim($path) == '') $path = MEC_ABSPATH. 'log.txt';
		if(is_array($log_msg) or is_object($log_msg)) $log_msg = print_r($log_msg, true);

		$fh = fopen($path, 'a');
        fwrite($fh, $log_msg);
	}

    /**
     * Filter Skin parameters to add taxonomy, etc filters that come from WordPress Query
     * This used for taxonomy archive pages etc that are handled by WordPress itself
     * @author Webnus <info@webnus.biz>
     * @param array $atts
     * @return array
     */
    public function add_search_filters($atts = array())
    {
        // Taxonomy Archive Page
        if(is_tax())
        {
            $query = get_queried_object();
            $term_id = $query->term_id;

            if(!isset($atts['category'])) $atts['category'] = '';

            $atts['category'] = trim(trim($atts['category'], ', ').','.$term_id, ', ');
        }

        return $atts;
    }

     /**
     * Filter TinyMce Buttons
     * @author Webnus <info@webnus.biz>
     * @param array $buttons
     * @return array
     */
    public function add_mce_buttons($buttons)
    {
        array_push($buttons, 'mec_mce_buttons');
        return $buttons;
    }

    /**
    * Filter TinyMce plugins
    * @author Webnus <info@webnus.biz>
    * @param array $plugins
    * @return array
    */
    public function add_mce_external_plugins($plugins)
    {
        $plugins['mec_mce_buttons'] = $this->asset('js/mec-external.js');
        return $plugins;
    }

    /**
     * Return JSON output id and the name of a post type
     * @author Webnus <info@webnus.biz>
     * @param string $post_type
     * @return string JSON
     */
    public function mce_get_shortcode_list($post_type = 'mec_calendars')
    {
        $shortcodes = array();
        $shortcodes['mce_title'] =  __('M.E. Calender', 'modern-events-calendar-lite');
        $shortcodes['shortcodes'] = array();

        if(post_type_exists($post_type))
        {
            $shortcodes_list = get_posts(array(
                'post_type' => $post_type,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'order' => 'DESC'
            ));

            if(count($shortcodes_list))
            {
                foreach($shortcodes_list as $shortcode)
                {
                    $shortcode_item = array();
                    $shortcode_item['ID'] = $shortcode->ID;

                    // PostName
                    $shortcode_item['PN'] = $shortcode->post_name;
                    array_push($shortcodes['shortcodes'], $shortcode_item);
                }
            }
        }

        return json_encode($shortcodes);
    }

    /**
     * Return date_diff
     * @author Webnus <info@webnus.biz>
     * @param string $start_date
     * @param string $end_date
     * @return object
     */
    public function date_diff($start_date, $end_date)
    {
        if(version_compare(PHP_VERSION, '5.3.0', '>=')) return date_diff(date_create($start_date), date_create($end_date));
        else
        {
            $start = new DateTime($start_date);
            $end = new DateTime($end_date);
            $days = round(($end->format('U') - $start->format('U')) / (60*60*24));

            $interval = new stdClass();
            $interval->days = abs($days);
            $interval->invert = ($days >= 0 ? 0 : 1);

            return $interval;
        }
    }

    /**
     * Convert a certain time into seconds (Hours should be in 24 hours format)
     * @author Webnus <info@webnus.biz>
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     * @return int
     */
    public function time_to_seconds($hours, $minutes = 0, $seconds = 0)
    {
        return (((int) $hours * 3600) + ((int) $minutes * 60) + (int) $seconds);
    }

    /**
     * Convert a 12 hour format hour to a 24 format hour
     * @author Webnus <info@webnus.biz>
     * @param int $hour
     * @param string $ampm
     * @return int
     */
    public function to_24hours($hour, $ampm = 'PM')
    {
        // Time is already in 24 hour format
        if(is_null($ampm)) return $hour;

        $ampm = strtoupper($ampm);

        if($ampm == 'AM' and $hour < 12) return $hour;
        elseif($ampm == 'AM' and $hour == 12) return 24;
        elseif($ampm == 'PM' and $hour < 12) return $hour+12;
        elseif($ampm == 'PM' and $hour == 12) return 12;
        elseif($hour > 12) return $hour;
    }

    /**
     * Get rendered events based on a certain criteria
     * @author Webnus <info@webnus.biz>
     * @param array $args
     * @return array
     */
    public function get_rendered_events($args = array())
    {
        $events = array();
        $sorted = array();

        // Parse the args
        $args = wp_parse_args($args, array(
            'post_type'=>$this->get_main_post_type(),
            'posts_per_page'=>'-1',
            'post_status'=>'publish'
        ));

        // The Query
        $query = new WP_Query($args);

        if($query->have_posts())
        {
            // MEC Render Library
            $render = $this->getRender();

            // The Loop
            while($query->have_posts())
            {
                $query->the_post();

                $event_id = get_the_ID();
                $rendered = $render->data($event_id);

                $data = new stdClass();
                $data->ID = $event_id;
                $data->data = $rendered;
                $data->dates = $render->dates($event_id, $rendered, 6);
                $data->date = isset($data->dates[0]) ? $data->dates[0] : array();

                // Caclculate event start time
                $event_start_time = strtotime($data->date['start']['date']) + $rendered->meta['mec_start_day_seconds'];

                // Add the event into the to be sorted array
                if(!isset($sorted[$event_start_time])) $sorted[$event_start_time] = array();
                $sorted[$event_start_time][] = $data;
            }

            ksort($sorted, SORT_NUMERIC);
        }

        // Add sorted events to the results
        foreach($sorted as $sorted_events)
        {
            if(!is_array($sorted_events)) continue;
            foreach($sorted_events as $sorted_event) $events[$sorted_event->ID] = $sorted_event;
        }

        // Restore original Post Data
        wp_reset_postdata();

        return $events;
    }

    /**
     * Duplicate an event
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return boolean|int
     */
    public function duplicate($post_id)
    {
        // MEC DB Library
        $db = $this->getDB();

        $post = get_post($post_id);

        // Post is not exists
        if(!$post) return false;

        // New post data array
        $args = array
        (
            'comment_status'=>$post->comment_status,
            'ping_status'=>$post->ping_status,
            'post_author'=>$post->post_author,
            'post_content'=>$post->post_content,
            'post_excerpt'=>$post->post_excerpt,
            'post_name'=>sanitize_title($post->post_name.'-'.mt_rand(100, 999)),
            'post_parent'=>$post->post_parent,
            'post_password'=>$post->post_password,
            'post_status'=>'draft',
            'post_title'=>sprintf(__('Copy of %s', 'modern-events-calendar-lite'), $post->post_title),
            'post_type'=>$post->post_type,
            'to_ping'=>$post->to_ping,
            'menu_order'=>$post->menu_order
        );

        // insert the new post
        $new_post_id = wp_insert_post($args);

        // get all current post terms ad set them to the new post draft
        $taxonomies = get_object_taxonomies($post->post_type);
        foreach($taxonomies as $taxonomy)
        {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields'=>'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        // duplicate all post meta
        $post_metas = $db->select("SELECT `meta_key`, `meta_value` FROM `#__postmeta` WHERE `post_id`='$post_id'", 'loadObjectList');
        if(count($post_metas) != 0)
        {
            $sql_query = "INSERT INTO `#__postmeta` (post_id, meta_key, meta_value) ";

            foreach($post_metas as $meta_info)
            {
                $meta_key = $meta_info->meta_key;
                $meta_value = addslashes($meta_info->meta_value);

                $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }

            $sql_query .= implode(" UNION ALL ", $sql_query_sel);
            $db->q($sql_query);
        }

        // Duplicate MEC record
        $mec_data = $db->select("SELECT * FROM `#__mec_events` WHERE `post_id`='$post_id'", 'loadAssoc');

        $q1 = "";
        $q2 = "";
        foreach($mec_data as $key=>$value)
        {
            if(in_array($key, array('id', 'post_id'))) continue;

            $q1 .= "`$key`,";
            $q2 .= "'$value',";
        }

        $db->q("INSERT INTO `#__mec_events` (`post_id`,".trim($q1, ', ').") VALUES ('$new_post_id',".trim($q2, ', ').")");

        // Update Schedule
        $schedule = $this->getSchedule();
        $schedule->reschedule($new_post_id);

        return $new_post_id;
    }

    /**
     * Returns start/end date label
     * @author Webnus <info@webnus.biz>
     * @param array $start
     * @param array $end
     * @param string $format
     * @param string $separator
     * @param boolean $minify
     * @param integer $allday
     * @param object $event
     * @return string
     */
    public function date_label($start, $end, $format, $separator = ' - ', $minify = true, $allday = 0, $event = NULL)
    {
        $start_datetime = $start['date'];
        $end_datetime = $end['date'];

        if(isset($start['hour']))
        {
            $s_hour = $start['hour'];
            if(strtoupper($start['ampm']) == 'AM' and $s_hour == '0') $s_hour = 12;

            $start_datetime .= ' '.sprintf("%02d", $s_hour).':'.sprintf("%02d", $start['minutes']).' '.$start['ampm'];
        }

        if(isset($end['hour']))
        {
            $e_hour = $end['hour'];
            if(strtoupper($end['ampm']) == 'AM' and $e_hour == '0') $e_hour = 12;

            $end_datetime .= ' '.sprintf("%02d", $e_hour).':'.sprintf("%02d", $end['minutes']).' '.$end['ampm'];
        }

        $start_timestamp = strtotime($start_datetime);
        $end_timestamp = strtotime($end_datetime);

        $timezone_GMT = new DateTimeZone("GMT");
        $timezone_event = new DateTimeZone($this->get_timezone($event));

        $dt_now = new DateTime("now", $timezone_GMT);
        $dt_start = new DateTime($start_datetime, $timezone_GMT);
        $dt_end = new DateTime($end_datetime, $timezone_GMT);

        $offset_now = $timezone_event->getOffset($dt_now);
        $offset_start = $timezone_event->getOffset($dt_start);
        $offset_end = $timezone_event->getOffset($dt_end);

        if($offset_now != $offset_start and !function_exists('wp_date'))
        {
            $diff = $offset_start - $offset_now;
            if($diff > 0) $start_timestamp += $diff;
        }

        if($offset_now != $offset_end and !function_exists('wp_date'))
        {
            $diff = $offset_end - $offset_now;
            if($diff > 0) $end_timestamp += $diff;
        }

        // Event is All Day so remove the time formats
        if($allday)
        {
            foreach(array('a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'v') as $f) $format = str_replace($f, '', $format);
            $format = trim($format, ': ');
        }

        if($start_timestamp >= $end_timestamp) return '<span class="mec-start-date-label" itemprop="startDate">' . $this->date_i18n($format, $start_timestamp, $event) . '</span>';
        elseif($start_timestamp < $end_timestamp)
        {
            $start_date = $this->date_i18n($format, $start_timestamp, $event);
            $end_date = $this->date_i18n($format, $end_timestamp, $event);

            if($start_date == $end_date) return '<span class="mec-start-date-label" itemprop="startDate">' . $start_date . '</span>';
            else
            {
                $start_m = date('m', $start_timestamp);
                $end_m = date('m', $end_timestamp);

                $start_y = date('Y', $start_timestamp);
                $end_y = date('Y', $end_timestamp);

                // Same Month but Different Days
                if($minify and $start_m == $end_m and $start_y == $end_y and date('d', $start_timestamp) != date('d', $end_timestamp))
                {
                    $month_format = 'F';
                    if(strpos($format, 'm') !== false) $month_format = 'm';
                    elseif(strpos($format, 'M') !== false) $month_format = 'M';
                    elseif(strpos($format, 'n') !== false) $month_format = 'n';

                    $year_format = '';
                    if(strpos($format, 'Y') !== false) $year_format = 'Y';
                    elseif(strpos($format, 'y') !== false) $year_format = 'y';

                    $start_m = $this->date_i18n($month_format, $start_timestamp, $event);
                    $start_y = (trim($year_format) ? $this->date_i18n($year_format, $start_timestamp, $event) : '');
                    $end_y = (trim($year_format) ? $this->date_i18n($year_format, $end_timestamp, $event) : '');

                    $chars = str_split($format);

                    $date_label = '';
                    foreach($chars as $char)
                    {
                        if(in_array($char, array('d', 'D', 'j', 'l', 'N', 'S', 'w', 'z')))
                        {
                            $dot = (strpos($format, $char.'.') !== false);
                            $date_label .= $this->date_i18n($char, $start_timestamp, $event).($dot ? '.' : '') . ' - ' . $this->date_i18n($char, $end_timestamp, $event);
                        }
                        elseif(in_array($char, array('F', 'm', 'M', 'n')))
                        {
                            $date_label .= $start_m;
                        }
                        elseif(in_array($char, array('Y', 'y', 'o')))
                        {
                            $date_label .= ($start_y === $end_y ? $start_y : $start_y.' - '.$end_y);
                        }
                        elseif(in_array($char, array('e', 'I', 'O', 'P', 'p', 'T', 'Z')))
                        {
                            $date_label .= $this->date_i18n($char, $start_timestamp, $event);
                        }
                        else $date_label .= $char;
                    }

                    return '<span class="mec-start-date-label" itemprop="startDate">' .$date_label. '</span>';
                }
                else return '<span class="mec-start-date-label" itemprop="startDate">'.$this->date_i18n($format, $start_timestamp, $event).'</span><span class="mec-end-date-label" itemprop="endDate">'.$separator.$this->date_i18n($format, $end_timestamp, $event).'</span>';
            }
        }
    }

    public function dateify($event, $format, $separator = ' - ')
    {
        // Settings
        $settings = $this->get_settings();

        $time = sprintf("%02d", $event->data->meta['mec_end_time_hour']).':';
        $time .= sprintf("%02d", $event->data->meta['mec_end_time_minutes']).' ';
        $time .= $event->data->meta['mec_end_time_ampm'];

        $start_date = $event->date['start']['date'];
        $end_date = $event->date['end']['date'];

        $start_timestamp = strtotime($event->date['start']['date']);
        $end_timestamp = strtotime($event->date['end']['date']);

        // Midnight Hour
        $midnight_hour = (isset($settings['midnight_hour']) and $settings['midnight_hour']) ? $settings['midnight_hour'] : 0;
        $midnight = $end_timestamp+(3600*$midnight_hour);

        // End Date is before Midnight
        if($start_timestamp < $end_timestamp and $midnight >= strtotime($end_date.' '.$time)) $end_date = date('Y-m-d', ($end_timestamp - 86400));

        return $this->date_label(array('date' => $start_date), array('date' => $end_date), $format, $separator, true, 0, $event);
    }

    public function date_i18n($format, $time = NULL, $event = NULL)
    {
        // Force to numeric
        if(!is_numeric($time)) $time = strtotime($time);

        if($event and function_exists('wp_date'))
        {
            $TZO = ((isset($event->TZO) and $event->TZO and ($event->TZO instanceof DateTimeZone)) ? $event->TZO : $this->get_TZO($event));

            // Force to UTC
            $time = $time - $TZO->getOffset(new DateTime(date('Y-m-d H:i:s', $time)));

            return wp_date($format, $time, $TZO);
        }
        else
        {
            $timezone_GMT = new DateTimeZone("GMT");
            $timezone_site = new DateTimeZone($this->get_timezone());

            $dt_now = new DateTime("now", $timezone_GMT);
            $dt_time = new DateTime(date('Y-m-d', $time), $timezone_GMT);

            $offset_now = $timezone_site->getOffset($dt_now);
            $offset_time = $timezone_site->getOffset($dt_time);

            if($offset_now != $offset_time and !function_exists('wp_date'))
            {
                $diff = $offset_time - $offset_now;
                if($diff > 0) $time += $diff;
            }

            return date_i18n($format, $time);
        }
    }

    /**
     * Returns start/end time labels
     * @author Webnus <info@webnus.biz>
     * @param string $start
     * @param string $end
     * @param array $args
     * @return string
     */
    public function display_time($start = '', $end = '', $args = array())
    {
        if(!trim($start)) return '';

        $class = isset($args['class']) ? esc_attr($args['class']) : 'mec-time-details';

        $return = '<div class="'.$class.'">';
        if(trim($start)) $return .= '<span class="mec-start-time">' . $start . '</span>';
        if(trim($end)) $return .= ' - <span class="mec-end-time">' . $end . '</span>';
        $return .= '</div>';

        return $return;
    }

    /**
     * Returns end date of an event based on start date
     * @author Webnus <info@webnus.biz>
     * @param string $date
     * @param object $event
     * @return string
     */
    public function get_end_date($date, $event)
    {
        $start_date = isset($event->meta['mec_date']['start']) ? $event->meta['mec_date']['start'] : array();
        $end_date = isset($event->meta['mec_date']['end']) ? $event->meta['mec_date']['end'] : array();

        $event_period = $this->date_diff($start_date['date'], $end_date['date']);
        $event_period_days = $event_period ? $event_period->days : 0;

        $finish_date = array('date'=>$event->mec->end, 'hour'=>$event->meta['mec_date']['end']['hour'], 'minutes'=>$event->meta['mec_date']['end']['minutes'], 'ampm'=>(isset($event->meta['mec_date']['end']['ampm']) ? $event->meta['mec_date']['end']['ampm'] : ''));

        // DB
        $db = $this->getDB();

        // Event Passed
        $past = $this->is_past($finish_date['date'], $date);

        // Normal event
        if(isset($event->mec->repeat) and $event->mec->repeat == '0')
        {
            return isset($end_date['date']) ? $end_date['date'] : $date;
        }
        // Past Event
        elseif($past)
        {
            return isset($end_date['date']) ? $end_date['date'] : $date;
        }
        // Custom Days
        elseif($custom_date = $db->select("SELECT `dend` FROM `#__mec_dates` WHERE `post_id`='".$event->ID."' AND `dstart`<='".$date."' AND `dend`>='".$date."' ORDER BY `id` DESC LIMIT 1", 'loadResult'))
        {
            return $custom_date;
        }
        elseif(!$past)
        {
            /**
             * Multiple Day Event
             * Check to see if today is between start day and end day.
             * For example start day is 5 and end day is 15 but we're in 9th so only 6 days remained till ending the event not 10 days.
             */
            if($event_period_days)
            {
                $start_day = date('j', strtotime($start_date['date']));
                $day = date('j', strtotime($date));

                $passed_days = 0;
                if($day >= $start_day) $passed_days = $day - $start_day;
                else $passed_days = ($day+date('t', strtotime($start_date['date']))) - $start_day;

                $event_period_days = $event_period_days - $passed_days;
            }

            return date('Y-m-d', strtotime('+'.$event_period_days.' Days', strtotime($date)));
        }
    }

    /**
     * Get Archive Status of MEC
     * @author Webnus <info@webnus.biz>
     * @return int
     */
    public function get_archive_status()
    {
        $settings = $this->get_settings();

        $status = isset($settings['archive_status']) ? $settings['archive_status'] : '1';
        return apply_filters('mec_archive_status', $status);
    }

    /**
     * Check to see if a table exists or not
     * @author Webnus <info@webnus.biz>
     * @param string $table
     * @return boolean
     */
    public function table_exists($table = 'mec_events')
    {
        // MEC DB library
        $db = $this->getDB();

        return $db->q("SHOW TABLES LIKE '#__$table'");
    }

    /**
     * Create MEC Tables
     * @author Webnus <info@webnus.biz>
     * @return boolean
     */
    public function create_mec_tables()
    {
        // MEC Events table already exists
        if($this->table_exists('mec_events') and $this->table_exists('mec_dates') and $this->table_exists('mec_occurrences') and $this->table_exists('mec_users') and $this->table_exists('mec_bookings')) return true;

        // MEC File library
        $file = $this->getFile();

        // MEC DB library
        $db = $this->getDB();

        // Run Queries
        $query_file = MEC_ABSPATH. 'assets' .DS. 'sql' .DS. 'tables.sql';
		if($file->exists($query_file))
		{
			$queries = $file->read($query_file);
            $sqls = explode(';', $queries);

            foreach($sqls as $sql)
            {
                $sql = trim($sql, '; ');
                if(trim($sql) == '') continue;

                $sql .= ';';

                try
                {
                    $db->q($sql);
                }
                catch (Exception $e){}
            }
		}

		return true;
    }

    /**
     * Return HTML email type
     * @author Webnus <info@webnus.biz>
     * @param string $content_type
     * @return string
     */
    public function html_email_type($content_type)
    {
        return 'text/html';
    }

    public function get_next_upcoming_event()
    {
        MEC::import('app.skins.list', true);

        // Get list skin
        $list = new MEC_skin_list();

        // Attributes
        $atts = array(
            'show_only_past_events'=>0,
            'show_past_events'=>0,
            'start_date_type'=>'today',
            'sk-options'=> array(
                'list' => array('limit'=>1)
            ),
        );

        // Initialize the skin
        $list->initialize($atts);

        // General Settings
        $settings = $this->get_settings();

        // Disable Ongoing Events
        $disable_for_ongoing = (isset($settings['countdown_disable_for_ongoing_events']) and $settings['countdown_disable_for_ongoing_events']);
        if($disable_for_ongoing) $list->hide_time_method = 'start';

        // Fetch the events
        $list->fetch();

        $events = $list->events;
        $key = key($events);

        return (isset($events[$key][0]) ? $events[$key][0] : array());
    }

    /**
     * Return a web page
     * @author Webnus <info@webnus.biz>
     * @param string $url
     * @param int $timeout
     * @return string
     */
    public function get_web_page($url, $timeout = 20)
	{
		$result = false;

		// Doing WordPress Remote
		if(function_exists('wp_remote_get'))
		{
            $result = wp_remote_retrieve_body(wp_remote_get($url, array(
                'body' => null,
                'timeout' => $timeout,
                'redirection' => 5,
            )));
		}

		// Doing FGC
		if($result === false)
		{
            $http = array();
			$result = @file_get_contents($url, false, stream_context_create(array('http'=>$http)));
		}

		return $result;
	}

    public function save_events($events = array())
    {
        $ids = array();

        foreach($events as $event) $ids[] = $this->save_event($event, (isset($event['ID']) ? $event['ID'] : NULL));
        return $ids;
    }

    public function save_event($event = array(), $post_id = NULL)
    {
        $post = array('post_title'=>$event['title'], 'post_content'=>(isset($event['content']) ? $event['content'] : ''), 'post_type'=>$this->get_main_post_type(), 'post_status'=>(isset($event['status']) ? $event['status'] : 'publish'));

        // Update previously inserted post
        if(!is_null($post_id)) $post['ID'] = $post_id;

        $post_id = wp_insert_post($post);

        update_post_meta($post_id, 'mec_location_id', (isset($event['location_id']) ? $event['location_id'] : 1));
        update_post_meta($post_id, 'mec_dont_show_map', 0);
        update_post_meta($post_id, 'mec_organizer_id', (isset($event['organizer_id']) ? $event['organizer_id'] : 1));

        $start_time_hour = (isset($event['start_time_hour']) ? $event['start_time_hour'] : 8);
        $start_time_minutes = (isset($event['start_time_minutes']) ? $event['start_time_minutes'] : 0);
        $start_time_ampm = (isset($event['start_time_ampm']) ? $event['start_time_ampm'] : 'AM');

        $end_time_hour = (isset($event['end_time_hour']) ? $event['end_time_hour'] : 6);
        $end_time_minutes = (isset($event['end_time_minutes']) ? $event['end_time_minutes'] : 0);
        $end_time_ampm = (isset($event['end_time_ampm']) ? $event['end_time_ampm'] : 'PM');

        $allday = (isset($event['allday']) ? $event['allday'] : 0);
        $time_comment = (isset($event['time_comment']) ? $event['time_comment'] : '');
        $hide_time = ((isset($event['date']) and isset($event['date']['hide_time'])) ? $event['date']['hide_time'] : 0);
        $hide_end_time = ((isset($event['date']) and isset($event['date']['hide_end_time'])) ? $event['date']['hide_end_time'] : 0);

        $day_start_seconds = $this->time_to_seconds($this->to_24hours($start_time_hour, $start_time_ampm), $start_time_minutes);
        $day_end_seconds = $this->time_to_seconds($this->to_24hours($end_time_hour, $end_time_ampm), $end_time_minutes);

        update_post_meta($post_id, 'mec_allday', $allday);
        update_post_meta($post_id, 'mec_hide_time', $hide_time);
        update_post_meta($post_id, 'mec_hide_end_time', $hide_end_time);

        update_post_meta($post_id, 'mec_start_date', $event['start']);
        update_post_meta($post_id, 'mec_start_time_hour', $start_time_hour);
        update_post_meta($post_id, 'mec_start_time_minutes', $start_time_minutes);
        update_post_meta($post_id, 'mec_start_time_ampm', $start_time_ampm);
        update_post_meta($post_id, 'mec_start_day_seconds', $day_start_seconds);

        update_post_meta($post_id, 'mec_end_date', $event['end']);
        update_post_meta($post_id, 'mec_end_time_hour', $end_time_hour);
        update_post_meta($post_id, 'mec_end_time_minutes', $end_time_minutes);
        update_post_meta($post_id, 'mec_end_time_ampm', $end_time_ampm);
        update_post_meta($post_id, 'mec_end_day_seconds', $day_end_seconds);

        update_post_meta($post_id, 'mec_repeat_status', $event['repeat_status']);
        update_post_meta($post_id, 'mec_repeat_type', $event['repeat_type']);
        update_post_meta($post_id, 'mec_repeat_interval', $event['interval']);

        update_post_meta($post_id, 'mec_certain_weekdays', explode(',', trim((isset($event['weekdays']) ? $event['weekdays'] : ''), ', ')));

        $date = array
        (
            'start'=>array('date'=>$event['start'], 'hour'=>$start_time_hour, 'minutes'=>$start_time_minutes, 'ampm'=>$start_time_ampm),
            'end'=>array('date'=>$event['end'], 'hour'=>$end_time_hour, 'minutes'=>$end_time_minutes, 'ampm'=>$end_time_ampm),
            'repeat'=>((isset($event['date']) and isset($event['date']['repeat']) and is_array($event['date']['repeat'])) ? $event['date']['repeat'] : array()),
            'allday'=>$allday,
            'hide_time'=>((isset($event['date']) and isset($event['date']['hide_time'])) ? $event['date']['hide_time'] : 0),
            'hide_end_time'=>((isset($event['date']) and isset($event['date']['hide_end_time'])) ? $event['date']['hide_end_time'] : 0),
            'comment'=>$time_comment,
        );

        update_post_meta($post_id, 'mec_date', $date);

        // Finish Date
        $finish_date = (isset($event['finish']) ? $event['finish'] : '');
        if($finish_date)
        {
            update_post_meta($post_id, 'mec_repeat_end_at_date', $finish_date);
            update_post_meta($post_id, 'mec_repeat_end', 'date');
        }

        // Not In Days
        $not_in_days = (isset($event['not_in_days']) ? $event['not_in_days'] : '');
        if($not_in_days) update_post_meta($post_id, 'mec_not_in_days', $not_in_days);

        // Creating $mec array for inserting in mec_events table
        $mec = array('post_id'=>$post_id, 'start'=>$event['start'], 'repeat'=>$event['repeat_status'], 'rinterval'=>$event['interval'], 'time_start'=>$day_start_seconds, 'time_end'=>$day_end_seconds);

        // Add parameters to the $mec
        $mec['end'] = (trim($finish_date) ? $finish_date : '0000-00-00');
        $mec['year'] = isset($event['year']) ? $event['year'] : NULL;
        $mec['month'] = isset($event['month']) ? $event['month'] : NULL;
        $mec['day'] = isset($event['day']) ? $event['day'] : NULL;
        $mec['week'] = isset($event['week']) ? $event['week'] : NULL;
        $mec['weekday'] = isset($event['weekday']) ? $event['weekday'] : NULL;
        $mec['weekdays'] = isset($event['weekdays']) ? $event['weekdays'] : NULL;
        $mec['days'] = isset($event['days']) ? $event['days'] : '';
        $mec['not_in_days'] = $not_in_days;

        // MEC DB Library
        $db = $this->getDB();

        // Update MEC Events Table
        $mec_event_id = $db->select("SELECT `id` FROM `#__mec_events` WHERE `post_id`='$post_id'", 'loadResult');

        if(!$mec_event_id)
        {
            $q1 = "";
            $q2 = "";

            foreach($mec as $key=>$value)
            {
                $q1 .= "`$key`,";

                if(is_null($value)) $q2 .= "NULL,";
                else $q2 .= "'$value',";
            }

            $db->q("INSERT INTO `#__mec_events` (".trim($q1, ', ').") VALUES (".trim($q2, ', ').")", 'INSERT');
        }
        else
        {
            $q = "";

            foreach($mec as $key=>$value)
            {
                if(is_null($value)) $q .= "`$key`=NULL,";
                else $q .= "`$key`='$value',";
            }

            $db->q("UPDATE `#__mec_events` SET ".trim($q, ', ')." WHERE `id`='$mec_event_id'");
        }

        if(isset($event['meta']) and is_array($event['meta'])) foreach($event['meta'] as $key=>$value) update_post_meta($post_id, $key, $value);

        // Update Schedule
        $schedule = $this->getSchedule();
        $schedule->reschedule($post_id, $schedule->get_reschedule_maximum($event['repeat_type']));

        return $post_id;
    }

    public function save_category($category = array())
    {
        $name = isset($category['name']) ? $category['name'] : '';
        if(!trim($name)) return false;

        $term = get_term_by('name', $name, 'mec_category');

        // Term already exists
        if(is_object($term) and isset($term->term_id)) return $term->term_id;

        $term = wp_insert_term($name, 'mec_category');

        // An error ocurred
        if(is_wp_error($term)) return false;

        $category_id = $term['term_id'];
        if(!$category_id) return false;

        return $category_id;
    }

    public function save_tag($tag = array())
    {
        $name = isset($tag['name']) ? $tag['name'] : '';
        if(!trim($name)) return false;

        $term = get_term_by('name', $name, apply_filters('mec_taxonomy_tag', ''));

        // Term already exists
        if(is_object($term) and isset($term->term_id)) return $term->term_id;

        $term = wp_insert_term($name, apply_filters('mec_taxonomy_tag', ''));

        // An error ocurred
        if(is_wp_error($term)) return false;

        $tag_id = $term['term_id'];
        if(!$tag_id) return false;

        return $tag_id;
    }

    public function save_label($label = array())
    {
        $name = isset($label['name']) ? $label['name'] : '';
        if(!trim($name)) return false;

        $term = get_term_by('name', $name, 'mec_label');

        // Term already exists
        if(is_object($term) and isset($term->term_id)) return $term->term_id;

        $term = wp_insert_term($name, 'mec_label');

        // An error ocurred
        if(is_wp_error($term)) return false;

        $label_id = $term['term_id'];
        if(!$label_id) return false;

        $color = isset($label['color']) ? $label['color'] : '';
        update_term_meta($label_id, 'color', $color);

        return $label_id;
    }

    public function save_organizer($organizer = array())
    {
        $name = isset($organizer['name']) ? $organizer['name'] : '';
        if(!trim($name)) return false;

        $term = get_term_by('name', $name, 'mec_organizer');

        // Term already exists
        if(is_object($term) and isset($term->term_id)) return $term->term_id;

        $term = wp_insert_term($name, 'mec_organizer');

        // An error ocurred
        if(is_wp_error($term)) return false;

        $organizer_id = $term['term_id'];
        if(!$organizer_id) return false;

        if(isset($organizer['tel']) && strpos($organizer['tel'], '@') !== false)
        {
            // Just for EventON
            $tel = '';
            $email = (isset($organizer['tel']) and trim($organizer['tel'])) ? $organizer['tel'] : '';
        }
        else
        {
            $tel = (isset($organizer['tel']) and trim($organizer['tel'])) ? $organizer['tel'] : '';
            $email = (isset($organizer['email']) and trim($organizer['email'])) ? $organizer['email'] : '';
        }

        $url = (isset($organizer['url']) and trim($organizer['url'])) ? $organizer['url'] : '';
        $thumbnail = isset($organizer['thumbnail']) ? $organizer['thumbnail'] : '';

        update_term_meta($organizer_id, 'tel', $tel);
        update_term_meta($organizer_id, 'email', $email);
        update_term_meta($organizer_id, 'url', $url);
        if(trim($thumbnail)) update_term_meta($organizer_id, 'thumbnail', $thumbnail);

        return $organizer_id;
    }

    public function save_location($location = array())
    {
        $name = isset($location['name']) ? $location['name'] : '';
        if(!trim($name)) return false;

        $term = get_term_by('name', $name, 'mec_location');

        // Term already exists
        if(is_object($term) and isset($term->term_id)) return $term->term_id;

        $term = wp_insert_term($name, 'mec_location');

        // An error ocurred
        if(is_wp_error($term)) return false;

        $location_id = $term['term_id'];
        if(!$location_id) return false;

        $latitude = (isset($location['latitude']) and trim($location['latitude'])) ? $location['latitude'] : 0;
        $longitude = (isset($location['longitude']) and trim($location['longitude'])) ? $location['longitude'] : 0;
        $address = isset($location['address']) ? $location['address'] : '';
        $thumbnail = isset($location['thumbnail']) ? $location['thumbnail'] : '';
        $url = isset($location['url']) ? $location['url'] : '';

        if(!trim($latitude) or !trim($longitude))
        {
            $geo_point = $this->get_lat_lng($address);

            $latitude = $geo_point[0];
            $longitude = $geo_point[1];
        }

        update_term_meta($location_id, 'address', $address);
        update_term_meta($location_id, 'latitude', $latitude);
        update_term_meta($location_id, 'longitude', $longitude);
        update_term_meta($location_id, 'url', $url);

        if(trim($thumbnail)) update_term_meta($location_id, 'thumbnail', $thumbnail);
        return $location_id;
    }

    public function save_speaker($speaker = array())
    {
        $name = isset($speaker['name']) ? $speaker['name'] : '';
        if(!trim($name)) return false;

        $term = get_term_by('name', $name, 'mec_speaker');

        // Term already exists
        if(is_object($term) and isset($term->term_id)) return $term->term_id;

        $term = wp_insert_term($name, 'mec_speaker');

        // An error ocurred
        if(is_wp_error($term)) return false;

        $speaker_id = $term['term_id'];
        if(!$speaker_id) return false;

        $job_title = (isset($speaker['job_title']) and trim($speaker['job_title'])) ? $speaker['job_title'] : '';
        $tel = (isset($speaker['tel']) and trim($speaker['tel'])) ? $speaker['tel'] : '';
        $email = (isset($speaker['email']) and trim($speaker['email'])) ? $speaker['email'] : '';
        $facebook = (isset($speaker['facebook']) and trim($speaker['facebook'])) ? esc_url($speaker['facebook']) : '';
        $twitter = (isset($speaker['twitter']) and trim($speaker['twitter'])) ? esc_url($speaker['twitter']) : '';
        $instagram = (isset($speaker['instagram']) and trim($speaker['instagram'])) ? esc_url($speaker['instagram']) : '';
        $linkedin = (isset($speaker['linkedin']) and trim($speaker['linkedin'])) ? esc_url($speaker['linkedin']) : '';
        $website = (isset($speaker['website']) and trim($speaker['website'])) ? esc_url($speaker['website']) : '';
        $thumbnail = isset($speaker['thumbnail']) ? $speaker['thumbnail'] : '';

        update_term_meta($speaker_id, 'job_title', $job_title);
        update_term_meta($speaker_id, 'tel', $tel);
        update_term_meta($speaker_id, 'email', $email);
        update_term_meta($speaker_id, 'facebook', $facebook);
        update_term_meta($speaker_id, 'twitter', $twitter);
        update_term_meta($speaker_id, 'instagram', $instagram);
        update_term_meta($speaker_id, 'linkedin', $linkedin);
        update_term_meta($speaker_id, 'website', $website);
        if(trim($thumbnail)) update_term_meta($speaker_id, 'thumbnail', $thumbnail);

        return $speaker_id;
    }

    /**
     * Returns data export array for one event
     * @author Webnus <info@webnus.biz>
     * @param int $event_id
     * @return string
     */
    public function export_single($event_id)
    {
        // MEC Render Library
        $render = $this->getRender();

        return $render->data($event_id);
    }

    /**
     * Converts array to XML string
     * @author Webnus <info@webnus.biz>
     * @param array $data
     * @return string
     */
    public function xml_convert($data)
    {
        $main_node = array_keys($data);

        // Creating SimpleXMLElement object
        $xml = new SimpleXMLElement('<?xml version="1.0"?><'.$main_node[0].'></'.$main_node[0].'>');

        // Convert array to xml
        $this->array_to_xml($data[$main_node[0]], $xml);

        // Return XML String
        return $xml->asXML();
    }

    public function array_to_xml($data, &$xml)
    {
        foreach($data as $key=>$value)
        {
            if(is_numeric($key)) $key = 'item';

            if(is_array($value))
            {
                $subnode = $xml->addChild($key);
                $this->array_to_xml($value, $subnode);
            }
            elseif(is_object($value))
            {
                $subnode = $xml->addChild($key);
                $this->array_to_xml($value, $subnode);
            }
            else
            {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
    }

    /**
     * Returns Weekdays Day Numbers
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_weekdays()
    {
        $weekdays = array(1,2,3,4,5);

        // Get weekdays from options
        $settings = $this->get_settings();
        if(isset($settings['weekdays']) and is_array($settings['weekdays']) and count($settings['weekdays'])) $weekdays = $settings['weekdays'];

        return apply_filters('mec_weekday_numbers', $weekdays);
    }

    /**
     * Returns Weekends Day Numbers
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_weekends()
    {
        $weekends = array(6,7);

        // Get weekdays from options
        $settings = $this->get_settings();
        if(isset($settings['weekends']) and is_array($settings['weekends']) and count($settings['weekends'])) $weekends = $settings['weekends'];

        return apply_filters('mec_weekend_numbers', $weekends);
    }

    /**
     * Returns Event link with Occurrence Date
     * @author Webnus <info@webnus.biz>
     * @param string|object $event
     * @param string $date
     * @param boolean $force
     * @param array $time
     * @return string
     */
    public function get_event_date_permalink($event, $date = NULL, $force = false, $time = NULL)
    {
        // Get MEC Options
        $settings = $this->get_settings();

        if(is_object($event))
        {
            // Event Permalink
            $url = $event->data->permalink;

            // Return same URL if date is not provided
            if(is_null($date)) return apply_filters('mec_event_permalink', $url);

            // Single Page Date method is set to next date
            if(!$force and (!isset($settings['single_date_method']) or (isset($settings['single_date_method']) and $settings['single_date_method'] == 'next'))) return apply_filters('mec_event_permalink', $url);

            if(is_array($time) and isset($time['start_timestamp'])) $time_str = date('H:i:s', $time['start_timestamp']);
            elseif(isset($event->data->time) and is_array($event->data->time) and isset($event->data->time['start_timestamp'])) $time_str = date('H:i:s', $event->data->time['start_timestamp']);
            elseif(isset($event->data->time) and is_array($event->data->time) and isset($event->data->time['start_raw'])) $time_str = date('H:i:s', strtotime($event->data->time['start_raw']));
            else $time_str = ((is_array($time) and isset($time['start_raw'])) ? $time['start_raw'] : $event->data->time['start_raw']);

            // Timestamp
            $timestamp = strtotime($date.' '.$time_str);

            // Do not add occurrence when custom link is set
            $read_more = (isset($event->data->meta) and isset($event->data->meta['mec_read_more']) and filter_var($event->data->meta['mec_read_more'], FILTER_VALIDATE_URL)) ? $event->data->meta['mec_read_more'] : NULL;
            $read_more_occ_url = MEC_feature_occurrences::param($event->ID, $timestamp, 'read_more', $read_more);

            if($read_more_occ_url and filter_var($read_more_occ_url, FILTER_VALIDATE_URL)) $url = $read_more_occ_url;
            if($read_more_occ_url) return apply_filters('mec_event_permalink', $url);

            // Add Date to the URL
            $url = $this->add_qs_var('occurrence', $date, $url);

            $repeat_type = (isset($event->data->meta['mec_repeat_type']) ? $event->data->meta['mec_repeat_type'] : '');
            if($repeat_type == 'custom_days' and isset($event->data->time) and isset($event->data->time['start_raw']))
            {
                // Add Time
                $url = $this->add_qs_var('time', $timestamp, $url);
            }

            return apply_filters('mec_event_permalink', $url);
        }
        else
        {
            // Event Permalink
            $url = $event;

            // Return same URL if data is not provided
            if(is_null($date)) return apply_filters('mec_event_permalink', $url);

            // Single Page Date method is set to next date
            if(!$force and (!isset($settings['single_date_method']) or (isset($settings['single_date_method']) and $settings['single_date_method'] == 'next'))) return apply_filters('mec_event_permalink', $url);

            return apply_filters('mec_event_permalink', $this->add_qs_var('occurrence', $date, $url));
        }
    }

    /**
     * Register MEC Activity Action Type in BuddeyPress
     * @return void
     */
    public function bp_register_activity_actions()
    {
        bp_activity_set_action(
            'mec',
            'booked_event',
            __('Booked an event.', 'modern-events-calendar-lite')
        );
    }

    /**
     * Add a new activity to BuddyPress when a user book an event
     * @param int $book_id
     * @return boolean|int
     */
    public function bp_add_activity($book_id)
    {
        // Get MEC Options
        $settings = $this->get_settings();

        // BuddyPress integration is disabled
        if(!isset($settings['bp_status']) or (isset($settings['bp_status']) and !$settings['bp_status'])) return false;

        // BuddyPress add activity is disabled
        if(!isset($settings['bp_add_activity']) or (isset($settings['bp_add_activity']) and !$settings['bp_add_activity'])) return false;

        // BuddyPress is not installed or activated
        if(!function_exists('bp_activity_add')) return false;

        $verification = get_post_meta($book_id, 'mec_verified', true);
        $confirmation = get_post_meta($book_id, 'mec_confirmed', true);

        // Booking is not verified or confirmed
        if($verification != 1 or $confirmation != 1) return false;

        $event_id = get_post_meta($book_id, 'mec_event_id', true);
        $booker_id = get_post_field('post_author', $book_id);

        $event_title = get_the_title($event_id);
        $event_link = get_the_permalink($event_id);

        $profile_link = bp_core_get_userlink($booker_id);
        $bp_activity_id = get_post_meta($book_id, 'mec_bp_activity_id', true);

        $activity_id = bp_activity_add(array
        (
            'id'=>$bp_activity_id,
            'action'=>sprintf(__('%s booked %s event.', 'modern-events-calendar-lite'), $profile_link, '<a href="'.$event_link.'">'.$event_title.'</a>'),
            'component'=>'mec',
            'type'=>'booked_event',
            'primary_link'=>$event_link,
            'user_id'=>$booker_id,
            'item_id'=>$book_id,
            'secondary_item_id'=>$event_id,
        ));

        // Set Activity ID
        update_post_meta($book_id, 'mec_bp_activity_id', $activity_id);

        return $activity_id;
    }

    public function bp_add_profile_menu()
    {
        // Get MEC Options
        $settings = $this->get_settings();

        // BuddyPress integration is disabled
        if(!isset($settings['bp_status']) or (isset($settings['bp_status']) and !$settings['bp_status'])) return false;

        // BuddyPress events menus is disabled
        if(!isset($settings['bp_profile_menu']) or (isset($settings['bp_profile_menu']) and !$settings['bp_profile_menu'])) return false;

        // User is not logged in
        if(!is_user_logged_in()) return false;

        global $bp;

        // Loggedin User is not Displayed User
        if(!isset($bp->displayed_user) or (isset($bp->displayed_user) and isset($bp->displayed_user->id) and get_current_user_id() != $bp->displayed_user->id)) return false;

        bp_core_new_nav_item(array(
            'name' => __('Events', 'modern-events-calendar-lite'),
            'slug' => 'mec-events',
            'screen_function' => array($this, 'bp_profile_menu_screen'),
            'position' => 30,
            'parent_url' => bp_loggedin_user_domain() . '/mec-events/',
            'parent_slug' => $bp->profile->slug,
            'default_subnav_slug' => 'events'
        ));
    }

    public function bp_profile_menu_screen()
    {
        add_action('bp_template_title', array($this, 'bp_profile_menu_title'));
        add_action('bp_template_content', array($this, 'bp_profile_menu_content'));

        bp_core_load_template(array('buddypress/members/single/plugins'));
    }

    public function bp_profile_menu_title()
    {
        echo esc_html__('Events', 'modern-events-calendar-lite');
    }

    public function bp_profile_menu_content()
    {
        echo do_shortcode('[MEC_fes_list relative-link="1"]');
    }

    /**
     * Add booker information to mailchimp list
     * @param int $book_id
     * @return boolean
     */
    public function mailchimp_add_subscriber($book_id)
    {
        // Get MEC Options
        $settings = $this->get_settings();

        // Mailchim integration is disabled
        if(!isset($settings['mchimp_status']) or (isset($settings['mchimp_status']) and !$settings['mchimp_status'])) return false;

        $api_key = isset($settings['mchimp_api_key']) ? $settings['mchimp_api_key'] : '';
        $list_id = isset($settings['mchimp_list_id']) ? $settings['mchimp_list_id'] : '';

        // Mailchim credentials are required
        if(!trim($api_key) or !trim($list_id)) return false;

        // Options
        $date_format = (isset($settings['booking_date_format1']) and trim($settings['booking_date_format1'])) ? $settings['booking_date_format1'] : 'Y-m-d';
        $segment_status = (isset($settings['mchimp_segment_status']) and $settings['mchimp_segment_status']);

        // Booking Date
        $mec_date = get_post_meta($book_id, 'mec_date', true);
        $dates = (trim($mec_date) ? explode(':', $mec_date) : array());
        $booking_date = date($date_format, $dates[0]);

        // Event Title
        $event_id = get_post_meta($book_id, 'mec_event_id', true);
        $event = get_post($event_id);

        $book = $this->getBook();
        $attendees = $book->get_attendees($book_id);

        $data_center = substr($api_key, strpos($api_key, '-') + 1);
        $subscription_status = isset($settings['mchimp_subscription_status']) ? $settings['mchimp_subscription_status'] : 'subscribed';

        $member_response = NULL;
        $did = array();

        foreach($attendees as $attendee)
        {
            // Name
            $name = ((isset($attendee['name']) and trim($attendee['name'])) ? $attendee['name'] : '');

            // Email
            $email = ((isset($attendee['email']) and trim($attendee['email'])) ? $attendee['email'] : '');
            if(!is_email($email)) continue;

            // No Duplicate
            if(in_array($email, $did)) continue;
            $did[] = $email;

            $names = explode(' ', $name);

            $first_name = $names[0];
            unset($names[0]);

            $last_name = implode(' ', $names);

            // UPSERT
            $member_response = wp_remote_request('https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/'.md5(strtolower($email)), array(
                'method' => 'PUT',
                'body' => json_encode(array
                (
                    'email_address'=>$email,
                    'status'=>$subscription_status,
                    'merge_fields'=>array
                    (
                        'FNAME'=>$first_name,
                        'LNAME'=>$last_name
                    ),
                    'tags'=>array($booking_date, $event->post_title)
                )),
                'timeout' => '10',
                'redirection' => '10',
                'headers' => array('Content-Type' => 'application/json', 'Authorization' => 'Basic ' . base64_encode('user:' . $api_key)),
            ));

            // TAGS
            wp_remote_post('https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/'.md5(strtolower($email)).'/tags', array(
                'body' => json_encode(array
                (
                    'tags'=>array(
                        array('name' => $booking_date, 'status' => 'active'),
                        array('name' => $event->post_title, 'status' => 'active')
                    )
                )),
                'timeout' => '10',
                'redirection' => '10',
                'headers' => array('Content-Type' => 'application/json', 'Authorization' => 'Basic ' . base64_encode('user:' . $api_key)),
            ));
        }

        // Handle Segment
        if($segment_status)
        {
            wp_remote_post('https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/segments/', array(
                'body' => json_encode(array
                (
                    'name'=>sprintf('%s at %s', $event->post_title, $booking_date),
                    'options'=>array(
                        'match'=>'any',
                        'conditions'=>array()
                    )
                )),
                'timeout' => '10',
                'redirection' => '10',
                'headers' => array('Content-Type' => 'application/json', 'Authorization' => 'Basic ' . base64_encode('user:' . $api_key)),
            ));
        }

        return ($member_response ? wp_remote_retrieve_response_code($member_response) : false);
    }

    /**
     * Add booker information to campaign monitor list
     * @param int $book_id
     * @return boolean
     */
    public function campaign_monitor_add_subscriber($book_id)
    {
        require_once MEC_ABSPATH.'/app/api/Campaign_Monitor/csrest_subscribers.php';
        // Get MEC Options
        $settings = $this->get_settings();

        // Campaign Monitor integration is disabled
        if(!isset($settings['campm_status']) or (isset($settings['campm_status']) and !$settings['campm_status'])) return false;

        $api_key = isset($settings['campm_api_key']) ? $settings['campm_api_key'] : '';
        $list_id = isset($settings['campm_list_id']) ? $settings['campm_list_id'] : '';

        // Campaign Monitor credentials are required
        if(!trim($api_key) or !trim($list_id)) return false;

        // MEC User
        $u = $this->getUser();
        $booker = $u->booking($book_id);

        $wrap = new CS_REST_Subscribers($list_id, $api_key);
        $result = $wrap->add(array(
            'EmailAddress' => $booker->user_email,
            'Name' => $booker->first_name . ' ' .$booker->last_name,
            'ConsentToTrack' => 'yes',
            'Resubscribe' => true
        ));
    }

    /**
     * Add booker information to mailerlite list
     * @param int $book_id
     * @return boolean}int
     */
    public function mailerlite_add_subscriber($book_id)
    {
        // Get MEC Options
        $settings = $this->get_settings();

        // mailerlite integration is disabled
        if(!isset($settings['mailerlite_status']) or (isset($settings['mailerlite_status']) and !$settings['mailerlite_status'])) return false;

        $api_key = isset($settings['mailerlite_api_key']) ? $settings['mailerlite_api_key'] : '';
        $list_id = isset($settings['mailerlite_list_id']) ? $settings['mailerlite_list_id'] : '';

        // mailerlite credentials are required
        if(!trim($api_key) or !trim($list_id)) return false;

        // MEC User
        $u = $this->getUser();
        $booker = $u->booking($book_id);

        $url = 'https://api.mailerlite.com/api/v2/groups/'.$list_id.'/subscribers';

        $json = json_encode(array
        (
            'email'=>$booker->user_email,
            'name'=>$booker->first_name . ' ' .$booker->last_name,
        ));

        // Execute the Request and Return the Response Code
        return wp_remote_retrieve_response_code(wp_remote_post($url, array(
            'body' => $json,
            'timeout' => '10',
            'redirection' => '10',
            'headers' => array('Content-Type' => 'application/json', 'X-MailerLite-ApiKey' => $api_key),
        )));
    }

    /**
     * Add booker information to Active Campaign list
     * @param int $book_id
     * @return boolean
     */
    public function active_campaign_add_subscriber($book_id)
    {
        // Get MEC Options
        $settings = $this->get_settings();

        // Mailchim integration is disabled
        if(!isset($settings['active_campaign_status']) or (isset($settings['active_campaign_status']) and !$settings['active_campaign_status'])) return false;

        $api_url = isset($settings['active_campaign_api_url']) ? $settings['active_campaign_api_url'] : '';
        $api_key = isset($settings['active_campaign_api_key']) ? $settings['active_campaign_api_key'] : '';
        $list_id = isset($settings['active_campaign_list_id']) ? $settings['active_campaign_list_id'] : '';

        // Mailchim credentials are required
        if(!trim($api_url) or !trim($api_key)) return false;

        // MEC User
        $u = $this->getUser();
        $booker = $u->booking($book_id);

        $url = $api_url.'/api/3/contact/sync';

        $array_parameters = array(
            'email'=>$booker->user_email,
            'firstName'=>$booker->first_name,
            'lastName'=>$booker->last_name,
        );
        $array_parameters = apply_filters('mec_active_campaign_parameters', $array_parameters, $booker,$book_id);
        $json = json_encode(array
        (
            'contact' => $array_parameters,
        ));

        // Execute the Request and Return the Response Code
        $request = wp_remote_post( $url, array(
            'body' => $json,
            'timeout' => '10',
            'redirection' => '10',
            'headers' => array('Content-Type' => 'application/json', 'Api-Token' => $api_key),
        ) );

        if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
            error_log( print_r( $request, true ) );
        }
        $response = wp_remote_retrieve_body( $request );

        // Subscribe to list
        if (trim($list_id)) {
            $person = json_decode($response);
            $new_url = $api_url.'/api/3/contactLists';
            $new_json = json_encode(array
            (
                'contactList' => array(
                    'list'=>(int)$list_id,
                    'contact'=>(int)$person->contact->id,
                    'status'=>1,
                ),
            ));
            $new_request = wp_remote_post( $new_url, array(
                'body' => $new_json,
                'timeout' => '10',
                'redirection' => '10',
                'headers' => array('Content-Type' => 'application/json', 'Api-Token' => $api_key),
            ) );

            if ( is_wp_error( $new_request ) || wp_remote_retrieve_response_code( $new_request ) != 200 ) {
                error_log( print_r( $new_request, true ) );
            }

            $new_response = wp_remote_retrieve_body( $new_request );
        }
    }

    /**
     * Add booker information to Aweber list
     * @param int $book_id
     * @return boolean
     */
    public function aweber_add_subscriber($book_id)
    {
        // Aweber Plugin is not installed or it's not activated
        if(!class_exists('AWeberWebFormPluginNamespace\AWeberWebformPlugin')) return false;

        // Get MEC Options
        $settings = $this->get_settings();

        // AWeber integration is disabled
        if(!isset($settings['aweber_status']) or (isset($settings['aweber_status']) and !$settings['aweber_status'])) return false;

        $list_id = isset($settings['aweber_list_id']) ? preg_replace("/[^0-9]/", "", $settings['aweber_list_id']) : '';

        // AWeber credentials are required
        if(!trim($list_id)) return false;

        $aweber = new \AWeberWebFormPluginNamespace\AWeberWebformPlugin();

        // AWeber Authorization
        $aweber_options = get_option($aweber->adminOptionsName);
        if(!is_array($aweber_options)) $aweber_options = array();

        // AWeber Credentials are Required
        if(!isset($aweber_options['consumer_key']) or (isset($aweber_options['consumer_key']) and !trim($aweber_options['consumer_key']))) return false;
        if(!isset($aweber_options['consumer_secret']) or (isset($aweber_options['consumer_secret']) and !trim($aweber_options['consumer_secret']))) return false;
        if(!isset($aweber_options['access_key']) or (isset($aweber_options['access_key']) and !trim($aweber_options['access_key']))) return false;
        if(!isset($aweber_options['access_secret']) or (isset($aweber_options['access_secret']) and !trim($aweber_options['access_secret']))) return false;

        // MEC User
        $u = $this->getUser();
        $booker = $u->booking($book_id);
        $name = trim($booker->first_name.' '.$booker->last_name);

        return $aweber->create_subscriber($booker->user_email, NULL, $list_id, $name, 'a,b');
    }

    /**
     * Add booker information to Mailpoet list
     * @param int $book_id
     * @return boolean
     */
    public function mailpoet_add_subscriber($book_id)
    {
        // Mailpoet Plugin is not installed or it's not activated
        if(!class_exists(\MailPoet\API\API::class)) return false;

        // Get MEC Options
        $settings = $this->get_settings();

        // MailPoet integration is disabled
        if(!isset($settings['mailpoet_status']) or (isset($settings['mailpoet_status']) and !$settings['mailpoet_status'])) return false;

        // MailPoet API
        $mailpoet_api = \MailPoet\API\API::MP('v1');

        // List ID
        $list_ids = ((isset($settings['mailpoet_list_id']) and trim($settings['mailpoet_list_id'])) ? array($settings['mailpoet_list_id']) : NULL);

        // MEC User
        $u = $this->getUser();
        $booker = $u->booking($book_id);

        try
        {
            return $mailpoet_api->addSubscriber(array(
                'email' => $booker->user_email,
                'first_name' => $booker->first_name,
                'last_name' => $booker->last_name,
            ), $list_ids);
        }
        catch(Exception $e)
        {
            if($e->getCode() == 12 and $list_ids)
            {
                try
                {
                    $subscriber = $mailpoet_api->getSubscriber($booker->user_email);
                    return $mailpoet_api->subscribeToLists($subscriber['id'], $list_ids);
                }
                catch(Exception $e)
                {
                    return false;
                }
            }

            return false;
        }
    }

    /**
     * Add booker information to Sendfox list
     * @param int $book_id
     * @return boolean|array
     */
    public function sendfox_add_subscriber($book_id)
    {
        // Sendfox Plugin is not installed or it's not activated
        if(!function_exists('gb_sf4wp_add_contact')) return false;

        // Get MEC Options
        $settings = $this->get_settings();

        // Sendfox integration is disabled
        if(!isset($settings['sendfox_status']) or (isset($settings['sendfox_status']) and !$settings['sendfox_status'])) return false;

        // List ID
        $list_id = ((isset($settings['sendfox_list_id']) and trim($settings['sendfox_list_id'])) ? (int) $settings['sendfox_list_id'] : NULL);

        // MEC User
        $u = $this->getUser();
        $booker = $u->booking($book_id);

        return gb_sf4wp_add_contact(array(
            'email' => $booker->user_email,
            'first_name' => $booker->first_name,
            'last_name' => $booker->last_name,
            'lists' => array($list_id)
        ));
    }

    /**
     * Add booker information to constantcontact list
     * @param int $book_id
     * @return boolean|int
     */
    public function constantcontact_add_subscriber($book_id)
    {

        // Get MEC Options
        $settings = $this->get_settings();

        // constantcontact integration is disabled
        if(!isset($settings['constantcontact_status']) or (isset($settings['constantcontact_status']) and !$settings['constantcontact_status'])) return false;

        $api_key = isset($settings['constantcontact_api_key']) ? $settings['constantcontact_api_key'] : '';
        $access_token = isset($settings['constantcontact_access_token']) ? $settings['constantcontact_access_token'] : '';
        $list_id = isset($settings['constantcontact_list_id']) ? $settings['constantcontact_list_id'] : '';

        // constantcontact credentials are required
        if(!trim($api_key) or !trim($access_token) or !trim($list_id)) return false;

        // MEC User
        $u = $this->getUser();
        $booker = $u->booking($book_id);

        $url = 'https://api.constantcontact.com/v2/contacts?action_by=ACTION_BY_OWNER&api_key='.$api_key;

        $json = json_encode(array
        (
            'lists'=>array(json_encode(array('list' =>$list_id ))),
            'email_addresses'=>array(json_encode(array('email_address' =>$booker->user_email ))),
            'first_name'=>$booker->first_name ,
            'last_name'=>$booker->last_name,
        ));

        // Execute the Request and Return the Response Code
        return wp_remote_retrieve_response_code(wp_remote_post($url, array(
            'body' => $json,
            'timeout' => '10',
            'redirection' => '10',
            'headers' => array('Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $access_token),
        )));

    }

    /**
     * Returns Booking of a certain event at certain date
     * @param int $event_id
     * @param integer $timestamp
     * @param integer|string $limit
     * @param integer $user_id
     * @return array
     */
    public function get_bookings($event_id, $timestamp, $limit = '-1', $user_id = NULL)
    {
        $booking_options = get_post_meta($event_id, 'mec_booking', true);
        if(!is_array($booking_options)) $booking_options = array();

        $book_all_occurrences = isset($booking_options['bookings_all_occurrences']) ? (int) $booking_options['bookings_all_occurrences'] : 0;

        if(!$book_all_occurrences) $date_query = " AND `timestamp`=".$timestamp;
        else $date_query = " AND `timestamp`<=".$timestamp;

        if($user_id) $user_query = " AND `user_id`=".$user_id;
        else $user_query = "";

        if(is_numeric($limit) and $limit > 0) $limit_query = " LIMIT ".$limit;
        else $limit_query = "";

        // Database
        $db = $this->getDB();

        $records = $db->select("SELECT `id`,`booking_id`,`timestamp` FROM `#__mec_bookings` WHERE `event_id`=".$event_id." AND `status` IN ('publish', 'future') AND `confirmed`='1' AND `verified`='1'".$date_query.$user_query.$limit_query);

        $results = array();
        foreach($records as $record)
        {
            $post = get_post($record->booking_id);
            $post->mec_timestamp = $record->timestamp;

            $results[] = $post;
        }

        return $results;
    }

    public function get_bookings_for_occurrence($timestamps, $args = array())
    {
        $limit = ((isset($args['limit']) and is_numeric($args['limit'])) ? $args['limit'] : -1);
        $status = ((isset($args['status']) and is_array($args['status'])) ? $args['status'] : array());
        $confirmed = ((isset($args['confirmed']) and is_numeric($args['confirmed'])) ? $args['confirmed'] : 1);
        $verified = ((isset($args['verified']) and is_numeric($args['verified'])) ? $args['verified'] : 1);

        $start = $timestamps[0];
        $end = (isset($timestamps[1]) ? $timestamps[1] : NULL);

        // Database
        $db = $this->getDB();

        // Query
        $query = "SELECT `id`,`booking_id`,`timestamp` FROM `#__mec_bookings` WHERE `confirmed`='".esc_sql($confirmed)."' AND `verified`='".esc_sql($verified)."'";

        // Status
        if(count($status))
        {
            $status_str = '';
            foreach($status as $s) $status_str .= "'".$s."', ";

            $query .= " AND `status` IN (".trim($status_str, ', ').")";
        }

        // Times
        if($start and $end)
        {
            $query .= " AND `timestamp`>='".esc_sql($start)."' AND `timestamp`<'".esc_sql($end)."'";
        }
        else $query .= " AND `timestamp`='".esc_sql($start)."'";

        // Order
        $query .= " ORDER BY `id` ASC";

        // Limit
        if($limit > 0) $query .= " LIMIT ".$limit;

        $records = $db->select($query);

        $results = array();
        foreach($records as $record)
        {
            $post = get_post($record->booking_id);
            $post->mec_timestamp = $record->timestamp;

            $results[] = $post;
        }

        return $results;
    }

    /**
     * Check whether to show event note or not
     * @param string $status
     * @return boolean
     */
    public function is_note_visible($status)
    {
        // MEC Settings
        $settings = $this->get_settings();

        // FES Note is not enabled
        if(!isset($settings['fes_note']) or (isset($settings['fes_note']) and !$settings['fes_note'])) return false;

        // Return visibility status by post status and visibility method
        return (isset($settings['fes_note_visibility']) ? ($settings['fes_note_visibility'] == 'always' ? true : $status != 'publish') : true);
    }

    /**
     * Get Next event based on datetime of current event
     * @param array $atts
     * @return object
     */
    public function get_next_event($atts = array())
    {
        MEC::import('app.skins.list', true);

        // Get list skin
        $list = new MEC_skin_list();

        // Initialize the skin
        $list->initialize($atts);

        // Fetch the events
        $list->fetch();

        $events = $list->events;
        $key = key($events);

        return (isset($events[$key][0]) ? $events[$key][0] : (new stdClass()));
    }

    /**
     * For getting event end date based on occurrence date
     * @param int $event_id
     * @param string $occurrence
     * @return string
     */
    public function get_end_date_by_occurrence($event_id, $occurrence)
    {
        $event_date = get_post_meta($event_id, 'mec_date', true);

        $start_date = isset($event_date['start']) ? $event_date['start'] : array();
        $end_date = isset($event_date['end']) ? $event_date['end'] : array();

        $event_period = $this->date_diff($start_date['date'], $end_date['date']);
        $event_period_days = $event_period ? $event_period->days : 0;

        // Single Day Event
        if(!$event_period_days) return $occurrence;

        return date('Y-m-d', strtotime('+'.$event_period_days.' days', strtotime($occurrence)));
    }

    /**
     * Add MEC Event CPT to Tags Archive Page
     * @param object $query
     */
    public function add_events_to_tags_archive($query)
    {
        if($query->is_tag() and $query->is_main_query() and !is_admin())
        {
            $pt = $this->get_main_post_type();
            $query->set('post_type', array('post', $pt));
        }
    }

    /**
     * Get Post ID by meta value and meta key
     * @param string $meta_key
     * @param string $meta_value
     * @return string
     */
    public function get_post_id_by_meta($meta_key, $meta_value)
    {
        $db = $this->getDB();
        return $db->select("SELECT `post_id` FROM `#__postmeta` WHERE `meta_value`='$meta_value' AND `meta_key`='$meta_key'", 'loadResult');
    }

    /**
     * Set Featured Image for a Post
     * @param string $image_url
     * @param int $post_id
     * @return bool|int
     */
    public function set_featured_image($image_url, $post_id)
    {
        $attach_id = $this->get_attach_id($image_url);
        if(!$attach_id)
        {
            $upload_dir = wp_upload_dir();
            $filename = basename($image_url);

            if(wp_mkdir_p($upload_dir['path'])) $file = $upload_dir['path'].'/'.$filename;
            else $file = $upload_dir['basedir'].'/'.$filename;

            if(!file_exists($file))
            {
                $image_data = $this->get_web_page($image_url);
                file_put_contents($file, $image_data);
            }

            $wp_filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type'=>$wp_filetype['type'],
                'post_title'=>sanitize_file_name($filename),
                'post_content'=>'',
                'post_status'=>'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $file, $post_id);
            require_once ABSPATH.'wp-admin/includes/image.php';

            $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            wp_update_attachment_metadata($attach_id, $attach_data);
        }

        return set_post_thumbnail($post_id, $attach_id);
    }

    /**
     * Get Attachment ID by Image URL
     * @param string $image_url
     * @return int
     */
    public function get_attach_id($image_url)
    {
        $db = $this->getDB();
        return $db->select("SELECT `ID` FROM `#__posts` WHERE `guid`='$image_url'", 'loadResult');
    }

    /**
     * Get Image Type by Buffer. Used in Facebook Importer
     * @param string $buffer
     * @return string
     */
    public function get_image_type_by_buffer($buffer)
    {
        $types = array('jpeg'=>"\xFF\xD8\xFF", 'gif'=>'GIF', 'png'=>"\x89\x50\x4e\x47\x0d\x0a", 'bmp'=>'BM', 'psd'=>'8BPS', 'swf'=>'FWS');
        $found = 'other';

        foreach($types as $type=>$header)
        {
            if(strpos($buffer, $header) === 0)
            {
                $found = $type;
                break;
            }
        }

        return $found;
    }

    /**
     * Load Google Maps assets
     * @var $define_settings
     * @return bool
     */
    public function load_map_assets($define_settings = null)
    {
        if(!$this->getPRO()) return false;

        // MEC Settings
        $settings = $this->get_settings();

        $assets = array('js'=>array(), 'css'=>array());

        $local = $this->get_current_language();
        $ex = explode('_',$local);

        $language = ((isset($ex[0]) and trim($ex[0])) ? $ex[0] : 'en');
        $region = ((isset($ex[1]) and trim($ex[1])) ? $ex[1] : 'US');

        $gm_include = apply_filters('mec_gm_include', true);
        if($gm_include) $assets['js']['googlemap'] = '//maps.googleapis.com/maps/api/js?libraries=places'.((isset($settings['google_maps_api_key']) and trim($settings['google_maps_api_key']) != '') ? '&key='.$settings['google_maps_api_key'] : '').'&language='.$language.'&region='.$region;

        $assets['js']['mec-richmarker-script'] = $this->asset('packages/richmarker/richmarker.min.js'); // Google Maps Rich Marker
        $assets['js']['mec-clustering-script'] = $this->asset('packages/clusterer/markerclusterer.min.js'); // Google Maps Clustering
        $assets['js']['mec-googlemap-script'] = $this->asset('js/googlemap.js'); // Google Maps Javascript API

        // Apply Filters
        $assets = apply_filters('mec_map_assets_include', $assets, $this, $define_settings);

        if(count($assets['js']) > 0) foreach($assets['js'] as $key => $link) wp_enqueue_script($key, $link, array('jquery'), $this->get_version());
        if(count($assets['css']) > 0) foreach($assets['css'] as $key => $link) wp_enqueue_style($key, $link, array(), $this->get_version());
    }

    /**
     * Load Owl Carousel assets
     */
    public function load_owl_assets()
    {
        // Include MEC frontend CSS files
        wp_enqueue_style('mec-owl-carousel-style', $this->asset('packages/owl-carousel/owl.carousel.min.css'));
        wp_enqueue_style('mec-owl-carousel-theme-style', $this->asset('packages/owl-carousel/owl.theme.min.css'));
    }

    /**
     * Load Isotope assets
     */
    public function load_isotope_assets()
    {
        // Isotope JS file
        wp_enqueue_script('mec-isotope-script', $this->asset('js/isotope.pkgd.min.js'), array(), $this->get_version(), true);
        wp_enqueue_script('mec-imagesload-script', $this->asset('js/imagesload.js'), array(), $this->get_version(), true);
    }

    /**
     * Load Time Picker assets
     */
    public function load_time_picker_assets()
    {
        // Include CSS
        wp_enqueue_style('mec-time-picker', $this->asset('packages/timepicker/jquery.timepicker.min.css'));

        // Include JS
        wp_enqueue_script('mec-time-picker', $this->asset('packages/timepicker/jquery.timepicker.min.js'));
    }

    function get_client_ip()
    {
        if(isset($_SERVER['HTTP_CLIENT_IP'])) $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        elseif(isset($_SERVER['HTTP_X_FORWARDED'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        elseif(isset($_SERVER['HTTP_FORWARDED'])) $ipaddress = $_SERVER['HTTP_FORWARDED'];
        elseif(isset($_SERVER['REMOTE_ADDR'])) $ipaddress = $_SERVER['REMOTE_ADDR'];
        else $ipaddress = 'UNKNOWN';

        $ips = explode(',', $ipaddress);
        if(count($ips) > 1) $ipaddress = $ips[0];

        return $ipaddress;
    }

    public function get_timezone_by_ip()
    {
        // Client IP
        $ip = $this->get_client_ip();

        $cache_key = 'mec_visitor_timezone_'.$ip;
        $cache = $this->getCache();

        // Get From Cache
        if($cache->has($cache_key)) return $cache->get($cache_key);

        // First Provider
        $JSON = $this->get_web_page('http://ip-api.com/json/'.$ip, 3);
        $data = json_decode($JSON, true);

        // Second Provider
        if(!trim($JSON) or (is_array($data) and !isset($data['timezone'])))
        {
            $JSON = $this->get_web_page('https://ipapi.co/'.$ip.'/json/', 3);
            $data = json_decode($JSON, true);
        }

        // Second provider returns X instead of false in case of error!
        $timezone = (isset($data['timezone']) and strtolower($data['timezone']) != 'x') ? $data['timezone'] : false;

        // Add to Cache
        $cache->set($cache_key, $timezone);

        return $timezone;
    }

    public function is_ajax()
    {
        return (defined('DOING_AJAX') && DOING_AJAX);
    }

    public function load_sed_assets()
    {
        // Load Map assets
        $this->load_map_assets();

        // Include FlipCount library
        wp_enqueue_script('mec-flipcount-script', $this->asset('js/flipcount.js'));
    }

    public function is_sold($event, $date = NULL)
    {
        if(is_object($event))
        {
            $event_id = $event->data->ID;
            $tickets = (isset($event->data->tickets) and is_array($event->data->tickets)) ? $event->data->tickets : array();

            $timestamp = (trim($date) ? $date : ((isset($event->date['start']) and isset($event->date['start']['timestamp'])) ? $event->date['start']['timestamp'] : 0));
        }
        else
        {
            $event_id = $event;
            $tickets = get_post_meta($event_id, 'mec_tickets', true);
            if(!is_array($tickets)) $tickets = array();

            $timestamp = (is_numeric($date) ? $date : strtotime($date));
        }

        // No Tickets
        if(!count($tickets) or !$timestamp) return false;

        $book = $this->getBook();
        $availability = $book->get_tickets_availability($event_id, $timestamp);

        if(is_array($availability) and count($availability))
        {
            $remained_tickets = 0;
            foreach($availability as $ticket_id => $remained)
            {
                if(is_numeric($ticket_id) and $remained >= 0) $remained_tickets += $remained;
                if(is_numeric($ticket_id) and $remained == -1)
                {
                    $remained_tickets = -1;
                    break;
                }
            }

            // Check For Return SoldOut Label Exist.
            if($remained_tickets === 0) return true;
        }

        return false;
    }

    public function get_date_periods($date_start, $date_end, $type = 'daily')
    {
        $periods = array();

        $time_start = strtotime($date_start);
        $time_end = strtotime($date_end);

        if($type == 'daily')
        {
            while($time_start < $time_end)
            {
                $periods[] = array('start'=>date("Y-m-d H:i:s", $time_start), 'end'=>date("Y-m-d H:i:s", ($time_start+86399)), 'label'=>date("Y-m-d", $time_start));
                $time_start += 86400;
            }
        }
        // @todo
        elseif($type == 'weekly')
        {
        }
        elseif($type == 'monthly')
        {
            $start_year = date('Y', $time_start);
            $start_month = date('m', $time_start);
            $start_id = (int) $start_year.$start_month;

            $end_year = date('Y', $time_end);
            $end_month = date('m', $time_end);
            $end_id = (int) $end_year.$end_month;

            while($start_id <= $end_id)
            {
                $periods[] = array('start'=>$start_year."-".$start_month."-01 00:00:00", 'end'=>$start_year."-".$start_month."-".date('t', strtotime($start_year."-".$start_month."-01 00:00:00"))." 23:59:59", 'label'=>date('Y F', strtotime($start_year."-".$start_month."-01 00:00:00")));

                if($start_month == '12')
                {
                    $start_month = '01';
                    $start_year++;
                }
                else
                {
                    $start_month = (int) $start_month+1;
                    if(strlen($start_month) == 1) $start_month = '0'.$start_month;
                }

                $start_id = (int) $start_year.$start_month;
            }
        }
        elseif($type == 'yearly')
        {
            $start_year = date('Y', $time_start);
            $end_year = date('Y', $time_end);

            while($start_year <= $end_year)
            {
                $periods[] = array('start'=>$start_year."-01-01 00:00:00", 'end'=>$start_year."-12-31 23:59:59", 'label'=>$start_year);
                $start_year++;
            }
        }

        return $periods;
    }

    public function get_messages()
    {
        if($this->getPRO())
        {
            $messages = array(
                'taxonomies'=>array(
                    'category'=>array('name'=>__('Taxonomies', 'modern-events-calendar-lite')),
                    'messages'=>array(
                        'taxonomy_categories'=>array('label'=>__('Category Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Categories', 'modern-events-calendar-lite')),
                        'taxonomy_category'=>array('label'=>__('Category Singular Label', 'modern-events-calendar-lite'), 'default'=>__('Category', 'modern-events-calendar-lite')),
                        'taxonomy_labels'=>array('label'=>__('Label Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Labels', 'modern-events-calendar-lite')),
                        'taxonomy_label'=>array('label'=>__('Label Singular Label', 'modern-events-calendar-lite'), 'default'=>__('label', 'modern-events-calendar-lite')),
                        'taxonomy_locations'=>array('label'=>__('Location Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Locations', 'modern-events-calendar-lite')),
                        'taxonomy_location'=>array('label'=>__('Location Singular Label', 'modern-events-calendar-lite'), 'default'=>__('Location', 'modern-events-calendar-lite')),
                        'taxonomy_organizers'=>array('label'=>__('Organizer Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Organizers', 'modern-events-calendar-lite')),
                        'taxonomy_organizer'=>array('label'=>__('Organizer Singular Label', 'modern-events-calendar-lite'), 'default'=>__('Organizer', 'modern-events-calendar-lite')),
                        'taxonomy_speakers'=>array('label'=>__('Speaker Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Speakers', 'modern-events-calendar-lite')),
                        'taxonomy_speaker'=>array('label'=>__('Speaker Singular Label', 'modern-events-calendar-lite'), 'default'=>__('Speaker', 'modern-events-calendar-lite')),
                    )
                ),
                'weekdays'=>array(
                    'category'=>array('name'=>__('Weekdays', 'modern-events-calendar-lite')),
                    'messages'=>array(
                        'weekdays_su'=>array('label'=>__('Sunday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('SU', 'modern-events-calendar-lite')),
                        'weekdays_mo'=>array('label'=>__('Monday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('MO', 'modern-events-calendar-lite')),
                        'weekdays_tu'=>array('label'=>__('Tuesday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('TU', 'modern-events-calendar-lite')),
                        'weekdays_we'=>array('label'=>__('Wednesday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('WE', 'modern-events-calendar-lite')),
                        'weekdays_th'=>array('label'=>__('Thursday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('TH', 'modern-events-calendar-lite')),
                        'weekdays_fr'=>array('label'=>__('Friday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('FR', 'modern-events-calendar-lite')),
                        'weekdays_sa'=>array('label'=>__('Saturday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('SA', 'modern-events-calendar-lite')),
                    )
                ),
                'booking'=>array(
                    'category'=>array('name'=>__('Booking', 'modern-events-calendar-lite')),
                    'messages'=>array(
                        'booking'=>array('label'=>__('Booking (Singular)', 'modern-events-calendar-lite'), 'default'=>__('Booking', 'modern-events-calendar-lite')),
                        'bookings'=>array('label'=>__('Bookings (Plural)', 'modern-events-calendar-lite'), 'default'=>__('Bookings', 'modern-events-calendar-lite')),
                        'book_success_message'=>array('label'=>__('Booking Success Message', 'modern-events-calendar-lite'), 'default'=>__('Thank you for booking. Your tickets are booked, booking verification might be needed, please check your email.', 'modern-events-calendar-lite')),
                        'booking_restriction_message1'=>array('label'=>__('Booking Restriction Message 1', 'modern-events-calendar-lite'), 'default'=>__('You selected %s tickets to book but maximum number of tikets per user is %s tickets.', 'modern-events-calendar-lite')),
                        'booking_restriction_message2'=>array('label'=>__('Booking Restriction Message 2', 'modern-events-calendar-lite'), 'default'=>__('You booked %s tickets till now but maximum number of tickets per user is %s tickets.', 'modern-events-calendar-lite')),
                        'booking_restriction_message3'=>array('label'=>__('Booking IP Restriction Message', 'modern-events-calendar-lite'), 'default'=>__('Maximum allowed number of tickets that you can book is %s.', 'modern-events-calendar-lite')),
                        'booking_button'=>array('label'=>__('Booking Button', 'modern-events-calendar-lite'), 'default'=>__('Book Now', 'modern-events-calendar-lite')),
                        'ticket'=>array('label'=>__('Ticket (Singular)', 'modern-events-calendar-lite'), 'default'=>__('Ticket', 'modern-events-calendar-lite')),
                        'tickets'=>array('label'=>__('Tickets (Plural)', 'modern-events-calendar-lite'), 'default'=>__('Tickets', 'modern-events-calendar-lite')),
                    )
                ),
                'others'=>array(
                    'category'=>array('name'=>__('Others', 'modern-events-calendar-lite')),
                    'messages'=>array(
                        'register_button'=>array('label'=>__('Register Button', 'modern-events-calendar-lite'), 'default'=>__('REGISTER', 'modern-events-calendar-lite')),
                        'view_detail'=>array('label'=>__('View Detail Button', 'modern-events-calendar-lite'), 'default'=>__('View Detail', 'modern-events-calendar-lite')),
                        'event_detail'=>array('label'=>__('Event Detail Button', 'modern-events-calendar-lite'), 'default'=>__('Event Detail', 'modern-events-calendar-lite')),
                        'read_more_link'=>array('label'=>__('Event Link', 'modern-events-calendar-lite'), 'default'=>__('Event Link', 'modern-events-calendar-lite')),
                        'more_info_link'=>array('label'=>__('More Info Link', 'modern-events-calendar-lite'), 'default'=>__('More Info', 'modern-events-calendar-lite')),
                        'event_cost'=>array('label'=>__('Event Cost', 'modern-events-calendar-lite'), 'default'=>__('Event Cost', 'modern-events-calendar-lite')),
                        'cost'=>array('label'=>__('Cost', 'modern-events-calendar-lite'), 'default'=>__('Cost', 'modern-events-calendar-lite')),
                        'other_organizers'=>array('label'=>__('Other Organizers', 'modern-events-calendar-lite'), 'default'=>__('Other Organizers', 'modern-events-calendar-lite')),
                        'other_locations'=>array('label'=>__('Other Locations', 'modern-events-calendar-lite'), 'default'=>__('Other Locations', 'modern-events-calendar-lite')),
                        'all_day'=>array('label'=>__('All Day', 'modern-events-calendar-lite'), 'default'=>__('All Day', 'modern-events-calendar-lite')),
                    )
                ),
            );
        }
        else
        {
            $messages = array(
                'taxonomies'=>array(
                    'category'=>array('name'=>__('Taxonomies', 'modern-events-calendar-lite')),
                    'messages'=>array(
                        'taxonomy_categories'=>array('label'=>__('Category Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Categories', 'modern-events-calendar-lite')),
                        'taxonomy_category'=>array('label'=>__('Category Singular Label', 'modern-events-calendar-lite'), 'default'=>__('Category', 'modern-events-calendar-lite')),
                        'taxonomy_labels'=>array('label'=>__('Label Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Labels', 'modern-events-calendar-lite')),
                        'taxonomy_label'=>array('label'=>__('Label Singular Label', 'modern-events-calendar-lite'), 'default'=>__('label', 'modern-events-calendar-lite')),
                        'taxonomy_locations'=>array('label'=>__('Location Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Locations', 'modern-events-calendar-lite')),
                        'taxonomy_location'=>array('label'=>__('Location Singular Label', 'modern-events-calendar-lite'), 'default'=>__('Location', 'modern-events-calendar-lite')),
                        'taxonomy_organizers'=>array('label'=>__('Organizer Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Organizers', 'modern-events-calendar-lite')),
                        'taxonomy_organizer'=>array('label'=>__('Organizer Singular Label', 'modern-events-calendar-lite'), 'default'=>__('Organizer', 'modern-events-calendar-lite')),
                        'taxonomy_speakers'=>array('label'=>__('Speaker Plural Label', 'modern-events-calendar-lite'), 'default'=>__('Speakers', 'modern-events-calendar-lite')),
                        'taxonomy_speaker'=>array('label'=>__('Speaker Singular Label', 'modern-events-calendar-lite'), 'default'=>__('Speaker', 'modern-events-calendar-lite')),
                    )
                ),
                'weekdays'=>array(
                    'category'=>array('name'=>__('Weekdays', 'modern-events-calendar-lite')),
                    'messages'=>array(
                        'weekdays_su'=>array('label'=>__('Sunday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('SU', 'modern-events-calendar-lite')),
                        'weekdays_mo'=>array('label'=>__('Monday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('MO', 'modern-events-calendar-lite')),
                        'weekdays_tu'=>array('label'=>__('Tuesday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('TU', 'modern-events-calendar-lite')),
                        'weekdays_we'=>array('label'=>__('Wednesday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('WE', 'modern-events-calendar-lite')),
                        'weekdays_th'=>array('label'=>__('Thursday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('TH', 'modern-events-calendar-lite')),
                        'weekdays_fr'=>array('label'=>__('Friday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('FR', 'modern-events-calendar-lite')),
                        'weekdays_sa'=>array('label'=>__('Saturday abbreviation', 'modern-events-calendar-lite'), 'default'=>__('SA', 'modern-events-calendar-lite')),
                    )
                ),
                'others'=>array(
                    'category'=>array('name'=>__('Others', 'modern-events-calendar-lite')),
                    'messages'=>array(
                        'register_button'=>array('label'=>__('Register Button', 'modern-events-calendar-lite'), 'default'=>__('REGISTER', 'modern-events-calendar-lite')),
                        'view_detail'=>array('label'=>__('View Detail Button', 'modern-events-calendar-lite'), 'default'=>__('View Detail', 'modern-events-calendar-lite')),
                        'event_detail'=>array('label'=>__('Event Detail Button', 'modern-events-calendar-lite'), 'default'=>__('Event Detail', 'modern-events-calendar-lite')),
                        'read_more_link'=>array('label'=>__('Event Link', 'modern-events-calendar-lite'), 'default'=>__('Event Link', 'modern-events-calendar-lite')),
                        'more_info_link'=>array('label'=>__('More Info Link', 'modern-events-calendar-lite'), 'default'=>__('More Info', 'modern-events-calendar-lite')),
                        'event_cost'=>array('label'=>__('Event Cost', 'modern-events-calendar-lite'), 'default'=>__('Event Cost', 'modern-events-calendar-lite')),
                        'cost'=>array('label'=>__('Cost', 'modern-events-calendar-lite'), 'default'=>__('Cost', 'modern-events-calendar-lite')),
                        'other_organizers'=>array('label'=>__('Other Organizers', 'modern-events-calendar-lite'), 'default'=>__('Other Organizers', 'modern-events-calendar-lite')),
                        'other_locations'=>array('label'=>__('Other Locations', 'modern-events-calendar-lite'), 'default'=>__('Other Locations', 'modern-events-calendar-lite')),
                        'all_day'=>array('label'=>__('All Day', 'modern-events-calendar-lite'), 'default'=>__('All Day', 'modern-events-calendar-lite')),
                    )
                ),
            );
        }

        return apply_filters('mec_messages', $messages);
    }

    /**
     * For showing dynamic messages based on their default value and the inserted value in backend (if any)
     * @param $message_key string
     * @param $default string
     * @return string
     */
    public function m($message_key, $default)
    {
        $message_values = $this->get_messages_options();

        // Message is not set from backend
        if(!isset($message_values[$message_key]) or (isset($message_values[$message_key]) and !trim($message_values[$message_key]))) return $default;

        // Return the dynamic message inserted in backend
        return $message_values[$message_key];
    }

    /**
     * Get Weather from the data provider
     * @param $apikey
     * @param $lat
     * @param $lng
     * @param $datetime
     * @return bool|array
     */
    public function get_weather_darksky($apikey, $lat, $lng, $datetime)
    {
        $locale = substr(get_locale(), 0, 2);

        // Set the language to English if it's not included in available languages
        if(!in_array($locale, array
        (
            'ar', 'az', 'be', 'bg', 'bs', 'ca', 'cs', 'da', 'de', 'el', 'en', 'es', 'et',
            'fi', 'fr', 'hr', 'hu', 'id', 'is', 'it', 'ja', 'ka', 'ko', 'kw', 'nb', 'nl',
            'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'tet', 'tr', 'uk', 'x-pig-latin',
            'zh', 'zh-tw'
        ))) $locale = 'en';

        // Dark Sky Provider
        $JSON = $this->get_web_page('https://api.darksky.net/forecast/'.$apikey.'/'.$lat.','.$lng.','.strtotime($datetime).'?exclude=minutely,hourly,daily,alerts&units=ca&lang='.$locale);
        $data = json_decode($JSON, true);

        return (isset($data['currently']) ? $data['currently'] : false);
    }

    /**
     * Get Weather from the data provider
     * @param $apikey
     * @param $lat
     * @param $lng
     * @param $datetime
     * @return bool|array
     */
    public function get_weather_wa($apikey, $lat, $lng, $datetime)
    {
        $locale = substr(get_locale(), 0, 2);

        // Set the language to English if it's not included in available languages
        if(!in_array($locale, array
        (
            'ar', 'bn', 'bg', 'zh', 'zh_tw', 'cs', 'da', 'nl', 'fi', 'fr', 'de', 'el',
            'hi', 'hu', 'it', 'ja', 'jv', 'ko', 'zh_cmn', 'mr', 'pl', 'pt', 'pa', 'ro', 'ru',
            'si', 'si', 'sk', 'es', 'sv', 'ta', 'te', 'tr', 'uk', 'ur', 'vi', 'zh_wuu', 'zh_hsn',
            'zh_yue', 'zu'
        ))) $locale = 'en';

        // Dark Sky Provider
        $JSON = $this->get_web_page('https://api.weatherapi.com/v1/current.json?key='.$apikey.'&q='.$lat.','.$lng.'&lang='.$locale);
        $data = json_decode($JSON, true);

        return (isset($data['current']) ? $data['current'] : false);
    }

    /**
     * Convert weather unit
     * @author Webnus <info@webnus.biz>
     * @param $value
     * @param $mode
     * @return string|boolean
     */
    function weather_unit_convert($value, $mode)
    {
        if(func_num_args() < 2) return false;
        $mode = strtoupper($mode);

        if($mode == 'F_TO_C') return (round(((floatval($value) -32) *5 /9)));
        else if($mode == 'C_TO_F') return (round(((1.8 * floatval($value)) +32)));
        else if($mode == 'M_TO_KM') return(round(1.609344 * floatval($value)));
        else if($mode == 'KM_TO_M') return(round(0.6214 * floatval($value)));
        return false;
    }

    /**
     * Get Integrated plugins to import events
     * @return array
     */
    public function get_integrated_plugins_for_import()
    {
        return array(
            'eventon' => __('EventON', 'modern-events-calendar-lite'),
            'the-events-calendar' => __('The Events Calendar', 'modern-events-calendar-lite'),
            'weekly-class' => __('Events Schedule WP Plugin', 'modern-events-calendar-lite'),
            'calendarize-it' => __('Calendarize It', 'modern-events-calendar-lite'),
            'event-espresso' => __('Event Espresso', 'modern-events-calendar-lite'),
            'events-manager-recurring' => __('Events Manager (Recurring)', 'modern-events-calendar-lite'),
            'events-manager-single' => __('Events Manager (Single)', 'modern-events-calendar-lite'),
            'wp-event-manager' => __('WP Event Manager', 'modern-events-calendar-lite'),
        );
    }

    public function get_original_event($event_id)
    {
        // If WPML Plugin is installed and activated
        if(class_exists('SitePress'))
        {
            $trid = apply_filters('wpml_element_trid', NULL, $event_id, 'post_mec-events');
            $translations = apply_filters('wpml_get_element_translations', NULL, $trid, 'post_mec-events');

            if(!is_array($translations) or (is_array($translations) and !count($translations))) return $event_id;

            $original_id = $event_id;
            foreach($translations as $translation)
            {
                if(isset($translation->original) and $translation->original)
                {
                    $original_id = $translation->element_id;
                    break;
                }
            }

            return $original_id;
        }
        // Poly Lang is installed and activated
        elseif(function_exists('pll_default_language'))
        {
            $def = pll_default_language();

            $translations = pll_get_post_translations($event_id);
            if(!is_array($translations) or (is_array($translations) and !count($translations))) return $event_id;

            if(isset($translations[$def]) and is_numeric($translations[$def])) return $translations[$def];
        }
        else return $event_id;
    }

    public function is_multilingual()
    {
        $multilingual = false;

        // WPML
        if(class_exists('SitePress')) $multilingual = true;

        // Polylang
        if(function_exists('pll_default_language')) $multilingual = true;

        return $multilingual;
    }

    public function get_current_locale()
    {
        return get_locale();
    }

    public function get_backend_active_locale()
    {
        // WPML
        if(class_exists('SitePress'))
        {
            $languages = apply_filters('wpml_active_languages', array());
            if(is_array($languages) and count($languages))
            {
                foreach($languages as $language)
                {
                    if(isset($language['active']) and $language['active']) return $language['default_locale'];
                }
            }
        }

        // Polylang
        if(function_exists('pll_default_language'))
        {
            global $polylang;
            return $polylang->pref_lang->locale;
        }

        return $this->get_current_locale();

    }

    /**
     * To check is a date is valid or not
     * @param string $date
     * @param string $format
     * @return bool
     */
    public function validate_date($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function parse_ics($feed)
    {
        try {
            return new ICal($feed, array(
                'defaultSpan'                 => 2,     // Default value
                'defaultTimeZone'             => 'UTC',
                'defaultWeekStart'            => 'MO',  // Default value
                'disableCharacterReplacement' => false, // Default value
                'skipRecurrence'              => true, // Default value
                'useTimeZoneWithRRules'       => false, // Default value
            ));
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    public function get_pro_link()
    {
        $link = 'https://webnus.net/mec-purchase/?ref=17/';
        return apply_filters('MEC_upgrade_link', $link);
    }

    /**
     * Get Label for booking confirmation
     * @author Webnus <info@webnus.biz>
     * @param int $confirmed
     * @return string
     */
    public function get_confirmation_label($confirmed = 1)
    {
        if($confirmed == '1') $label = __('Confirmed', 'modern-events-calendar-lite');
        elseif($confirmed == '-1') $label = __('Rejected', 'modern-events-calendar-lite');
        else $label = __('Pending', 'modern-events-calendar-lite');

        return $label;
    }

    /**
     * Get Label for events status
     * @author Webnus <info@webnus.biz>
     * @param string $label
     * @param boolean $return_class
     * @return string|array
     */
    public function get_event_label_status($label = 'empty', $return_class = true)
    {
        if(!trim($label)) $label = 'empty';
        switch($label)
        {
            case 'publish':
                $label = __('Confirmed', 'modern-events-calendar-lite');
                $status_class = 'mec-book-confirmed';
                break;
            case 'pending':
                $label = __('Pending', 'modern-events-calendar-lite');
                $status_class = 'mec-book-pending';
                break;
            case 'trash':
                $label = __('Rejected', 'modern-events-calendar-lite');
                $status_class = 'mec-book-pending';
                break;
            default:
                $label = __(ucwords($label), 'modern-events-calendar-lite');
                $status_class = 'mec-book-other';
                break;
        }

        return !$return_class ? $label : array('label' => $label, 'status_class' => $status_class);
    }

    /**
     * Get Label for booking verification
     * @author Webnus <info@webnus.biz>
     * @param int $verified
     * @return string
     */
    public function get_verification_label($verified = 1)
    {
        if($verified == '1') $label = __('Verified', 'modern-events-calendar-lite');
        elseif($verified == '-1') $label = __('Canceled', 'modern-events-calendar-lite');
        else $label = __('Waiting', 'modern-events-calendar-lite');

        return $label;
    }

    /**
     * Added Block Editor Custome Category
     * @author Webnus <info@webnus.biz>
     * @param array $categories
     * @return array
     */
    public function add_custom_block_cateogry($categories)
    {
        $categories = array_merge(array(array('slug' => 'mec.block.category', 'title' => __('M.E. Calender', 'modern-events-calendar-lite'), 'icon' => 'calendar-alt')), $categories);
        return $categories;
    }

    /**
	 * Advanced Repeating MEC Active
	 * @author Webnus <info@webnus.biz>
	 * @param array $days
	 * @param string $item
	 */
	public function mec_active($days = array(), $item = '')
	{
		if(is_array($days) and in_array($item, $days)) echo 'mec-active';
    }

    /**
     * Advanced repeat sorting by start of week day number
     * @author Webnus <info@webnus.biz>
     * @param int $start_of_week
     * @param $day
     * @return string|boolean
     */
    public function advanced_repeating_sort_day($start_of_week = 1, $day = 1)
    {
        if(func_num_args() < 2) return false;

        $start_of_week = intval($start_of_week);
        $day = intval($day) == 0 ? intval($day) : intval($day) - 1;

        // KEEP IT FOR TRANSLATORS
        array(__('Sun', 'modern-events-calendar-lite'), __('Mon', 'modern-events-calendar-lite'), __('Tue', 'modern-events-calendar-lite'), __('Wed', 'modern-events-calendar-lite'), __('Thu', 'modern-events-calendar-lite'), __('Fri', 'modern-events-calendar-lite'), __('Sat', 'modern-events-calendar-lite'));

        // DO NOT MAKE THEM TRANSLATE-ABLE
        $days = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

        $s1 = array_splice($days, $start_of_week, count($days));
        $s2 = array_splice($days, 0, $start_of_week);
        $merge = array_merge($s1, $s2);

        return $merge[$day];
    }

    public function get_ical_rrules($event, $only_rrule = false)
    {
        if(is_numeric($event))
        {
            $render = $this->getRender();
            $event = $render->data($event);
        }

        $recurrence = array();
        if(isset($event->mec->repeat) and $event->mec->repeat)
        {
            $finish_time = $event->time['end'];
            $finish_time = str_replace(array('h:', 'H:', 'H'), 'h', $finish_time);
            $finish_time = str_replace(array('h ', 'h'), ':', $finish_time);

            $finish = ($event->mec->end != '0000-00-00' ? date('Ymd\THis\Z', strtotime($event->mec->end.' '.$finish_time)) : '');
            $freq = '';
            $interval = '1';
            $bysetpos = '';
            $byday = '';
            $wkst = '';

            $repeat_type = $event->meta['mec_repeat_type'];
            $week_day_mapping = array('1'=>'MO', '2'=>'TU', '3'=>'WE', '4'=>'TH', '5'=>'FR', '6'=>'SA', '7'=>'SU');

            if($repeat_type == 'daily')
            {
                $freq = 'DAILY';
                $interval = $event->mec->rinterval;
            }
            elseif($repeat_type == 'weekly')
            {
                $freq = 'WEEKLY';
                $interval = ($event->mec->rinterval/7);
            }
            elseif($repeat_type == 'monthly')
            {
                $freq = 'MONTHLY';
                $interval = $event->mec->rinterval;
            }
            elseif($repeat_type == 'yearly') $freq = 'YEARLY';
            elseif($repeat_type == 'weekday')
            {
                $mec_weekdays = explode(',', trim($event->mec->weekdays, ','));
                foreach($mec_weekdays as $mec_weekday) $byday .= $week_day_mapping[$mec_weekday].',';

                $byday = trim($byday, ', ');
                $freq = 'WEEKLY';
            }
            elseif($repeat_type == 'weekend')
            {
                $mec_weekdays = explode(',', trim($event->mec->weekdays, ','));
                foreach($mec_weekdays as $mec_weekday) $byday .= $week_day_mapping[$mec_weekday].',';

                $byday = trim($byday, ', ');
                $freq = 'WEEKLY';
            }
            elseif($repeat_type == 'certain_weekdays')
            {
                $mec_weekdays = explode(',', trim($event->mec->weekdays, ','));
                foreach($mec_weekdays as $mec_weekday) $byday .= $week_day_mapping[$mec_weekday].',';

                $byday = trim($byday, ', ');
                $freq = 'WEEKLY';
            }
            elseif($repeat_type == 'advanced')
            {
                $advanced_days = is_array($event->meta['mec_advanced_days']) ? $event->meta['mec_advanced_days'] : array();

                $first_rule = isset($advanced_days[0]) ? $advanced_days[0] : NULL;
                $ex = explode('.', $first_rule);

                $bysetpos = isset($ex[1]) ? $ex[1] : NULL;
                if($bysetpos === 'l') $bysetpos = -1;

                $byday_mapping = array('MON'=>'MO', 'TUE'=>'TU', 'WED'=>'WE', 'THU'=>'TH', 'FRI'=>'FR', 'SAT'=>'SA', 'SUN'=>'SU');
                $byday = $byday_mapping[strtoupper($ex[0])];

                $freq = 'MONTHLY';
            }
            elseif($repeat_type == 'custom_days')
            {
                $freq = '';
                $mec_periods = explode(',', trim($event->mec->days, ','));

                $days = '';
                foreach($mec_periods as $mec_period)
                {
                    $mec_days = explode(':', trim($mec_period, ': '));
                    if(!isset($mec_days[1])) continue;

                    $time_start = $event->time['start'];
                    if(isset($mec_days[2])) $time_start = str_replace('-', ':', str_replace('-AM', ' AM', str_replace('-PM', ' PM', $mec_days[2])));

                    $time_end = $event->time['end'];
                    if(isset($mec_days[3])) $time_end = str_replace('-', ':', str_replace('-AM', ' AM', str_replace('-PM', ' PM', $mec_days[3])));

                    $start_time = strtotime($mec_days[0].' '.$time_start);
                    $end_time = strtotime($mec_days[1].' '.$time_end);

                    $gmt_offset_seconds = $this->get_gmt_offset_seconds($start_time, $event);
                    $days .= gmdate('Ymd\\THi00\\Z', ($start_time - $gmt_offset_seconds)).'/'.gmdate('Ymd\\THi00\\Z', ($end_time - $gmt_offset_seconds)).',';
                }

                // Add RDATE
                $recurrence[] = trim('RDATE;VALUE=PERIOD:'.trim($days, ', '), '; ');
            }

            // Add RRULE
            if(trim($freq))
            {
                $rrule = 'RRULE:FREQ='.$freq.';'
                    .($interval > 1 ? 'INTERVAL='.$interval.';' : '')
                    .(($finish != '0000-00-00' and $finish != '') ? 'UNTIL='.$finish.';' : '')
                    .($wkst != '' ? 'WKST='.$wkst.';' : '')
                    .($bysetpos != '' ? 'BYSETPOS='.$bysetpos.';' : '')
                    .($byday != '' ? 'BYDAY='.$byday.';' : '');

                $recurrence[] = trim($rrule, '; ');
            }

            if(trim($event->mec->not_in_days))
            {
                $mec_not_in_days = explode(',', trim($event->mec->not_in_days, ','));

                $not_in_days = '';
                foreach($mec_not_in_days as $mec_not_in_day) $not_in_days .= date('Ymd', strtotime($mec_not_in_day)).',';

                // Add EXDATE
                $recurrence[] = trim('EXDATE;VALUE=DATE:'.trim($not_in_days, ', '), '; ');
            }
        }

        if($only_rrule)
        {
            $rrule = '';
            if(is_array($recurrence) and count($recurrence))
            {
                foreach($recurrence as $recur)
                {
                    if(strpos($recur, 'RRULE') !== false) $rrule = $recur;
                }
            }

            return $rrule;
        }
        else return $recurrence;
    }

    public static function get_upcoming_events($limit = 12)
    {
        MEC::import('app.skins.list', true);

        // Get list skin
        $list = new MEC_skin_list();

        // Attributes
        $atts = array(
            'show_past_events'=>1,
            'start_date_type'=>'today',
            'sk-options'=> array(
                'list' => array('limit'=>20)
            ),
        );

        // Initialize the skin
        $list->initialize($atts);

        // Fetch the events
        $list->fetch();

        return $list->events;
    }

    /**
     * Do the shortcode and return its output
     * @author Webnus <info@webnus.biz>
     * @param integer $shortcode_id
     * @return string
     */
    public static function get_shortcode_events($shortcode_id)
    {
        // Get Render
        $render = new MEC_render();
        $atts = apply_filters('mec_calendar_atts', $render->parse($shortcode_id, array()));

        $skin = isset($atts['skin']) ? $atts['skin'] : $render->get_default_layout();

        $path = MEC::import('app.skins.'.$skin, true, true);
        $skin_path = apply_filters('mec_skin_path', $skin);

        if($skin_path != $skin and $render->file->exists($skin_path)) $path = $skin_path;
        if(!$render->file->exists($path))
        {
            return __('Skin controller does not exist.', 'modern-events-calendar-lite');
        }

        include_once $path;

        $skin_class_name = 'MEC_skin_'.$skin;

        // Create Skin Object Class
        $SKO = new $skin_class_name();

        // Initialize the skin
        $SKO->initialize($atts);

        // Fetch the events
        $SKO->fetch();

        // Return the Events
        return $SKO->events;
    }

    /**
     * User limited for booking a event
     * @author Webnus <info@webnus.biz>
     * @param string $user_email
     * @param array $ticket_info
     * @param integer $limit
     * @return array|boolean
     */
    public function booking_permitted($user_email, $ticket_info, $limit)
    {
        if(!is_array($ticket_info) or is_array($ticket_info) and count($ticket_info) < 2) return false;

        $user_email = sanitize_email($user_email);
        $user = $this->getUser()->by_email($user_email);
        $user_id = isset($user->ID) ? $user->ID : 0;

        // It's the first booking of this email
        if(!$user_id) return true;

        $event_id = isset($ticket_info['event_id']) ? intval($ticket_info['event_id']) : 0;
        $count = isset($ticket_info['count']) ? intval($ticket_info['count']) : 0;

        $timestamp = isset($ticket_info['date']) ? $ticket_info['date'] : '';
        if(!is_numeric($timestamp)) $timestamp = strtotime($timestamp);

        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);
        $hour = date('H', $timestamp);
        $minutes = date('i', $timestamp);

        $permission = true;
        $query = new WP_Query(array
        (
            'post_type'=>$this->get_book_post_type(),
            'author'=>$user_id,
            'posts_per_page'=>-1,
            'post_status'=>array('publish', 'pending', 'draft', 'future', 'private'),
            'year'=>$year,
            'monthnum'=>$month,
            'day'=>$day,
            'hour'=>$hour,
            'minute'=>$minutes,
            'meta_query'=>array
            (
                array('key'=>'mec_event_id', 'value'=>$event_id, 'compare'=>'='),
                array('key'=>'mec_verified', 'value'=>'-1', 'compare'=>'!='), // Don't include canceled bookings
                array('key'=>'mec_confirmed', 'value'=>'-1', 'compare'=>'!='), // Don't include rejected bookings
            )
        ));

        $bookings = 0;
        if($query->have_posts())
        {
            while($query->have_posts())
            {
                $query->the_post();

                $ticket_ids_string = trim(get_post_meta(get_the_ID(), 'mec_ticket_id', true), ', ');
                $ticket_ids_count = count(explode(',', $ticket_ids_string));

                $bookings += $ticket_ids_count;
            }
        }

        if(($bookings + $count) > $limit) $permission = false;

        return array('booking_count' => $bookings, 'permission' => $permission);
    }

    public function booking_permitted_by_ip($event_id, $limit, $ticket_info = array())
    {
        if(!is_array($ticket_info) or count($ticket_info) < 2) return false;

        $count = isset($ticket_info['count']) ? intval($ticket_info['count']) : 0;

        $timestamp = isset($ticket_info['date']) ? $ticket_info['date'] : '';
        if(!is_numeric($timestamp)) $timestamp = strtotime($timestamp);

        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);
        $hour = date('H', $timestamp);
        $minutes = date('i', $timestamp);

        $attendee_ip = $this->get_client_ip();

        $args = array(
            'post_type' => $this->get_book_post_type(),
            'posts_per_page' => -1,
            'post_status' => array('publish', 'pending', 'draft', 'future', 'private'),
            'year'=>$year,
            'monthnum'=>$month,
            'day'=>$day,
            'hour'=>$hour,
            'minute'=>$minutes,
            'meta_query' => array
            (
                array(
                    'key' => 'mec_event_id',
                    'value' => $event_id,
                    'compare' => '=',
                ),
                array(
                    'key' => 'mec_verified',
                    'value' => '-1',
                    'compare' => '!=',
                ),
                array(
                    'key' => 'mec_confirmed',
                    'value' => '-1',
                    'compare' => '!=',
                ),
                array(
                    'key' => 'mec_attendees',
                    'value' => $attendee_ip,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $bookings = 0;
        $permission = true;
        $mec_books = get_posts($args);

        foreach($mec_books as $mec_book)
        {
            $get_attendees = get_post_meta($mec_book->ID, 'mec_attendees', true);
            if(is_array($get_attendees))
            {
                foreach($get_attendees as $attendee)
                {
                    if(isset($attendee['buyerip']) and trim($attendee['buyerip'], '') == $attendee_ip)
                    {
                        $bookings += isset($attendee['count']) ? intval($attendee['count']) : 0;
                    }
                }
            }
        }

        if(($bookings + $count) > $limit) $permission = false;

        return array('booking_count' => $bookings, 'permission' => $permission);
    }

    /**
     * Return SoldOut Or A Few Tickets Label
     * @author Webnus <info@webnus.biz>
     * @param string|object $event
     * @param string $date
     * @return string|boolean
     */
    public function get_flags($event, $date = NULL)
    {
        if(is_object($event))
        {
            $event_id = $event->data->ID;
            $timestamp = $event->data->time['start_timestamp'];
        }
        else
        {
            $event_id = $event;
            $timestamp = strtotime($date);
        }

        if((!isset($event_id) or !trim($event_id)) or !trim($timestamp)) return false;

        // MEC Settings
        $settings = $this->get_settings();

        // Booking on single page is disabled
        if(!isset($settings['booking_status']) or (isset($settings['booking_status']) and !$settings['booking_status'])) return false;

        // Original Event ID for Multilingual Websites
        $event_id = $this->get_original_event($event_id);

        // No Tickets
        $tickets = get_post_meta($event_id, 'mec_tickets', true);
        if(!is_array($tickets) or (is_array($tickets) and !count($tickets))) return false;

        $total_event_seats = 0;
        foreach($tickets as $ticket_id => $ticket)
        {
            if(!is_numeric($ticket_id)) continue;

            $bookings_limit_unlimited = isset($ticket['unlimited']) ? $ticket['unlimited'] : 0;
            if(!$bookings_limit_unlimited and $total_event_seats >= 0 and isset($ticket['limit']) and is_numeric($ticket['limit']) and $ticket['limit'] >= 0) $total_event_seats += $ticket['limit'];
            else $total_event_seats = -1;
        }

        // Convert Timestamp
        $timestamp = $this->get_start_time_of_multiple_days($event_id, $timestamp);

        $book = $this->getBook();
        $availability = $book->get_tickets_availability($event_id, $timestamp);

        if(is_array($availability) and count($availability))
        {
            $remained_tickets = 0;
            foreach($availability as $ticket_id => $remained)
            {
                if(is_numeric($ticket_id) and $remained >= 0) $remained_tickets += $remained;

                // Unlimited Tickets
                if(is_numeric($ticket_id) and $remained == -1)
                {
                    $remained_tickets = -1;
                    break;
                }
            }

            if(isset($availability['total']) and $availability['total'] >= 0 and $remained_tickets >= 0) $remained_tickets = min($availability['total'], $remained_tickets);

            $add_css_class = $remained_tickets ? 'mec-few-tickets' : '';
            $output_tag = ' <span class="mec-event-title-soldout ' . $add_css_class . '"><span class=soldout>%%title%%</span></span> ';

            // Check For Return SoldOut Label Exist.
            if($remained_tickets === 0) return str_replace('%%title%%', __('Sold Out', 'modern-events-calendar-lite'), $output_tag) . '<input type="hidden" value="%%soldout%%"/>';

            // Booking Options
            $booking_options = get_post_meta($event_id, 'mec_booking', true);

            $bookings_last_few_tickets_percentage_inherite = isset($booking_options['last_few_tickets_percentage_inherit']) ? $booking_options['last_few_tickets_percentage_inherit'] : 1;
            $bookings_last_few_tickets_percentage = ((isset($booking_options['last_few_tickets_percentage']) and trim($booking_options['last_few_tickets_percentage']) != '') ? $booking_options['last_few_tickets_percentage'] : NULL);

            $total_bookings_limit = (isset($booking_options['bookings_limit']) and trim($booking_options['bookings_limit'])) ? $booking_options['bookings_limit'] : 100;
            $bookings_limit_unlimited = isset($booking_options['bookings_limit_unlimited']) ? $booking_options['bookings_limit_unlimited'] : 0;
            if($bookings_limit_unlimited == '1') $total_bookings_limit = -1;

            // Get Per Occurrence
            $total_bookings_limit = MEC_feature_occurrences::param($event_id, $timestamp, 'bookings_limit', $total_bookings_limit);

            if(count($tickets) === 1)
            {
                $ticket = reset($tickets);
                if(isset($ticket['limit']) and trim($ticket['limit'])) $total_bookings_limit = $ticket['limit'];

                $bookings_limit_unlimited = isset($ticket['unlimited']) ? $ticket['unlimited'] : 0;
                if($bookings_limit_unlimited == '1') $total_bookings_limit = -1;
            }

            if($total_event_seats >= 0 and $total_bookings_limit >= 0 and $total_event_seats < $total_bookings_limit) $total_bookings_limit = $total_event_seats;

            // Percentage
            $percentage = ((isset($settings['booking_last_few_tickets_percentage']) and trim($settings['booking_last_few_tickets_percentage']) != '') ? $settings['booking_last_few_tickets_percentage'] : 15);
            if(!$bookings_last_few_tickets_percentage_inherite and $bookings_last_few_tickets_percentage) $percentage = (int) $bookings_last_few_tickets_percentage;

            // Check For Return A Few Label Exist.
            if(($total_bookings_limit > 0) and ($remained_tickets > 0 and $remained_tickets <= (($percentage * $total_bookings_limit) / 100))) return str_replace('%%title%%', __('Last Few Tickets', 'modern-events-calendar-lite'), $output_tag);

            return false;
        }

        return false;
    }

    public function is_soldout($event, $date)
    {
        return $this->get_flags($event, $date);
    }

    /**
     * Add Query String To URL
     * @param string $url
     * @param string $key
     * @param string $value
     * @resourse wp-mix.com
     * @return string
     */
    public function add_query_string($url, $key, $value)
    {
        $url = preg_replace('/([?&])'. $key .'=.*?(&|$)/i', '$1$2$4', $url);

        if(substr($url, strlen($url) - 1) == "?" or substr($url, strlen($url) - 1) == "&")
        $url = substr($url, 0, -1);

        if(strpos($url, '?') === false)
        {
            return ($url .'?'. $key .'='. $value);
        }
        else
        {
            return ($url .'&'. $key .'='. $value);
        }
    }

    /**
     * Check Is DateTime Format Validation
     * @param string $format
     * @param string $date
     * @return boolean
     */
    public function check_date_time_validation($format, $date)
    {
        if(func_num_args() < 2) return false;

        $check = DateTime::createFromFormat($format, $date);

        return $check && $check->format($format) === $date;
    }

    public function get_start_of_multiple_days($event_id, $date)
    {
        if(trim($date) == '') return NULL;

        $db = $this->getDB();
        return $db->select("SELECT `dstart` FROM `#__mec_dates` WHERE `post_id`='".$event_id."' AND ((`dstart`='".esc_sql($date)."') OR (`dstart`<'".esc_sql($date)."' AND `dend`>='".esc_sql($date)."')) ORDER BY `dstart` DESC LIMIT 1", 'loadResult');
    }

    public function get_start_time_of_multiple_days($event_id, $time)
    {
        if(!trim($time)) return NULL;

        $db = $this->getDB();
        $new_time = $db->select("SELECT `tstart` FROM `#__mec_dates` WHERE `post_id`=".$event_id." AND ((`tstart`=".esc_sql($time).") OR (`tstart`<".esc_sql($time)." AND `tend`>".esc_sql($time).")) ORDER BY `tstart` DESC LIMIT 1", 'loadResult');

        return ($new_time ? $new_time : $time);
    }

    public function is_midnight_event($event)
    {
        // Settings
        $settings = $this->get_settings();

        $start_timestamp = strtotime($event->date['start']['date']);
        $end_timestamp = strtotime($event->date['end']['date']);

        $diff = $this->date_diff($event->date['start']['date'], $event->date['end']['date']);
        $days = (isset($diff->days) and !$diff->invert) ? $diff->days : 0;

        $time = $event->data->time['end_raw'];

        // Midnight Hour
        $midnight_hour = (isset($settings['midnight_hour']) and $settings['midnight_hour']) ? $settings['midnight_hour'] : 0;
        $midnight = $end_timestamp+(3600*$midnight_hour);

        // End Date is before Midnight
        if($days == 1 and $start_timestamp < $end_timestamp and $midnight >= strtotime($event->date['end']['date'].' '.$time)) return true;

        return false;
    }

    public function mec_content_html($text, $max_length)
    {
        $tags   = array();
        $result = "";
        $is_open   = false;
        $grab_open = false;
        $is_close  = false;
        $in_double_quotes = false;
        $in_single_quotes = false;
        $tag = "";
        $i = 0;
        $stripped = 0;
        $stripped_text = strip_tags($text);

        while($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length)
        {
            $symbol  = $text[$i];
            $result .= $symbol;
            switch($symbol)
            {
                case '<':
                    $is_open   = true;
                    $grab_open = true;
                    break;

                case '"':
                    if($in_double_quotes) $in_double_quotes = false;
                    else $in_double_quotes = true;
                    break;

                case "'":
                    if($in_single_quotes) $in_single_quotes = false;
                    else $in_single_quotes = true;
                    break;

                case '/':
                    if($is_open && !$in_double_quotes && !$in_single_quotes)
                    {
                        $is_close  = true;
                        $is_open   = false;
                        $grab_open = false;
                    }

                    break;

                case ' ':
                    if($is_open) $grab_open = false;
                    else $stripped++;

                    break;

                case '>':
                    if($is_open)
                    {
                        $is_open   = false;
                        $grab_open = false;
                        array_push($tags, $tag);
                        $tag = "";
                    }
                    elseif($is_close)
                    {
                        $is_close = false;
                        array_pop($tags);
                        $tag = "";
                    }

                    break;

                default:
                    if($grab_open || $is_close) $tag .= $symbol;
                    if(!$is_open && !$is_close) $stripped++;
            }

            $i++;
        }

        while($tags) $result .= "</".array_pop($tags).">";

        return $result;
    }

    public function get_users_dropdown($current = array(), $notifications = 'booking_notification')
    {
        $users = get_users();
        ob_start();
        ?>
            <select id="mec_notifications_<?php echo $notifications; ?>_receiver_users" class="mec-notification-dropdown-select2" name="mec[notifications][<?php echo $notifications; ?>][receiver_users][]" multiple="multiple">
                <?php
                    foreach($users as $user)
                    {
                ?>
                    <option value="<?php echo isset($user->data->ID) ? intval($user->data->ID) : 0; ?>" <?php echo (is_array($current) and in_array(intval($user->data->ID), $current)) ? 'selected="selected"' : ''; ?>><?php echo (isset($user->data->display_name) and trim($user->data->display_name)) ? trim($user->data->display_name) : '(' . trim($user->data->user_login) . ')'; ?></option>
                <?php
                    }
                ?>
            </select>
        <?php
        $output = ob_get_contents();
        ob_clean();

        return $output;
    }

    public function get_emails_by_users($users)
    {
        $users_list = array();
        if(is_array($users) and count($users))
        {
            $query = 'SELECT `user_email` FROM `#__users` WHERE';
            foreach($users as $user_id)
            {
                $query .= ' ID='.$user_id.' OR';
            }

            $db = $this->getDB();
            $users_list = $db->select(substr(trim($query), 0, -2), 'loadObjectList');
        }

        return array_keys($users_list);
    }

    public function get_roles_dropdown($current = array(), $notifications = 'booking_notification')
    {
        global $wp_roles;
        $roles = $wp_roles->get_names();
        ob_start();
        ?>
            <select id="mec_notifications_<?php echo $notifications; ?>_receiver_roles" class="mec-notification-dropdown-select2" name="mec[notifications][<?php echo $notifications; ?>][receiver_roles][]" multiple="multiple">
                <?php
                    foreach($roles as $role_key => $role_name)
                    {
                ?>
                    <option value="<?php echo esc_attr($role_key); ?>" <?php echo (is_array($current) and in_array(trim($role_key), $current)) ? 'selected="selected"' : ''; ?>><?php echo $role_name; ?></option>
                <?php
                    }
                ?>
            </select>
        <?php
        $output = ob_get_contents();
        ob_clean();

        return $output;
    }

    public function get_emails_by_roles($roles)
    {
        $user_list = array();
        foreach($roles as $role)
        {
            $curren_get_users = get_users(array(
                'role' => $role,
            ));

            if(count($curren_get_users))
            {
                foreach($curren_get_users as $user)
                {
                    if(isset($user->data->user_email) and !in_array($user->data->user_email, $user_list)) $user_list[] = $user->data->user_email;
                }
            }
        }

        return $user_list;
    }

    public function get_normal_labels($event, $display_label = false)
    {
        $output = '';

        if($display_label != false and is_object($event) and isset($event->data->labels) and !empty($event->data->labels))
        {
            foreach($event->data->labels as $label)
            {
                if(isset($label['style']) and !trim($label['style']) and isset($label['name']) and trim($label['name'])) $output .= '<span data-style="Normal" class="mec-label-normal" style="background-color:'.$label['color'].';">' . trim($label['name']) . '</span>';
            }
        }

        // Ongoing Event
        if($display_label and $this->is_ongoing($event)) $output .= '<span data-style="Normal" class="mec-label-normal mec-ongoing-normal-label">' . esc_html__('Ongoing', 'modern-events-calendar-lite') . '</span>';
        // Expired Event
        elseif($display_label and $this->is_expired($event)) $output .= '<span data-style="Normal" class="mec-label-normal mec-expired-normal-label">' . esc_html__('Expired', 'modern-events-calendar-lite') . '</span>';

        return $output ? '<span class="mec-labels-normal">' . $output . '</span>' : $output;
    }

    public function display_cancellation_reason($event, $display_reason = false)
    {
        if(!is_object($event)) return '';

        $start_timestamp = (isset($event->data->time['start_timestamp']) ? $event->data->time['start_timestamp'] : (isset($event->date['start']['timestamp']) ? $event->date['start']['timestamp'] : strtotime($event->date['start']['date'])));

        // All Params
        $params = MEC_feature_occurrences::param($event->ID, $start_timestamp, '*');

        $event_status = (isset($event->data->meta['mec_event_status']) and trim($event->data->meta['mec_event_status'])) ? $event->data->meta['mec_event_status'] : 'EventScheduled';
        $event_status = (isset($params['event_status']) and trim($params['event_status']) != '') ? $params['event_status'] : $event_status;

        $reason = get_post_meta($event->ID, 'mec_cancelled_reason', true);
        $reason = (isset($params['cancelled_reason']) and trim($params['cancelled_reason']) != '') ? $params['cancelled_reason'] : $reason;

        $output = '';
        if(isset($event_status) and $event_status == 'EventCancelled' && $display_reason != false and isset($reason) and !empty($reason))
        {
            $output = '<div class="mec-cancellation-reason"><span>'.$reason.'</span></div>';
        }

        return $output;
    }

    public function standardize_format($date = '', $format = 'Y-m-d')
    {
        if(!trim($date)) return '';

        $date = str_replace('.', '-', $date);
        $f = explode('&', trim($format));

        if(isset($f[1])) $return = date($f[1], strtotime($date));
        else $return = date($format, strtotime($date));

        return $return;
    }

    public function timepicker($args)
    {
        $method = isset($args['method']) ? $args['method'] : 24;
        $time_hour = isset($args['time_hour']) ? $args['time_hour'] : NULL;
        $time_minutes = isset($args['time_minutes']) ? $args['time_minutes'] : NULL;
        $time_ampm = isset($args['time_ampm']) ? $args['time_ampm'] : NULL;
        $name = isset($args['name']) ? $args['name'] : 'mec[date]';
        $id_key = isset($args['id_key']) ? $args['id_key'] : '';

        $hour_key = isset($args['hour_key']) ? $args['hour_key'] : 'hour';
        $minutes_key = isset($args['minutes_key']) ? $args['minutes_key'] : 'minutes';
        $ampm_key = isset($args['ampm_key']) ? $args['ampm_key'] : 'ampm';

        if($method == 24)
        {
            if($time_ampm == 'PM' and $time_hour != 12) $time_hour += 12;
            if($time_ampm == 'AM' and $time_hour == 12) $time_hour += 12;
            ?>
            <select name="<?php echo $name; ?>[<?php echo $hour_key; ?>]" <?php if(trim($id_key)): ?>id="mec_<?php echo $id_key; ?>hour"<?php endif; ?> title="<?php esc_attr_e('Hours', 'modern-events-calendar-lite'); ?>">
                <?php for ($i = 0; $i <= 23; $i++) : ?>
                    <option <?php echo ($time_hour == $i) ? 'selected="selected"' : ''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            <span class="time-dv">:</span>
            <select name="<?php echo $name; ?>[<?php echo $minutes_key; ?>]" <?php if(trim($id_key)): ?>id="mec_<?php echo $id_key; ?>minutes"<?php endif; ?> title="<?php esc_attr_e('Minutes', 'modern-events-calendar-lite'); ?>">
                <?php for ($i = 0; $i <= 11; $i++) : ?>
                    <option <?php echo ($time_minutes == ($i * 5)) ? 'selected="selected"' : ''; ?> value="<?php echo($i * 5); ?>"><?php echo sprintf('%02d', ($i * 5)); ?></option>
                <?php endfor; ?>
            </select>
            <?php
        }
        else
        {
            if($time_ampm == 'AM' and $time_hour == '0') $time_hour = 12;
            ?>
            <select name="<?php echo $name; ?>[<?php echo $hour_key; ?>]" <?php if(trim($id_key)): ?>id="mec_<?php echo $id_key; ?>hour"<?php endif; ?> title="<?php esc_attr_e('Hours', 'modern-events-calendar-lite'); ?>">
                <?php for ($i = 1; $i <= 12; $i++) : ?>
                    <option <?php echo ($time_hour == $i) ? 'selected="selected"' : ''; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            <span class="time-dv">:</span>
            <select name="<?php echo $name; ?>[<?php echo $minutes_key; ?>]" <?php if(trim($id_key)): ?>id="mec_<?php echo $id_key; ?>minutes"<?php endif; ?> title="<?php esc_attr_e('Minutes', 'modern-events-calendar-lite'); ?>">
                <?php for ($i = 0; $i <= 11; $i++) : ?>
                    <option <?php echo ($time_minutes == ($i * 5)) ? 'selected="selected"' : ''; ?> value="<?php echo($i * 5); ?>"><?php echo sprintf('%02d', ($i * 5)); ?></option>
                <?php endfor; ?>
            </select>
            <select name="<?php echo $name; ?>[<?php echo $ampm_key; ?>]" <?php if(trim($id_key)): ?>id="mec_<?php echo $id_key; ?>ampm"<?php endif; ?> title="<?php esc_attr_e('AM / PM', 'modern-events-calendar-lite'); ?>">
                <option <?php echo ($time_ampm == 'AM') ? 'selected="selected"' : ''; ?> value="AM"><?php _e('AM', 'modern-events-calendar-lite'); ?></option>
                <option <?php echo ($time_ampm == 'PM') ? 'selected="selected"' : ''; ?> value="PM"><?php _e('PM', 'modern-events-calendar-lite'); ?></option>
            </select>
            <?php
        }
    }

    public function holding_status($event)
    {
        if($this->is_ongoing($event)) return '<dl><dd><span class="mec-holding-status mec-holding-status-ongoing">'.__('Ongoing...', 'modern-events-calendar-lite').'</span></dd></dl>';
        elseif($this->is_expired($event)) return '<dl><dd><span class="mec-holding-status mec-holding-status-expired">'.__('Expired!', 'modern-events-calendar-lite').'</span></dd></dl>';

        return '';
    }

    public function is_ongoing($event)
    {
        $now = current_time('Y-m-d H:i:s');
        $date = (($event and isset($event->date)) ? $event->date : array());

        $start_date = (isset($date['start']) and isset($date['start']['date'])) ? $date['start']['date'] : NULL;
        $end_date = (isset($date['end']) and isset($date['end']['date'])) ? $date['end']['date'] : NULL;

        if(!$start_date or !$end_date) return false;

        $start_time = NULL;
        if(isset($date['start']['hour']))
        {
            $s_hour = $date['start']['hour'];
            if(isset($date['start']['ampm']) and strtoupper($date['start']['ampm']) == 'AM' and $s_hour == '0') $s_hour = 12;

            $start_time = sprintf("%02d", $s_hour).':';
            $start_time .= sprintf("%02d", $date['start']['minutes']);
            if(isset($date['start']['ampm'])) $start_time .= ' '.trim($date['start']['ampm']);
        }
        elseif(isset($event->data->time) and is_array($event->data->time) and isset($event->data->time['start_timestamp'])) $start_time = date('H:i', $event->data->time['start_timestamp']);

        $end_time = NULL;
        if(isset($date['end']['hour']))
        {
            $e_hour = $date['end']['hour'];
            if(isset($date['end']['ampm']) and strtoupper($date['end']['ampm']) == 'AM' and $e_hour == '0') $e_hour = 12;

            $end_time = sprintf("%02d", $e_hour).':';
            $end_time .= sprintf("%02d", $date['end']['minutes']);
            if($date['end']['ampm']) $end_time .= ' '.trim($date['end']['ampm']);
        }
        elseif(isset($event->data->time) and is_array($event->data->time) and isset($event->data->time['end_timestamp'])) $end_time = date('H:i', $event->data->time['end_timestamp']);

        if(!$start_time or !$end_time) return false;

        $allday = get_post_meta($event->ID, 'mec_allday', true);
        if($allday)
        {
            $start_time = '12:01 AM';
            $end_time = '11:59 PM';
        }

        // The event is ongoing
        if($this->is_past($start_date.' '.$start_time, $now) and !$this->is_past($end_date.' '.$end_time, $now)) return true;
        return false;
    }

    public function is_expired($event)
    {
        $now = current_time('Y-m-d H:i:s');
        $date = (($event and isset($event->date)) ? $event->date : array());

        $end_date = (isset($date['end']) and isset($date['end']['date'])) ? $date['end']['date'] : NULL;
        if(!$end_date) return false;

        $e_hour = (isset($date['end']['hour']) ? $date['end']['hour'] : 11);
        if(isset($date['end']['ampm']) and strtoupper($date['end']['ampm']) == 'AM' and $e_hour == '0') $e_hour = 12;

        $end_time = sprintf("%02d", $e_hour).':';
        $end_time .= sprintf("%02d", (isset($date['end']['minutes']) ? $date['end']['minutes'] : 59));
        $end_time .= ' '.(isset($date['end']['ampm']) ? trim($date['end']['ampm']) : 'PM');

        $allday = isset($date['allday']) ? $date['allday'] : 0;
        if($allday) $end_time = '11:59 PM';

        // The event is expired
        if($this->is_past($end_date.' '.$end_time, $now)) return true;
        return false;
    }

    public function is_started($event)
    {
        $now = current_time('Y-m-d H:i:s');
        $date = (($event and isset($event->date)) ? $event->date : array());

        $start_date = (isset($date['start']) and isset($date['start']['date'])) ? $date['start']['date'] : NULL;
        if(!$start_date) return false;

        $s_hour = (isset($date['start']['hour']) ? $date['start']['hour'] : NULL);
        if(isset($date['start']['ampm']) and strtoupper($date['start']['ampm']) == 'AM' and $s_hour == '0') $s_hour = 12;

        $start_time = sprintf("%02d", $s_hour).':';
        $start_time .= sprintf("%02d", (isset($date['start']['minutes']) ? $date['start']['minutes'] : NULL));
        $start_time .= ' '.(isset($date['start']['ampm']) ? trim($date['start']['ampm']) : NULL);

        $allday = (isset($date['allday']) ? $date['allday'] : 0);
        if($allday) $start_time = '12:01 AM';

        // The event is started
        if($this->is_past($start_date.' '.$start_time, $now)) return true;
        return false;
    }

    public function array_key_first($arr)
    {
        if(!function_exists('array_key_first'))
        {
            reset($arr);
            return key($arr);
        }
        else return array_key_first($arr);
    }

    public function array_key_last($arr)
    {
        if(!function_exists('array_key_last'))
        {
            end($arr);
            return key($arr);
        }
        else return array_key_last($arr);
    }

    public function is_day_first($format = NULL)
    {
        if(!trim($format)) $format = get_option('date_format');
        $chars = str_split($format);

        $status = true;
        foreach($chars as $char)
        {
            if(in_array($char, array('d', 'D', 'j', 'l', 'N', 'S', 'w', 'z')))
            {
                $status = true;
                break;
            }
            elseif(in_array($char, array('F', 'm', 'M', 'n')))
            {
                $status = false;
                break;
            }
        }

        return $status;
    }

    public function is_year_first($format = NULL)
    {
        if(!trim($format)) $format = get_option('date_format');
        $chars = str_split($format);

        $status = true;
        foreach($chars as $char)
        {
            if(in_array($char, array('Y', 'y', 'o')))
            {
                $status = true;
                break;
            }
            elseif(in_array($char, array('F', 'm', 'M', 'n', 'd', 'D', 'j', 'l', 'N', 'S', 'w', 'z')))
            {
                $status = false;
                break;
            }
        }

        return $status;
    }

    public function timezones($selected)
    {
        $output = wp_timezone_choice($selected);

        $ex = explode('<optgroup', $output);
        unset($ex[count($ex) - 1]);

        return implode('<optgroup', $ex);
    }

    public function get_event_next_occurrences($event, $occurrence, $maximum = 2, $occurrence_time = NULL)
    {
        $event_id = $event->ID;

        // Event Repeat Type
        $repeat_type = (!empty($event->meta['mec_repeat_type']) ? $event->meta['mec_repeat_type'] : '');

        $md_start = $this->get_start_of_multiple_days($event_id, $occurrence);
        if($md_start) $occurrence = $md_start;

        $md_start_time = $this->get_start_time_of_multiple_days($event_id, $occurrence_time);
        if($md_start_time) $occurrence_time = $md_start_time;

        if(strtotime($occurrence) and in_array($repeat_type, array('certain_weekdays', 'custom_days', 'weekday', 'weekend', 'advanced'))) $occurrence = date('Y-m-d', strtotime($occurrence));
        elseif(strtotime($occurrence))
        {
            $new_occurrence = date('Y-m-d', strtotime('-1 day', strtotime($occurrence)));
            if(in_array($repeat_type, array('monthly')) and date('m', strtotime($new_occurrence)) != date('m', strtotime($occurrence))) $new_occurrence = date('Y-m-d', strtotime($occurrence));

            $occurrence = $new_occurrence;
        }
        else $occurrence = NULL;

        $render = $this->getRender();
        return $render->dates($event_id, (isset($event->data) ? $event->data : NULL), $maximum, (trim($occurrence_time) ? date('Y-m-d H:i:s', $occurrence_time) : $occurrence));
    }

    public function get_post_thumbnail_url($post = NULL, $size = 'post-thumbnail')
    {
        if(function_exists('get_the_post_thumbnail_url')) return get_the_post_thumbnail_url($post, $size);
        else
        {
            $post_thumbnail_id = get_post_thumbnail_id($post);
            if(!$post_thumbnail_id) return false;

            $image = wp_get_attachment_image_src($post_thumbnail_id, $size);
            return isset($image['0']) ? $image['0'] : false;
        }
    }

    public function is_multipleday_occurrence($event, $check_same_month = false)
    {
        // Multiple Day Flag
        if(isset($event->data) and isset($event->data->multipleday)) return (boolean) $event->data->multipleday;

        $start_date = ((isset($event->date) and isset($event->date['start']) and isset($event->date['start']['date'])) ? $event->date['start']['date'] : NULL);
        $end_date = ((isset($event->date) and isset($event->date['end']) and isset($event->date['end']['date'])) ? $event->date['end']['date'] : NULL);

        if($check_same_month)
        {
            $multipleday = (!is_null($start_date) and $start_date !== $end_date);
            return ($multipleday and (date('m', strtotime($start_date)) == date('m', strtotime($end_date))));
        }

        return (!is_null($start_date) and $start_date !== $end_date);
    }

    public function get_wp_user_fields()
    {
        $raw_fields = get_user_meta(get_current_user_id());
        $forbidden = array(
            'nickname',
            'syntax_highlighting',
            'comment_shortcuts',
            'admin_color',
            'use_ssl',
            'show_admin_bar_front',
            'wp_user_level',
            'user_last_view_date',
            'user_last_view_date_events',
            'wc_last_active',
            'last_update',
            'last_activity',
            'locale',
            'show_welcome_panel',
            'rich_editing',
            'nav_menu_recently_edited',
        );

        $fields = array();
        foreach($raw_fields as $key => $values)
        {
            if(substr($key, 0, 1) === '_') continue;
            if(substr($key, 0, 4) === 'icl_') continue;
            if(substr($key, 0, 4) === 'mec_') continue;
            if(substr($key, 0, 3) === 'wp_') continue;
            if(substr($key, 0, 10) === 'dismissed_') continue;
            if(in_array($key, $forbidden)) continue;

            $value = (isset($values[0]) ? $values[0] : NULL);
            if(is_array($value)) continue;
            if(is_serialized($value)) continue;

            $fields[$key] = trim(ucwords(str_replace('_', ' ', $key)));
        }

        return $fields;
    }

    public function get_wp_user_fields_dropdown($name, $value)
    {
        $fields = $this->get_wp_user_fields();

        $dropdown = '<select name="'.esc_attr($name).'" title="'.esc_html__('Mapping with Profile Fields', 'modern-events-calendar-lite').'">';
        $dropdown .= '<option value="">-----</option>';
        foreach($fields as $key => $label) $dropdown .= '<option value="'.esc_attr($key).'" '.($value == $key ? 'selected="selected"' : '').'>'.esc_html($label).'</option>';
        $dropdown .= '</select>';

        return $dropdown;
    }

    public function wizard_import_dummy_events()
    {
        if(apply_filters('mec_activation_import_events', true))
        {
            // Create Default Events
            $events = array
            (
                array('title'=>'One Time Multiple Day Event', 'start'=>date('Y-m-d', strtotime('+5 days')), 'end'=>date('Y-m-d', strtotime('+7 days')), 'finish'=>date('Y-m-d', strtotime('+7 days')), 'repeat_type'=>'', 'repeat_status'=>0, 'interval'=>NULL, 'meta'=>array('mec_color'=>'dd823b')),
                array('title'=>'Daily each 3 days', 'start'=>date('Y-m-d'), 'end'=>date('Y-m-d'), 'repeat_type'=>'daily', 'repeat_status'=>1, 'interval'=>3, 'meta'=>array('mec_color'=>'a3b745')),
                array('title'=>'Weekly on Mondays', 'start'=>date('Y-m-d', strtotime('Next Monday')), 'end'=>date('Y-m-d', strtotime('Next Monday')), 'repeat_type'=>'weekly', 'repeat_status'=>1, 'interval'=>7, 'meta'=>array('mec_color'=>'e14d43')),
                array('title'=>'Monthly on 27th', 'start'=>date('Y-m-27'), 'end'=>date('Y-m-27'), 'repeat_type'=>'monthly', 'repeat_status'=>1, 'interval'=>NULL, 'year'=>'*', 'month'=>'*', 'day'=>',27,', 'week'=>'*', 'weekday'=>'*', 'meta'=>array('mec_color'=>'00a0d2')),
                array('title'=>'Yearly on August 20th and 21st', 'start'=>date('Y-08-20'), 'end'=>date('Y-08-21'), 'repeat_type'=>'yearly', 'repeat_status'=>1, 'interval'=>NULL, 'year'=>'*', 'month'=>',08,', 'day'=>',20,21,', 'week'=>'*', 'weekday'=>'*', 'meta'=>array('mec_color'=>'fdd700')),
            );

            // Import Events
            $this->save_events($events);
        }
    }

    public function wizard_import_dummy_shortcodes()
    {
        if(apply_filters('mec_activation_import_shortcodes', true))
        {
            // Search Form Options
            $sf_options = array('category'=>array('type'=>'dropdown'), 'text_search'=>array('type'=>'text_input'));

            // Create Default Calendars
            $calendars = array
            (
                array('title'=>'Full Calendar', 'meta'=>array('skin'=>'full_calendar', 'show_past_events'=>1, 'sk-options'=>array('full_calendar'=>array('start_date_type'=>'today', 'default_view'=>'list', 'monthly'=>1, 'weekly'=>1, 'daily'=>1, 'list'=>1)), 'sf-options'=>array('full_calendar'=>array('month_filter'=>array('type'=>'dropdown'), 'text_search'=>array('type'=>'text_input'))), 'sf_status'=>1)),
                array('title'=>'Monthly View', 'meta'=>array('skin'=>'monthly_view', 'show_past_events'=>1, 'sk-options'=>array('monthly_view'=>array('start_date_type'=>'start_current_month', 'next_previous_button'=>1)), 'sf-options'=>array('monthly_view'=>$sf_options), 'sf_status'=>1)),
                array('title'=>'Weekly View', 'meta'=>array('skin'=>'weekly_view', 'show_past_events'=>1, 'sk-options'=>array('weekly_view'=>array('start_date_type'=>'start_current_month', 'next_previous_button'=>1)), 'sf-options'=>array('weekly_view'=>$sf_options), 'sf_status'=>1)),
                array('title'=>'Daily View', 'meta'=>array('skin'=>'daily_view', 'show_past_events'=>1, 'sk-options'=>array('daily_view'=>array('start_date_type'=>'start_current_month', 'next_previous_button'=>1)), 'sf-options'=>array('daily_view'=>$sf_options), 'sf_status'=>1)),
                array('title'=>'Map View', 'meta'=>array('skin'=>'map', 'show_past_events'=>1, 'sk-options'=>array('map'=>array('limit'=>200)), 'sf-options'=>array('map'=>$sf_options), 'sf_status'=>1)),
                array('title'=>'Upcoming events (List)', 'meta'=>array('skin'=>'list', 'show_past_events'=>0, 'sk-options'=>array('list'=>array('load_more_button'=>1)), 'sf-options'=>array('list'=>$sf_options), 'sf_status'=>1)),
                array('title'=>'Upcoming events (Grid)', 'meta'=>array('skin'=>'grid', 'show_past_events'=>0, 'sk-options'=>array('grid'=>array('load_more_button'=>1)), 'sf-options'=>array('grid'=>$sf_options), 'sf_status'=>1)),
                array('title'=>'Carousel View', 'meta'=>array('skin'=>'carousel', 'show_past_events'=>0, 'sk-options'=>array('carousel'=>array('count'=>3, 'limit'=>12)), 'sf-options'=>array('carousel'=>$sf_options), 'sf_status'=>0)),
                array('title'=>'Countdown View', 'meta'=>array('skin'=>'countdown', 'show_past_events'=>0, 'sk-options'=>array('countdown'=>array('style'=>'style3', 'event_id'=>'-1')), 'sf-options'=>array('countdown'=>$sf_options), 'sf_status'=>0)),
                array('title'=>'Slider View', 'meta'=>array('skin'=>'slider', 'show_past_events'=>0, 'sk-options'=>array('slider'=>array('style'=>'t1', 'limit'=>6, 'autoplay'=>3000)), 'sf-options'=>array('slider'=>$sf_options), 'sf_status'=>0)),
                array('title'=>'Masonry View', 'meta'=>array('skin'=>'masonry', 'show_past_events'=>0, 'sk-options'=>array('masonry'=>array('limit'=>24, 'filter_by'=>'category')), 'sf-options'=>array('masonry'=>$sf_options), 'sf_status'=>0)),
                array('title'=>'Agenda View', 'meta'=>array('skin'=>'agenda', 'show_past_events'=>0, 'sk-options'=>array('agenda'=>array('load_more_button'=>1)), 'sf-options'=>array('agenda'=>$sf_options), 'sf_status'=>1)),
                array('title'=>'Timetable View', 'meta'=>array('skin'=>'timetable', 'show_past_events'=>0, 'sk-options'=>array('timetable'=>array('next_previous_button'=>1)), 'sf-options'=>array('timetable'=>$sf_options), 'sf_status'=>1)),
                array('title'=>'Tile View', 'meta'=>array('skin'=>'tile', 'show_past_events'=>0, 'sk-options'=>array('tile'=>array('next_previous_button'=>1)), 'sf-options'=>array('tile'=>$sf_options), 'sf_status'=>1)),
                array('title'=>'Timeline View', 'meta'=>array('skin'=>'timeline', 'show_past_events'=>0, 'sk-options'=>array('timeline'=>array('load_more_button'=>1)), 'sf-options'=>array('timeline'=>$sf_options), 'sf_status'=>0)),
            );

            foreach($calendars as $calendar)
            {
                // Calendar exists
                if(post_exists($calendar['title'], 'modern-events-calendar-lite')) continue;

                $post = array('post_title'=>$calendar['title'], 'post_content'=>'MEC', 'post_type'=>'mec_calendars', 'post_status'=>'publish');
                $post_id = wp_insert_post($post);

                update_post_meta($post_id, 'label', '');
                update_post_meta($post_id, 'category', '');
                update_post_meta($post_id, 'location', '');
                update_post_meta($post_id, 'organizer', '');
                update_post_meta($post_id, 'tag', '');
                update_post_meta($post_id, 'author', '');

                foreach($calendar['meta'] as $key=>$value) update_post_meta($post_id, $key, $value);
            }
        }
    }

    public function save_wizard_options()
    {
        $request = $this->getRequest();
        $mec = $request->getVar('mec', array());

        $filtered = array();
        foreach($mec as $key=>$value) $filtered[$key] = (is_array($value) ? $value : array());

        $current = get_option('mec_options', array());
        $final = $current;

        // Merge new options with previous options
        foreach($filtered as $key=>$value)
        {
            if(is_array($value))
            {
                foreach($value as $k=>$v)
                {
                    // Define New Array
                    if(!isset($final[$key])) $final[$key] = array();

                    // Overwrite Old Value
                    $final[$key][$k] = $v;
                }
            }
            // Overwrite Old Value
            else $final[$key] = $value;
        }

        update_option('mec_options', $final);
        die();
    }

    public function is_user_booked($user_id, $event_id, $timestamp)
    {
        $bookings = $this->get_bookings($event_id, $timestamp, 1, $user_id);
        return (boolean) count($bookings);
    }

    public function get_event_attendees($id, $occurrence = NULL, $verified = true)
    {
        $allday = get_post_meta($id, 'mec_allday', true);
        if($allday and $occurrence)
        {
            $start_seconds = get_post_meta($id, 'mec_start_day_seconds', true);
            $occurrence = ($occurrence - 60) + $start_seconds;
        }

        $date_query = array();
        if($occurrence)
        {
            $date_query = array(
                array(
                    'year' => date('Y', $occurrence),
                    'month'=> date('m', $occurrence),
                    'day' => date('d', $occurrence),
                    'hour' => date('H', $occurrence),
                    'minute' => date('i', $occurrence),
                ),
            );
        }

        $booking_options = get_post_meta($id, 'mec_booking', true);
        $bookings_all_occurrences = (isset($booking_options['bookings_all_occurrences']) ? $booking_options['bookings_all_occurrences'] : 0);
        if($bookings_all_occurrences and $occurrence)
        {
            $date_query = array(
                'before' => date('Y-m-d', $occurrence).' 23:59:59',
            );
        }

        $meta_query = array();
        if($verified)
        {
            $meta_query = array
            (
                'relation' => 'AND',
                array(
                    'key' => 'mec_verified',
                    'value' => '1',
                    'compare' => '=',
                ),
                array(
                    'key' => 'mec_confirmed',
                    'value' => '1',
                    'compare' => '=',
                ),
            );
        }

        // Fetch Bookings
        $bookings = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => $this->get_book_post_type(),
            'post_status' => 'any',
            'meta_key' => 'mec_event_id',
            'meta_value' => $id,
            'meta_compare' => '=',
            'meta_query' => $meta_query,
            'date_query' => $date_query,
        ));

        // Attendees
        $attendees = array();
        foreach($bookings as $booking)
        {
            $atts = get_post_meta($booking->ID, 'mec_attendees', true);
            if(isset($atts['attachments'])) unset($atts['attachments']);

            foreach($atts as $key => $value)
            {
                if(!is_numeric($key)) continue;

                $atts[$key]['book_id'] = $booking->ID;
                $atts[$key]['key'] = ($key + 1);
            }

            $attendees = array_merge($attendees, $atts);
        }

        $attendees = apply_filters('mec_attendees_list_data', $attendees, $id, $occurrence);
        usort($attendees, function($a, $b)
        {
            return strcmp($a['name'], $b['name']);
        });

        return $attendees;
    }

    public function mysql2date($format, $date, $timezone)
    {
        if(empty($date)) return false;

        $datetime = date_create($date, $timezone);
        if(false === $datetime) return false;

        // Returns a sum of timestamp with timezone offset. Ideally should never be used.
        if('G' === $format || 'U' === $format) return $datetime->getTimestamp() + $datetime->getOffset();

        return $datetime->format($format);
    }

    public function is_second_booking($event_id, $email)
    {
        $attendees = $this->get_event_attendees($event_id, NULL, false);
        if(!is_array($attendees)) $attendees = array();

        $found = false;
        foreach($attendees as $attendee)
        {
            if($email and isset($attendee['email']) and trim(strtolower($email)) == trim(strtolower($attendee['email'])))
            {
                $found = true;
                break;
            }
        }

        return $found;
    }

    public function get_from_mapped_field($reg_field, $default_value = NULL)
    {
        $current_user_id = get_current_user_id();
        if(!$current_user_id) return $default_value;

        $mapped_field = (isset($reg_field['mapping']) and trim($reg_field['mapping']) != '') ? $reg_field['mapping'] : NULL;
        if(!$mapped_field) return $default_value;

        $value = get_user_meta($current_user_id, $mapped_field, true);
        return ($value ? $value : $default_value);
    }

    public function get_master_location_id($event, $occurrence = NULL)
    {
        // Event ID
        if(is_numeric($event))
        {
            $location_id = get_post_meta($event, 'mec_location_id', true);

            // Get From Occurrence
            if($occurrence) $location_id = MEC_feature_occurrences::param($event, $occurrence, 'location_id', $location_id);
        }
        // Event Object
        else
        {
            $location_id = (isset($event->data) and isset($event->data->meta) and isset($event->data->meta['mec_location_id'])) ? $event->data->meta['mec_location_id'] : '';

            // Get From Occurrence
            if(isset($event->date) and isset($event->date['start']) and isset($event->date['start']['timestamp'])) $location_id = MEC_feature_occurrences::param($event->ID, $event->date['start']['timestamp'], 'location_id', $location_id);
        }

        if(trim($location_id) === '' or $location_id == 1) $location_id = 0;

        $location_id = apply_filters('wpml_object_id', $location_id, 'mec_location', true);
        return $location_id;
    }

    public function get_master_organizer_id($event, $occurrence = NULL)
    {
        // Event ID
        if(is_numeric($event))
        {
            $organizer_id = get_post_meta($event, 'mec_organizer_id', true);

            // Get From Occurrence
            if($occurrence) $organizer_id = MEC_feature_occurrences::param($event, $occurrence, 'organizer_id', $organizer_id);
        }
        // Event Object
        else
        {
            $organizer_id = (isset($event->data) and isset($event->data->meta) and isset($event->data->meta['mec_organizer_id'])) ? $event->data->meta['mec_organizer_id'] : '';

            // Get From Occurrence
            if(isset($event->date) and isset($event->date['start']) and isset($event->date['start']['timestamp'])) $organizer_id = MEC_feature_occurrences::param($event->ID, $event->date['start']['timestamp'], 'organizer_id', $organizer_id);
        }

        if(trim($organizer_id) === '' or $organizer_id == 1) $organizer_id = 0;

        $organizer_id = apply_filters('wpml_object_id', $organizer_id, 'mec_organizer', true);
        return $organizer_id;
    }

    public function get_location_data($location_id)
    {
        $term = get_term($location_id);
        if(!isset($term->term_id) or $location_id == 1) return array();

        return array(
            'id'=>$term->term_id,
            'name'=>$term->name,
            'address'=>get_metadata('term', $term->term_id, 'address', true),
            'latitude'=>get_metadata('term', $term->term_id, 'latitude', true),
            'longitude'=>get_metadata('term', $term->term_id, 'longitude', true),
            'url'=>get_metadata('term', $term->term_id, 'url', true),
            'thumbnail'=>get_metadata('term', $term->term_id, 'thumbnail', true)
        );
    }

    public function get_organizer_data($organizer_id)
    {
        $term = get_term($organizer_id);
        if(!isset($term->term_id) or $organizer_id == 1) return array();

        return array(
            'id'=>$term->term_id,
            'name'=>$term->name,
            'tel'=>get_metadata('term', $term->term_id, 'tel', true),
            'email'=>get_metadata('term', $term->term_id, 'email', true),
            'url'=>get_metadata('term', $term->term_id, 'url', true),
            'thumbnail'=>get_metadata('term', $term->term_id, 'thumbnail', true)
        );
    }

    public function is_uncategorized($term_id)
    {
        $term = get_term($term_id);
        $name = strtolower($term->name);

        return ($name === 'uncategorized' or $name === __('Uncategorized'));
    }

    public function get_thankyou_page_id($event_id = NULL)
    {
        // Global Settings
        $settings = $this->get_settings();

        // Global Thank You Page
        $thankyou_page_id = (isset($settings['booking_thankyou_page']) and is_numeric($settings['booking_thankyou_page']) and trim($settings['booking_thankyou_page'])) ? $settings['booking_thankyou_page'] : 0;

        // Get by Event
        if($event_id)
        {
            $booking_options = get_post_meta($event_id, 'mec_booking', true);
            if(!is_array($booking_options)) $booking_options = array();

            $bookings_thankyou_page_inherit = isset($booking_options['thankyou_page_inherit']) ? $booking_options['thankyou_page_inherit'] : 1;
            if(!$bookings_thankyou_page_inherit)
            {
                if(isset($booking_options['booking_thankyou_page']) and $booking_options['booking_thankyou_page']) $thankyou_page_id = $booking_options['booking_thankyou_page'];
                else $thankyou_page_id = 0;
            }
        }

        return $thankyou_page_id;
    }

    public function get_thankyou_page_time($transaction_id = NULL)
    {
        // Global Settings
        $settings = $this->get_settings();

        // Global Time
        $thankyou_page_time = (isset($settings['booking_thankyou_page_time']) and is_numeric($settings['booking_thankyou_page_time'])) ? (int) $settings['booking_thankyou_page_time'] : 2000;

        // Get by Event
        if($transaction_id)
        {
            // Booking
            $book = $this->getBook();
            $transaction = $book->get_transaction($transaction_id);

            $event_id = (isset($transaction['event_id']) ? $transaction['event_id'] : 0);
            if($event_id)
            {
                $booking_options = get_post_meta($event_id, 'mec_booking', true);
                if(!is_array($booking_options)) $booking_options = array();

                $bookings_thankyou_page_inherit = isset($booking_options['thankyou_page_inherit']) ? $booking_options['thankyou_page_inherit'] : 1;
                if(!$bookings_thankyou_page_inherit)
                {
                    if(isset($booking_options['booking_thankyou_page_time']) and $booking_options['booking_thankyou_page_time']) $thankyou_page_time = (int) $booking_options['booking_thankyou_page_time'];
                }
            }
        }

        return max($thankyou_page_time, 0);
    }

    public function is_first_occurrence_passed($event)
    {
        // Event ID
        if(is_numeric($event)) $event_id = $event;
        // Event Object
        else $event_id = $event->ID;

        $now = current_time('timestamp', 0);

        $db = $this->getDB();
        $first = $db->select("SELECT `tstart` FROM `#__mec_dates` WHERE `post_id`='".$event_id."' ORDER BY `tstart` ASC LIMIT 1", 'loadResult');

        return ($first and $first < $now);
    }

    public function preview()
    {
        // Elementor
        if(isset($_GET['action']) and $_GET['action'] === 'elementor') return true;

        // Default
        return false;
    }
}