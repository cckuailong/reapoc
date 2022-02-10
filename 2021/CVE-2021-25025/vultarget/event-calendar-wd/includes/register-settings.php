<?php

/**
 * Register all settings needed for the Settings API.
 *
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}


if (isset($_GET[ECWD_PLUGIN_PREFIX . '_clear_cache']) && $_GET[ECWD_PLUGIN_PREFIX . '_clear_cache'] == 1) {
  $cpt = ECWD_Cpt::get_instance();
  add_action('admin_init', array($cpt, 'ecwd_clear_cache_option'));
}

if (isset($_GET[ECWD_PLUGIN_PREFIX . '_reset_script_key']) && $_GET[ECWD_PLUGIN_PREFIX . '_reset_script_key'] == 1) {
  ECWD::scripts_key(true);
  wp_redirect(ECWD_MENU_SLUG . '&page=ecwd_general_settings');
}

//if (isset($_GET['ecwd_start_tour']) && $_GET['ecwd_start_tour'] == 1) {
//    delete_user_meta(get_current_user_id(), 'ecwd_calendar_tour');
//    wp_redirect('edit.php?post_type=ecwd_calendar');
//}

//if (isset($_GET[ECWD_PLUGIN_PREFIX . '_clear_autogen']) && $_GET[ECWD_PLUGIN_PREFIX . '_clear_autogen'] == 1) {
//    $posts = get_option('auto_generated_posts');
//    if ($posts) {
//        $calen_id = $posts[0];
//        $venue_id = $posts[1];
//        $org_ids = $posts[2];
//        $ev_ids = $posts[3];
//        foreach ($ev_ids as $id)
//            wp_delete_post($id, true);
//        foreach ($org_ids as $id)
//            wp_delete_post($id, true);
//        wp_delete_post($venue_id, true);
//        wp_delete_post($calen_id, true);
//        delete_option('auto_generated_posts');
//        echo '<div class= "updated" ><p> ' . __('Auto generated data has been deleted.', 'ecwd') . '</p></div>';
//    } else {
//        echo '<div class= "updated" ><p> ' . __('Auto generated data has already deleted.', 'ecwd') . '</p></div>';
//    }
//}

/**
 *  Main function to register all of the plugin settings
 */
function ecwd_get_settings_params()
{

  global $ecwd_settings;
  global $ecwd_tabs;
  $date_format = get_option('date_format');


  if(!isset($date_format)){
    $date_format = "F j, Y";
  }

  $ecwd_tabs = array(
    'general' => __('General', 'event-calendar-wd'),
    'events'  => __('Events', 'event-calendar-wd'),
    'category_archive' => __('Category Page','event-calendar-wd'),
    'custom_css' => __('Custom CSS', 'event-calendar-wd'),
    'google_map' => __('Google Maps', 'event-calendar-wd'),
    'fb' => __('FB settings', 'event-calendar-wd'),
    'gcal' => __('Gcal settings', 'event-calendar-wd'),
    'ical' => __('Ical settings', 'event-calendar-wd'),
    'add_event' => __('Frontend Event Management', 'event-calendar-wd'),
    'countdown' =>  __('Countdown', 'event-calendar-wd'),
    'af' => __('Custom Fields', 'event-calendar-wd'),
    'ecwd_subscribe' => __('Subscribe','event-calendar-wd'),
    'upcoming_events' => __('Upcoming events', 'event-calendar-wd'),
    'filter_settings' => __('Filters','event-calendar-wd'),
    'export' => __('Export to GCal/ICal', 'event-calendar-wd')
  );

  $related_events_count = intval(get_option('posts_per_page'));
  if($related_events_count === 0){
    $related_events_count = 10;
  }

    $ecwd_settings = array(
        /* General Settings */

        'general' => array(
//            'toure_option' => array(
//                'id' => 'toure_option',
//                'name' => __('Start tour', 'ecwd'),
//                'desc' => __('Click to start tour.', 'ecwd'),
//                'size' => 'small-text',
//                'type' => 'link',
//                'href' => $_SERVER['REQUEST_URI'] . '&ecwd_start_tour=1'
//            ),
//            'clear_auto_gen' => array(
//                'id' => 'clear_auto_gen',
//                'name' => __('Clear auto generated data', 'ecwd'),
//                'desc' => __('Click to clear auto generated data', 'ecwd'),
//                'size' => 'small-text',
//                'type' => 'link',
//                'href' => $_SERVER['REQUEST_URI'] . '&ecwd_clear_autogen=1'
//            ),
          'ecwd_delete_past_events' => array(
            'id' => 'ecwd_delete_past_events',
            'name' => __('Delete past events', 'event-calendar-wd'),
            'desc' => '',
            'title' => 'Delete',
            'type' => 'link',
            'class'=>'ecwd_delete_past_events',
            'href' => '#ecwd_past_event_list_popup'
          ),
            'time_zone' => array(
                'id' => 'time_zone',
                'name' => __('TimeZone', 'event-calendar-wd'),
                'desc' => __('Use Continent/City format, e.g. Europe/Berlin. If left empty, the server timezone will be used (if set in php settings), otherwise default Europe/Berlin.  <a target="_blank" href="http://php.net/manual/en/timezones.php">PHP Timezones</a>', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'text',
                'default'   => ECWD::get_default_timezone()
            ),
            'show_time_zone' => array(
                'id' => 'show_time_zone',
                'name' => __('Show TimeZone in event', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'checkbox'
            ),
   /*   'date_format' => array(
                'id' => 'date_format',
                'name' => __('Date format', 'event-calendar-wd'),
                'desc' => __('Set the format for displaying event dates. Ex Y-m-d or Y/m/d', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'text'
      ),*/


      'date_format' => array(
        'id' => 'date_format',
        'name' =>  __('Date format', 'event-calendar-wd'),
        'desc' => __('Set the format for displaying event dates. Ex Y-m-d or Y/m/d', 'event-calendar-wd'),
        'type' => 'custom_date_radio',
        'default' => 'd/m/Y',
        'labels' => array('F j, Y', 'Y-m-d','m/d/Y','d/m/Y', 'custom')
            ),
            'time_format' => array(
                'id' => 'time_format',
                'name' => __('Time format', 'event-calendar-wd'),
                'desc' => __('Set the format for displaying event time. Ex H:i or H/i', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'text'
            ),
            'time_type' => array(
                'id' => 'time_type',
                'name' => __('Show AM/PM', 'event-calendar-wd'),
                'desc' => __('Select the time format type', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'time_type_select'
            ),
      /*xxxx*/
 /*     'list_date_format' => array(
                'id' => 'list_date_format',
                'name' => __('List,Week,Day views day format', 'event-calendar-wd'),
                'desc' => __('Note: Changed date format will not be translatable', 'event-calendar-wd'),
                'default' => 'd.F.l',
                'size' => 'medium-text',
                'type' => 'text'
      ),*/


      'list_date_format' => array(
        'id' => 'list_date_format',
        'name' =>  __('List,Week,Day views day format', 'event-calendar-wd'),
        'desc' => __('Note: Changed date format will not be translatable', 'event-calendar-wd'),
        'type' => 'custom_date_radio',
        'default' => $date_format,
        'labels' => array('F j, Y', 'Y-m-d','m/d/Y','d/m/Y', 'custom')
            ),
            'week_starts' => array(
                'id' => 'week_starts',
                'name' => __('Week start day', 'event-calendar-wd'),
                'desc' => __('Define the starting day for the week.', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'week_select'
            ),
            'enable_rewrite' => array(
                'id' => 'enable_rewrite',
                'name' => __('Enable rewrite', 'event-calendar-wd'),
                'default' => 'events',
                'desc' => __('Check yes to enable event(s) url rewrite rule.', 'event-calendar-wd'),
                'type' => 'radio',
                'default' => 1
            ),
            'cpt_order' => array(
                'id' => 'cpt_order',
                'name' => __('Order of Organizers and Venues by', 'event-calendar-wd'),
                'desc' => __('Select Order of Organizers and Venues.', 'event-calendar-wd'),
                'type' => 'order_select'
            ),
            'social_icons' => array(
                'id' => 'social_icons',
                'name' => __('Enable Social Icons', 'event-calendar-wd'),
                'desc' => __('Check to display social icons in event, organizer and venue pages.', 'event-calendar-wd'),
                'type' => 'checkbox'
            ),
            'cat_title_color' => array(
                'id' => 'cat_title_color',
                'name' => __('Apply category color to event title in event page', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 0
            ),
            'move_first_image' => array(
                'id' => 'move_first_image',
                'name' => __('Grab the first post image', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 1
            ),
            'event_description_max_length' => array(
                'id' => 'event_description_max_length',
                'name' => __('Event description max length.', 'event-calendar-wd'),
                'desc' => __('Event description max length.', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'text'
            ),
            'uninstall' => array(
              'id' => 'uninstall',
              'name' => __('Uninstall', 'event-calendar-wd'),
              'desc' => '',
              'size' => 'small-text',
              'type' => 'link',
              'href' => 'admin.php?page=ecwd_uninstall'
            ),
        ),
        'events' => array(
            'event_count_per_cell' => array(
              'id' => 'event_count_per_cell',
              'name' => __('Visible event count per day', 'event-calendar-wd'),
              'desc' => __('The count of visible events per day in "month view".', 'event-calendar-wd'),
              'size' => 'medium-text',
              'type' => 'text',
              'default' => '3'
            ),
          'category_and_tags' => array(
            'id' => 'category_and_tags',
            'name' => __('Enable Category and Tags', 'event-calendar-wd'),
            'desc' => __('Check to display category and Tags.', 'event-calendar-wd'),
            'type' => 'checkbox'
          ),
            'events_archive_page_order' => array(
                'id' => 'events_archive_page_order',
                'name' => __('Order of events archive page', 'event-calendar-wd'),
                'desc' => __('Sort by event start', 'event-calendar-wd'),
                'type' => 'custom_radio',
                'default' => '0',
                'labels' => array('DESC', 'ASC')
            ),
            'change_events_archive_page_post_date' => array(
              'id' => 'change_events_archive_page_post_date',
              'name' => __('In Events Archive page change post date to event start date', 'event-calendar-wd'),
              'desc' => '',
              'type' => 'radio',
              'default' => 0
            ),
            'enable_sidebar_in_event' => array(
                'id' => 'enable_sidebar_in_event',
                'name' => __('Enable sidebar in event page', 'event-calendar-wd'),
                'desc' =>'',
                'type' => 'checkbox'
            ),
            'event_default_description' => array(
                'id' => 'event_default_description',
                'name' => __('Description for events.', 'event-calendar-wd'),
                'default' => __('No additional detail for this event.', 'event-calendar-wd'),
                'desc' => __('Define the default text for empty events description.', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'text'
            ),
            'events_date' => array(
                'id' => 'events_date',
                'name' => __('Show event date in the events list page', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 0
            ),
            'long_events' => array(
                'id' => 'long_events',
                'name' => __('Mark all days of multi-day event', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 0
            ),
            'related_events_count' => array(
                'id' => 'related_events_count',
                'name' => __('Related events count', 'event-calendar-wd'),
                'desc' => __('empty for all events','event-calendar-wd'),
                'type' => 'text',
        'default' => $related_events_count
            ),
            'events_in_popup' => array(
                'id' => 'events_in_popup',
                'name' => __('Display Events in popup', 'event-calendar-wd'),
                'desc' => __('Check to display events in popup.', 'event-calendar-wd'),
                'type' => 'checkbox'
            ),
            'events_slug' => array(
                'id' => 'events_slug',
                'name' => __('Events slug', 'event-calendar-wd'),
                'default' => 'events',
                'desc' => __('Define the slug for the events list page.', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'text'
            ),
            'event_slug' => array(
                'id' => 'event_slug',
                'name' => __('Single Event slug', 'event-calendar-wd'),
                'default' => 'event',
                'desc' => __('Define the slug for the single event page.', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'text'
            ),
            'event_comments' => array(
                'id' => 'event_comments',
                'name' => __('Enable comments for events', 'event-calendar-wd'),
                'desc' => __('Check to enable commenting.', 'event-calendar-wd'),
                'type' => 'checkbox'
            ),
            'event_loop' => array(
                'id' => 'event_loop',
                'name' => __('Include events in main loop', 'event-calendar-wd'),
                'desc' => __('Check to display events within website post list in main pages.', 'event-calendar-wd'),
                'type' => 'checkbox'
            ),
            'show_events_detail' => array(
                'id' => 'show_events_detail',
                'name' => __('Show events detail on hover', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 1
            ),
            'events_new_tab' => array(
                'id' => 'events_new_tab',
                'name' => __('Open events in new tab', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 0
            ),
            'related_events' => array(
                'id' => 'related_events',
                'name' => __('Show related events in the event page', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 1
            ),
            'hide_old_events' => array(
              'id' => 'hide_old_events',
              'name' => __('Do not show past events', 'event-calendar-wd'),
              'desc' => '',
              'type' => 'radio',
              'default' => 0
            ),
            'use_custom_template' => array(
                'id' => 'use_custom_template',
                'name' => __('Use custom template', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 0
            ),
        ),
        'category_archive' => array(
            'category_archive_slug' => array(
                'id' => 'category_archive_slug',
                'name' => __('Events category slug', 'event-calendar-wd'),
                'default' => 'event_category',
                'desc' => __('Note: Please do not use default slugs such as "category"','event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'text'
            ),
            'ecwd_category_archive_template' => array(
                'id' => 'ecwd_category_archive_template',
                'name' => __('Add Event category archive template', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 0
            ),
            'category_archive_description' => array(
                'id' => 'category_archive_description',
                'name' => __('Display Description', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 1
            ),
            'category_archive_image' => array(
                'id' => 'category_archive_image',
                'name' => __('Display Image', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 1
            ),
           'category_archive_template_part_slug' => array(
              'id' => 'category_archive_template_part_slug',
              'name' => __('Template part slug', 'event-calendar-wd'),
              'desc' => __('The slug name for the generic template.', 'event-calendar-wd'),
              'size' => 'medium-text',
              'type' => 'text'
            ),
            'category_archive_template_part_name' => array(
              'id' => 'category_archive_template_part_name',
              'name' => __('Template part name', 'event-calendar-wd'),
              'desc' => __('The name of the specialised template.', 'event-calendar-wd'),
              'size' => 'medium-text',
              'type' => 'text'
            )
        ),
        'custom_css' => array(
            'custom_css' => array(
                'id' => 'custom_css',
                'name' => __('Custom css', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'textarea',
                'cols' => '45',
                'rows' => '15'
            )
          ),
          'google_map' => array(
            'add_project' => array(
                'id' => 'add_project',
                'name' => __('Get key', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'link',
                'href' => 'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend,places_backend&keyType=CLIENT_SIDE&reusekey=true',
                'target' => '_blank'
            ),
            'gmap_key' => array(
                'id' => 'gmap_key',
                'name' => __('API key', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'text',                
            ),
            'gmap_style' => array(
              'id' => 'gmap_style',
              'name' => __('Map style', 'event-calendar-wd'),
              'desc' => '',
              'type' => 'textarea',
              'cols' => '45',
              'rows' => '15'
            )
        )
    );
    
    
    /*disabled options*/
    
    $ecwd_disabled_settings = array(
      /* General Settings */
        'general' => array(                                                            
            'show_repeat_rate' => array(
                'id' => 'show_repeat_rate',
                'name' => __('Show the repeat rate', 'event-calendar-wd'),
                'desc' => __('Check to show the repeat rate in event page .', 'event-calendar-wd'),
                'type' => 'checkbox'
            ),            
            'posterboard_fixed_height' => array(
                'id' => 'posterboard_fixed_height',
                'name' => __('Add fixed height for events in posterboard view', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 0
            ),                                    
            'period_for_list' => array(
                'id' => 'period_for_list',
                'name' => __('Period for List view', 'event-calendar-wd'),
                'desc' => __('Period for showing events', 'event-calendar-wd'),
                'size' => 'medium-text',
                'type' => 'agenda_select'
            ),                        
        ),             
        'google_map' => array(         
          'gmap_type' => array(
                'id' => 'gmap_type',
                'name' => __('Satellite Gmap Type', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 0
            ),
          'gmap_marker_click' => array(
                'id' => 'gmap_marker_click',
                'name' => __('Open Google Maps when Marker is clicked', 'event-calendar-wd'),
                'desc' => '',
                'type' => 'radio',
                'default' => 0
            ),
        )
    );


  $config = get_option('ecwd_config');
  if($config == "on") {
    $ecwd_settings['general']['ecwd_reset_script_key'] = array(
      'id' => 'ecwd_reset_script_key',
      'name' => __('Rest script key', 'event-calendar-wd'),
      'desc' => '',
      'size' => 'small-text',
      'type' => 'link',
      'href' => $_SERVER['REQUEST_URI'] . '&ecwd_reset_script_key=1'
    );
  }

  if(1 == get_option('ecwd_old_events')) {
    $ecwd_settings['general']['show_repeat_rate'] = array(
      'id' => 'show_repeat_rate',
      'name' => __('Show the repeat rate', 'event-calendar-wd'),
      'desc' => __('Check to show the repeat rate in event page .', 'event-calendar-wd'),
      'type' => 'checkbox'
    );
  }
}

/**
 *  Main function to register all of the plugin settings
 */
function ecwd_register_settings() {
    global $ecwd_settings;
    global $ecwd_tabs;

    if($ecwd_settings === null || $ecwd_tabs === null){
      ecwd_get_settings_params();
    }


    /* If the options do not exist then create them for each section */
    if (false == get_option(ECWD_PLUGIN_PREFIX . '_settings')) {
        add_option(ECWD_PLUGIN_PREFIX . '_settings');
    }
    /* Add the  Settings sections */

//    $settings_init_on_activate = ((strpos($_SERVER['REQUEST_URI'], 'plugins.php')) && ((empty($calendar) && (get_option("activation_page_option") === false)) || isset($_POST['ecwd_settings_general']['week_starts'])));
//    if ($settings_init_on_activate) {
//        update_option("activation_page_option", "submit");
//        include_once "activation_settings_page.php";
//        ecwd_settings_init();
//    }
    foreach ($ecwd_settings as $key => $settings) {

        add_settings_section(
          ECWD_PLUGIN_PREFIX . '_settings_' . $key, $ecwd_tabs[$key], '__return_false', ECWD_PLUGIN_PREFIX . '_settings_' . $key
        );


        foreach ($settings as $option) {
            add_settings_field(
                    ECWD_PLUGIN_PREFIX . '_settings_' . $key . '[' . $option['id'] . ']', $option['name'], function_exists(ECWD_PLUGIN_PREFIX . '_' . $option['type'] . '_callback') ? ECWD_PLUGIN_PREFIX . '_' . $option['type'] . '_callback' : ECWD_PLUGIN_PREFIX . '_missing_callback', ECWD_PLUGIN_PREFIX . '_settings_' . $key, ECWD_PLUGIN_PREFIX . '_settings_' . $key, ecwd_get_settings_field_args($option, $key)
            );
        }
//        if ($settings_init_on_activate) {
//            activation_html_view();
//        }
        /* Register all settings or we will get an error when trying to save */
        register_setting(ECWD_PLUGIN_PREFIX . '_settings_' . $key, ECWD_PLUGIN_PREFIX . '_settings_' . $key, ECWD_PLUGIN_PREFIX . '_settings_sanitize');
    }


  /*disabled options*/

    $ecwd_disabled_settings = array(
      /* General Settings */
      'general' => array(
        'show_repeat_rate' => array(
          'id' => 'show_repeat_rate',
          'name' => __('Show the repeat rate', 'event-calendar-wd'),
          'desc' => __('Check to show the repeat rate in event page .', 'event-calendar-wd'),
          'type' => 'checkbox'
        ),
        'posterboard_fixed_height' => array(
          'id' => 'posterboard_fixed_height',
          'name' => __('Add fixed height for events in posterboard view', 'event-calendar-wd'),
          'desc' => '',
          'type' => 'radio',
          'default' => 0
        ),
        'period_for_list' => array(
          'id' => 'period_for_list',
          'name' => __('Period for List view', 'event-calendar-wd'),
          'desc' => __('Period for showing events', 'event-calendar-wd'),
          'size' => 'medium-text',
          'type' => 'agenda_select'
        ),
      ),
      'google_map' => array(
        'gmap_type' => array(
          'id' => 'gmap_type',
          'name' => __('Satellite Gmap Type', 'event-calendar-wd'),
          'desc' => '',
          'type' => 'radio',
          'default' => 0
        ),
        'gmap_marker_click' => array(
          'id' => 'gmap_marker_click',
          'name' => __('Open Google Maps when Marker is clicked', 'event-calendar-wd'),
          'desc' => '',
          'type' => 'radio',
          'default' => 0
        ),
      )
    );

    foreach ($ecwd_disabled_settings as $key => $settings) {        
        add_settings_section(
          ECWD_PLUGIN_PREFIX . '_settings_' . $key, $ecwd_tabs[$key], '__return_false', ECWD_PLUGIN_PREFIX . '_settings_' . $key
        );


        foreach ($settings as $option) {
          $option['disabled'] = true;
            add_settings_field(
                    ECWD_PLUGIN_PREFIX . '_settings_' . $key . '[' . $option['id'] . ']', $option['name'], function_exists(ECWD_PLUGIN_PREFIX . '_' . $option['type'] . '_callback') ? ECWD_PLUGIN_PREFIX . '_' . $option['type'] . '_callback' : ECWD_PLUGIN_PREFIX . '_missing_callback', ECWD_PLUGIN_PREFIX . '_settings_' . $key, ECWD_PLUGIN_PREFIX . '_settings_' . $key, ecwd_get_settings_field_args($option, $key)
            );
        }        
        /* Register all settings or we will get an error when trying to save */
        //register_setting(ECWD_PLUGIN_PREFIX . '_settings_' . $key, ECWD_PLUGIN_PREFIX . '_settings_' . $key, ECWD_PLUGIN_PREFIX . '_settings_sanitize');
    }            
}
add_action('admin_init', ECWD_PLUGIN_PREFIX . '_register_settings');

/*
 * Return generic add_settings_field $args parameter array.
 *
 * @param   string  $option   Single settings option key.
 * @param   string  $section  Section of settings apge.
 * @return  array             $args parameter to use with add_settings_field call.
 */

function ecwd_get_settings_field_args($option, $section) {
    $settings_args = array(
        'id' => $option['id'],
        'desc' => $option['desc'],
        'name' => $option['name'],
        'section' => $section,
        'size' => isset($option['size']) ? $option['size'] : null,
        'class' => isset($option['class']) ? $option['class'] : null,
        'options' => isset($option['options']) ? $option['options'] : '',
        'std' => isset($option['std']) ? $option['std'] : '',
        'href' => isset($option['href']) ? $option['href'] : '',
        'target' => isset($option['target']) ? $option['target'] : '',
        'default' => isset($option['default']) ? $option['default'] : '',
        'cols' => isset($option['cols']) ? $option['cols'] : '',
        'rows' => isset($option['rows']) ? $option['rows'] : '',
        'labels' => isset($option['labels']) ? $option['labels'] : array(),
        'disabled' => isset($option['disabled']) ? $option['disabled'] : false,
          'title' => isset($option['title']) ? $option['title'] : "",
    );

    // Link label to input using 'label_for' argument if text, textarea, password, select, or variations of.
    // Just add to existing settings args array if needed.
    if (in_array($option['type'], array('text', 'select', 'textarea', 'password', 'number'))) {
        $settings_args = array_merge($settings_args, array('label_for' => ECWD_PLUGIN_PREFIX . '_settings_' . $section . '[' . $option['id'] . ']'));
    }

    return $settings_args;
}

  function ecwd_button_callback($args) {
    echo "<a href='#' id='" . $args['id'] . "' class='button'>" . $args['title'] . "</a>";
  }

/*
 * Week select callback function
 */

function ecwd_week_select_callback($args) {
    global $ecwd_options;
    $html = "\n" . '<select  id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" >
        <option value="0" ' . selected(0, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>Sunday</option>
        <option value="1" ' . selected(1, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>Monday</option>
    </select>' . "\n";

    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }
    echo $html;
}

/*
 * Time type select callback function
 */

function ecwd_time_type_select_callback($args) {
    global $ecwd_options;
    $html = "\n" . '<select  id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" >
        <option value="" ' . selected("", isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>Use 24-hour format</option>
        <option value="a" ' . selected("a", isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>Use am/pm</option>
        <option value="A" ' . selected("A", isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>Use AM/PM</option>
    </select>' . "\n";

    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }

    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }
    
    echo $html;
}

/*
 * Order select callback function
 */

function ecwd_order_select_callback($args) {
    global $ecwd_options;
    $html = "\n" . '<select  id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" >
        <option value="post_name" ' . selected('post_name', isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>'.__('Name','event-calendar-wd').'</option>
        <option value="ID" ' . selected('ID', isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>ID</option>
        <option value="post_date" ' . selected('post_date', isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>'.__('Date','event-calendar-wd').'</option>
    </select>' . "\n";

    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }

    echo $html;
}

function ecwd_update_select_callback($args) {
    global $ecwd_options;
    $html = "\n" . '<select  id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" >
        <option value="1" ' . selected(1, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>1 hour</option>
        <option value="2" ' . selected(2, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>2 hours</option>
        <option value="3" ' . selected(3, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>3 hours</option>
        <option value="5" ' . selected(5, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>5 hours</option>
        <option value="12" ' . selected(12, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>12 hours</option>
    </select>' . "\n";

    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }

    echo $html;
}

function ecwd_status_select_callback($args) {
    global $ecwd_options;
    $html = "\n" . '<select  id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" >
        <option value="draft" ' . selected('draft', isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>Draft</option>
        <option value="publish" ' . selected('publish', isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>Publish</option>
        <option value="pending" ' . selected('pending', isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>Pending</option>
    </select>' . "\n";

    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }

    echo $html;
}

/*
 * Single checkbox callback function
 */

function ecwd_checkbox_callback($args) {
    global $ecwd_options;
    
    $checked = isset($ecwd_options[$args['id']]) ? checked(1, $ecwd_options[$args['id']], false) : ( isset($args['default']) ? checked(1, $args['default'], false) : '' );
    $id = 'ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']'; 
   $html = "\n" . '<div class="checkbox-div"><input type="checkbox" id="'.$id.'" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/><label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']"></label></div>' . "\n";
    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }

    echo $html;
}

/*
 * Radio callback function
 */

function ecwd_radio_callback($args) {  
    global $ecwd_options;

    $checked_no = isset($ecwd_options[$args['id']]) ? checked(0, $ecwd_options[$args['id']], false) : ( isset($args['default']) ? checked(0, $args['default'], false) : '' );

    $checked_yes = isset($ecwd_options[$args['id']]) ? checked(1, $ecwd_options[$args['id']], false) : ( isset($args['default']) ? checked(1, $args['default'], false) : '' );


    $html = "\n" . ' <div class="checkbox-div"><input type="radio" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_yes" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked_yes . '/><label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_yes"></label></div> <label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_yes">'.__('Yes','event-calendar-wd').'</label>' . "\n";
    $html .= '<div class="checkbox-div"> <input type="radio" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_no" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="0" ' . $checked_no . '/><label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_no"></label></div> <label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_no">'.__('No','event-calendar-wd').'</label>' . "\n";
    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }

    echo $html;
}

/*
 * Custom Radio callback function
 */

function ecwd_custom_radio_callback($args) {
    global $ecwd_options;

    $html = "\n";
    if (isset($ecwd_options['events_archive_page_order'])) {
        $checked_item_id = intval($ecwd_options['events_archive_page_order']);
    } else {
        if (isset($args['default'])) {
            $checked_item_id = intval($args['default']);
        } else {
            $checked_item_id = 0;
        }
    }
    foreach ($args['labels'] as $key => $label) {
        if ($checked_item_id == $key) {
            $check_text = 'checked';
        } else {
            $check_text = '';
        }
        $html .= '<div class="checkbox-div"> <input type="radio" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_' . $key . '" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . $key . '" ' . $check_text . ' /><label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_' . $key . '"></label></div> <label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_' . $key . '">' . $label . '</label>' . "\n";
    }

    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }
    
    echo $html;
}

/*
 * Multiple checkboxs callback function
 */


function ecwd_custom_date_radio_callback($args){
  global $ecwd_options;
  $date_format = get_option( 'date_format' );
  $html = '';
  if(isset($ecwd_options[$args["id"]])){
    $option_data = $ecwd_options[$args["id"]];
  }elseif (isset($args['default'])){
    $option_data = $args['default'];
  }
  $custom_date_input = "F j, Y";
  if(isset($ecwd_options['custom_'.$args['id']]) && !empty($ecwd_options['custom_'.$args['id']])){
    $custom_date_input = $ecwd_options['custom_'.$args['id']];
  }
  $is_checked = true;
  foreach ($args["labels"] as $key => $label){
    $check_text = '';
    $custom_check_text = '';

    if(((isset($option_data) && $label === $option_data) && $is_checked)){
      $check_text = 'checked';
      $is_checked = false;
    }
    $current_date = ECWD::ecwd_date($label);
    if($label === "custom"){
      if($label == end($args["labels"]) && $is_checked) {
        $custom_check_text = 'checked';
        if(isset($option_data)){
          $custom_date_input = $option_data;
        }
        elseif(isset($date_format)){
          $custom_date_input = $date_format;
        }
      }
      $current_date = "Custom";
      $html .= '<div class="ecwd_custom_date">
              <div class="checkbox-div-content">
                 <div class="checkbox-div"> 
                    <input type="radio" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_' . $key . '" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="custom" ' . $custom_check_text . ' /><label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_' . $key . '"></label>
                 </div> 
                 <label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_' . $key . '">' . $current_date . '</label>
               </div>
               <input class="ecwd_custom_date_el" type="text" name="ecwd_settings_' . $args['section'] . '[custom_'.$args['id'].']" value="'.$custom_date_input.'" >
             </div>' . "\n";
    }else{
      $html .= '<div class="ecwd_custom_date">
               <div class="checkbox-div-content">
                 <div class="checkbox-div"> 
                    <input type="radio" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_' . $key . '" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . $label . '" ' . $check_text . ' /><label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_' . $key . '"></label>
                 </div> 
                 <label for="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']_' . $key . '">' . $current_date . '</label>
               </div>
               <code class="ecwd_custom_date_el">'.$label.'</code> 
             </div>' . "\n";
    }



  }
  $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
  echo $html;
}

function ecwd_cats_checkbox_callback($args) {
    global $ecwd_options;
    $categories = get_categories(array('taxonomy' => ECWD_PLUGIN_PREFIX . '_event_category'));
    $html = '';
    if (!empty($categories)) {
        foreach ($categories as $cat) {
            $checked = ( isset($ecwd_options[$args['id']]) && in_array($cat->term_id, $ecwd_options[$args['id']]) ) ? 'checked="checked"' : '';
            $html .= "\n" . '<div class="checkbox-div"><input type="checkbox" id="ecwd_settings_' . $args['section'] . '_' . $args['id'] . '[' . $cat->term_id . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . '][]" value="' . $cat->term_id . '" ' . $checked . '/><label for="ecwd_settings_' . $args['section'] . '_' . $args['id'] . '[' . $cat->term_id . ']"></label></div><label for="ecwd_settings_' . $args['section'] . '_' . $args['id'] . '[' . $cat->term_id . ']">' . $cat->name . '</label>' . "\n";
        }
    }
    //$html = "\n" . '<input type="checkbox" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>' . "\n";
    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }

    echo $html;
}

/**
 * Textbox callback function
 * Valid built-in size CSS class values:
 * small-text, regular-text, large-text
 *
 */
function ecwd_text_callback($args) {
    global $ecwd_options;

    if (isset($ecwd_options[$args['id']])) {
        $value = $ecwd_options[$args['id']];
    } else {
        $value = isset($args['default']) ? $args['default'] : '';
    }

    $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : '';
    $html = "\n" . '<input type="text" class="' . $size . '" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr($value) . '"/>' . "\n";

    // Render and style description text underneath if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }

    echo $html;
}

function ecwd_textarea_callback($args) {
    global $ecwd_options;

    if (isset($ecwd_options[$args['id']])) {
        $value = $ecwd_options[$args['id']];
    } else {
        $value = isset($args['default']) ? $args['default'] : '';
    }

    $rows = ( isset($args['rows']) && !is_null($args['rows']) ) ? 'rows="' . $args['rows'] . '"' : '';
    $cols = ( isset($args['cols']) && !is_null($args['cols']) ) ? 'cols="' . $args['cols'] . '"' : '';
    $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : '';
    $html = "\n" . '<textarea type="text" ' . $rows . ' ' . $cols . ' class="' . $size . '" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_attr($value) . '</textarea>' . "\n";

    // Render and style description text underneath if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }

    echo $html;
}

/**
 * Button callback function
 * Valid built-in size CSS class values:
 * small-text, regular-text, large-text
 *
 */
function ecwd_link_callback($args) {
    global $ecwd_options;

    $value = isset($args['name']) ? $args['name'] : '';
    $href = isset($args['href']) ? $args['href'] : '#';
    $target = isset($args['target']) ? $args['target'] : '';    
    $html = "\n" . '<a target="'.$target.'" class="button" href="' . $href . '" id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']"  >' . esc_attr($value) . '</a>' . "\n";
    // Render and style description text underneath if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }

    echo $html;
}

function ecwd_agenda_select_callback($args) {
    global $ecwd_options;
    $html = "\n" . '<select  id="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ecwd_settings_' . $args['section'] . '[' . $args['id'] . ']" >
        <option value="1" ' . selected(1, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>1 month</option>
        <option value="2" ' . selected(2, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>2 months</option>
        <option value="3" ' . selected(3, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>3 months</option>
        <option value="4" ' . selected(4, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>4 months</option>
        <option value="5" ' . selected(5, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>5 months</option>
        <option value="6" ' . selected(6, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>6 months</option>
        <option value="12" ' . selected(12, isset($ecwd_options[$args['id']]) ? $ecwd_options[$args['id']] : '', false) . '>1 year</option>
    </select>' . "\n";

    // Render description text directly to the right in a label if it exists.
    if (!empty($args['desc'])) {
        $html .= '<p class="description">' . $args['desc'] . '</p>' . "\n";
    }
    
    if($args['disabled']){
      $html .= '<p class="ecwd_disabled_text">'.__('This option is disabled in free version.','event-calendar-wd').'</p>';
      $html .= '<input type="hidden" value="1" class="ecwd_disabled_option" />';
    }
    
    echo $html;
}

/*
 * Function we can use to sanitize the input data and return it when saving options
 * 
 */

function ecwd_settings_sanitize($input) {
    //add_settings_error( 'ecwd-notices', '', '', '' );
    return $input;
}

/*
 *  Default callback function if correct one does not exist
 * 
 */

function ecwd_missing_callback($args) {
    printf(__('The callback function used for the <strong>%s</strong> setting is missing.', 'event-calendar-wd'), $args['id']);
}

/*
 * Function used to return an array of all of the plugin settings
 * 
 */

function ecwd_get_settings() {
    $ecwd_tabs = array(
        'general' => __('General','event-calendar-wd'),
        'events'  => __('Events','event-calendar-wd'),
        'category_archive' => __('Category Page','event-calendar-wd'),
        'custom_css' => __('Custom CSS','event-calendar-wd'),
        'google_map' => __('Google Maps','event-calendar-wd'),
        'fb' => __('FB settings','event-calendar-wd'),
        'gcal' => __('Gcal settings','event-calendar-wd'),
        'ical' => __('Ical settings','event-calendar-wd'),
        'add_event' => __('Add Event','event-calendar-wd')
    );


    ecwd_get_settings_params();
    global $ecwd_settings;
    // Set default settings
    // If this is the first time running we need to set the defaults
    if (!get_option(ECWD_PLUGIN_PREFIX . '_upgrade_has_run')) {

        $general = get_option(ECWD_PLUGIN_PREFIX . '_settings_general');
        $general['save_settings'] = 1;

        update_option(ECWD_PLUGIN_PREFIX . '_settings_general', $general);
    }

  $general_settings = array();
  foreach($ecwd_tabs as $key => $settings) {

    $options = get_option(ECWD_PLUGIN_PREFIX . '_settings_' . $key);
    if(!is_array($options)) {
      $options = array();
    }


    if(isset($ecwd_settings[$key])) {

      foreach($ecwd_settings[$key] as $i => $setting) {

        if(isset($options[$setting['id']])) {
          continue;
        }

        switch($setting['type']) {
          case "checkbox":
            if(isset($setting['default']) && $setting['default'] == '1') {
              $options[$setting['id']] = '1';
            } else {
              $options[$setting['id']] = '0';
            }
            break;
          case "time_type_select":
            $options[$setting['id']] = '';
            break;
          case "week_select":
            $options[$setting['id']] = '0';
            break;
          case "radio":
            if(isset($setting['default']) && $setting['default'] == '1') {
              $options[$setting['id']] = '1';
            } else {
              $options[$setting['id']] = '0';
            }
            break;
          case "order_select":
            $options[$setting['id']] = 'post_name';
            break;
          case "agenda_select":
            $options[$setting['id']] = '1';
            break;
          case "custom_radio":
            if(isset($setting['default'])) {
              $options[$setting['id']] = $setting['default'];
            }else{
              $options[$setting['id']] = 0;
            }
            break;
          case "status_select"://add-event
            $options[$setting['id']] = 'draft';
            break;
          case "af_text"://custom fields
            $options[$setting['id']] = array('');
            break;
          case "cats_checkbox"://integration addons
            $options[$setting['id']] = array('');
            break;
          case "update_select"://integration addons
            $options[$setting['id']] = '1';
            break;
          case "text":
          case "textarea":
          case "number":
            $options[$setting['id']] = isset($setting['default']) ? $setting['default'] : '';
            break;
        }

      }

    }
    $general_settings += $options;
  }
  if(isset($general_settings["date_format"]) && $general_settings["date_format"]==="custom"){
    $general_settings["date_format"] = $general_settings["custom_date_format"];
  }
  if(isset($general_settings["list_date_format"]) && $general_settings["list_date_format"]==="custom"){
    $general_settings["list_date_format"] = $general_settings["custom_list_date_format"];
  }
  return $general_settings;
}
