<?php
/**
 * Tracking functions for reporting plugin usage to the Smash Balloon site for users that have opted in
 *
 * @copyright   Copyright (c) 2018, Chris Christoff
 * @since       3.13
 */
namespace CustomFacebookFeed\Admin;
use CustomFacebookFeed\CFF_Utils;
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Usage tracking
 *
 * @access public
 * @since  3.13
 * @return void
 */
class CFF_Tracking {

	public function __construct() {
		add_action( 'init', array( $this, 'schedule_send' ) );
		add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
		add_action( 'cff_usage_tracking_cron', array( $this, 'send_checkin' ) );
		add_action( 'cff_admin_notices', array( $this, 'usage_opt_in' ) );
		add_action( 'wp_ajax_cff_usage_opt_in_or_out', array( $this, 'usage_opt_in_or_out' ) );
	}

	private function normalize_and_format( $key, $value ) {
		$defaults = array(
			//Post types
			'cff_show_links_type'       => true,
			'cff_show_event_type'       => true,
			'cff_show_video_type'       => true,
			'cff_show_photos_type'      => true,
			'cff_show_status_type'      => true,
			'cff_show_albums_type'      => true,
			//Events only
			'cff_events_source'         => 'eventspage',
			'cff_event_offset'          => '6',
			'cff_event_image_size'      => 'full',
			//Albums only
			'cff_albums_source'         => 'photospage',
			'cff_show_album_title'      => true,
			'cff_show_album_number'     => true,
			'cff_album_cols'            => '4',
			//Photos only
			'cff_photos_source'         => 'photospage',
			'cff_photos_cols'           => '4',
			//Videos only
			'cff_videos_source'         => 'videospage',
			'cff_show_video_name'       => true,
			'cff_show_video_desc'       => true,
			'cff_video_cols'            => '4',

			//Lightbox
			'cff_disable_lightbox'      => false,
			'cff_lightbox_bg_color'     => '',
			'cff_lightbox_text_color'   => '',
			'cff_lightbox_link_color'   => '',

			//Filter
			'cff_filter_string'         => '',
			'cff_exclude_string'        => '',

			//Reviews
			'cff_reviews_rated_5'       => true,
			'cff_reviews_rated_4'       => true,
			'cff_reviews_rated_3'       => true,
			'cff_reviews_rated_2'       => true,
			'cff_reviews_rated_1'       => true,
			'cff_star_size'             => '12',
			'cff_reviews_link_text'     => 'View all Reviews',
			'cff_reviews_no_text'       => false,
			'cff_reviews_method'        => 'auto',
			'cff_reviews_hide_negative' => true,

			//Layout
			'cff_preset_layout'         => 'thumb',
			'cff_media_position'        => 'below',
			//Include
			'cff_show_text'             => true,
			'cff_show_desc'             => true,
			'cff_show_shared_links'     => true,
			'cff_show_date'             => true,
			'cff_show_media'            => true,
			'cff_show_event_title'      => true,
			'cff_show_event_details'    => true,
			'cff_show_meta'             => true,
			'cff_show_link'             => true,
			'cff_show_like_box'         => true,
			//Masonry
			'cff_masonry_enabled'       => false,
			'cff_masonry_desktop_col'   => 1,
			'cff_masonry_mobile_col'    => 1,

			//Post Styple
			'cff_post_style'            => '',
			'cff_post_bg_color'         => '',
			'cff_post_rounded'          => '0',
			'cff_box_shadow'            => false,
			//Typography
			'cff_title_format'          => 'p',
			'cff_title_size'            => 'inherit',
			'cff_title_weight'          => 'inherit',
			'cff_title_color'           => '',
			'cff_posttext_link_color'   => '',
			'cff_body_size'             => '12',
			'cff_body_weight'           => 'inherit',
			'cff_body_color'            => '',
			'cff_link_title_format'     => 'p',
			'cff_full_link_images'      => true,
			'cff_link_image_size'       => 'largesquare',
			'cff_image_size'            => 'large',
			'cff_link_title_size'       => 'inherit',
			'cff_link_url_size'         => '12',
			'cff_link_desc_size'        => 'inherit',
			'cff_link_desc_color'       => '',
			'cff_link_title_color'      => '',
			'cff_link_url_color'        => '',
			'cff_link_bg_color'         => '',
			'cff_link_border_color'     => '',
			'cff_disable_link_box'      => false,

			//Event title
			'cff_event_title_format'    => 'p',
			'cff_event_title_size'      => 'inherit',
			'cff_event_title_weight'    => 'bold',
			'cff_event_title_color'     => '',
			//Event date
			'cff_event_date_size'       => 'inherit',
			'cff_event_date_weight'     => 'inherit',
			'cff_event_date_color'      => '',
			'cff_event_date_position'   => 'below',
			'cff_event_date_formatting' => '14',
			'cff_event_date_custom'     => '',
			//Event details
			'cff_event_details_size'    => 'inherit',
			'cff_event_details_weight'  => 'inherit',
			'cff_event_details_color'   => '',
			'cff_event_link_color'      => '',

			//Date
			'cff_date_position'         => 'author',
			'cff_date_size'             => 'inherit',
			'cff_date_weight'           => 'inherit',
			'cff_date_color'            => '',
			'cff_date_formatting'       => '1',
			'cff_date_custom'           => '',
			'cff_date_before'           => '',
			'cff_date_after'            => '',
			'cff_timezone'              => 'America/Chicago',

			//Link to Facebook
			'cff_link_size'             => 'inherit',
			'cff_link_weight'           => 'inherit',
			'cff_link_color'            => '',
			'cff_view_link_text'        => 'View Link',
			'cff_link_to_timeline'      => false,

			//Load more button
			// 'cff_load_more'             => true,
			'cff_load_more_bg'          => '',
			'cff_load_more_text_color'  => '',
			'cff_load_more_bg_hover'    => '',
			'cff_load_more_text'        => 'Load more',
			'cff_no_more_posts_text'    => 'No more posts',

			//Meta
			'cff_icon_style'            => 'light',
			'cff_meta_text_color'       => '',
			'cff_meta_link_color'       => '',
			'cff_meta_bg_color'         => '',
			'cff_expand_comments'       => false,
			'cff_comments_num'          => '4',
			'cff_nocomments_text'       => 'No comments yet',
			'cff_hide_comments'         => false,
			'cff_hide_comment_avatars'  => false,
			'cff_lightbox_comments'     => true,
			//Misc
			'cff_feed_width'            => '100%',
			'cff_feed_width_resp'       => false,
			'cff_feed_height'           => '',
			'cff_feed_padding'          => '',
			'cff_like_box_position'     => 'bottom',
			'cff_like_box_outside'      => false,
			'cff_likebox_width'         => '',
			'cff_likebox_height'        => '',
			'cff_like_box_faces'        => false,
			'cff_like_box_border'       => false,
			'cff_like_box_cover'        => true,
			'cff_like_box_small_header' => false,
			'cff_like_box_hide_cta'     => false,

			//Misc Settings
			'cff_enable_narrow'         => true,
			'cff_one_image'             => false,

			'cff_bg_color'              => '',
			'cff_likebox_bg_color'      => '',
			'cff_like_box_text_color'   => 'blue',
			'cff_video_height'          => '',
			'cff_show_author'           => true,
			'cff_class'                 => '',
			'cff_app_id'                => '',
			'cff_show_credit'           => '',
			'cff_format_issue'          => false,
			'cff_disable_svgs'          => false,
			'cff_restricted_page'       => false,
			'cff_hide_supporter_posts'   => false,
			'cff_font_source'           => 'cdn',
			'cff_disable_ajax_cache'    => false,
			'cff_minify'                => false,
			'disable_admin_notice'      => false,
			'cff_request_method'        => 'auto',
			'cff_cron'                  => 'unset',
			'cff_timeline_pag'          => 'date',
			'cff_grid_pag'              => 'auto',

			//Feed Header
			'cff_show_header'           => '',
			'cff_header_outside'        => false,
			'cff_header_text'           => 'Facebook Posts',
			'cff_header_bg_color'       => '',
			'cff_header_padding'        => '',
			'cff_header_text_size'      => '',
			'cff_header_text_weight'    => '',
			'cff_header_text_color'     => '',
			'cff_header_icon'           => '',
			'cff_header_icon_color'     => '',
			'cff_header_icon_size'      => '28',

			//Author
			'cff_author_size'           => 'inherit',
			'cff_author_color'          => '',

			//New
			'cff_custom_css'            => '',
			'cff_custom_js'             => '',
			'cff_title_link'            => false,
			'cff_post_tags'             => true,
			'cff_link_hashtags'         => true,
			'cff_event_title_link'      => true,
			'cff_video_action'          => 'post',
			'cff_video_player'          => 'facebook',
			'cff_sep_color'             => '',
			'cff_sep_size'              => '1',

			//Translate - general
			'cff_see_more_text'         => 'See More',
			'cff_see_less_text'         => 'See Less',
			'cff_map_text'              => 'Map',
			'cff_no_events_text'        => 'No upcoming events',
			'cff_facebook_link_text'    => 'View on Facebook',
			'cff_facebook_share_text'   => 'Share',
			'cff_show_facebook_link'    => true,
			'cff_show_facebook_share'   => true,
			'cff_buy_tickets_text'      => 'Buy Tickets',
			'cff_interested_text'       => 'interested',
			'cff_going_text'            => 'going',

			//Translate - social
			'cff_translate_view_previous_comments_text'     => 'View more comments',
			'cff_translate_comment_on_facebook_text'        => 'Comment on Facebook',
			'cff_translate_photos_text'                     => 'photos',
			'cff_translate_likes_this_text'                 => 'likes this',
			'cff_translate_like_this_text'                  => 'like this',
			'cff_translate_reacted_text'                    => 'reacted to this',
			'cff_translate_and_text'                        => 'and',
			'cff_translate_other_text'                      => 'other',
			'cff_translate_others_text'                     => 'others',
			'cff_translate_reply_text'                      => 'Reply',
			'cff_translate_replies_text'                    => 'Replies',

			'cff_translate_learn_more_text' => 'Learn More',
			'cff_translate_shop_now_text'   => 'Shop Now',
			'cff_translate_message_page_text' => 'Message Page',
			'cff_translate_get_directions_text' => 'Get Directions',

			//Translate - date
			'cff_translate_second'      => 'second',
			'cff_translate_seconds'     => 'seconds',
			'cff_translate_minute'      => 'minute',
			'cff_translate_minutes'     => 'minutes',
			'cff_translate_hour'        => 'hour',
			'cff_translate_hours'       => 'hours',
			'cff_translate_day'         => 'day',
			'cff_translate_days'        => 'days',
			'cff_translate_week'        => 'week',
			'cff_translate_weeks'       => 'weeks',
			'cff_translate_month'       => 'month',
			'cff_translate_months'      => 'months',
			'cff_translate_year'        => 'year',
			'cff_translate_years'       => 'years',
			'cff_translate_ago'         => 'ago',

			// email
			'enable_email_report' => 'on',
			'email_notification' => 'monday',
			'email_notification_addresses' => get_option( 'admin_email' )
		);
		
		$normal_bools = array(
			'cff_show_links_type',
			'cff_show_event_type',
			'cff_show_video_type',
			'cff_show_photos_type',
			'cff_show_status_type',
			'cff_show_albums_type',
			'cff_show_album_title',
			'cff_show_album_number',
			'cff_show_video_name',
			'cff_show_video_desc',
			'cff_disable_lightbox',
			'cff_reviews_rated_5',
			'cff_reviews_rated_4',
			'cff_reviews_rated_3',
			'cff_reviews_rated_2',
			'cff_reviews_rated_1',
			'cff_reviews_no_text',
			'cff_reviews_hide_negative',
			'cff_show_text',
			'cff_show_desc',
			'cff_show_shared_links',
			'cff_show_date',
			'cff_show_media',
			'cff_show_event_title',
			'cff_show_event_details',
			'cff_show_meta',
			'cff_show_link',
			'cff_show_like_box',
			'cff_masonry_enabled',
			'cff_box_shadow',
			'cff_full_link_images',
			'cff_link_to_timeline',
			'cff_expand_comments',
			'cff_hide_comments',
			'cff_hide_comment_avatars',
			'cff_lightbox_comments',
			'cff_disable_link_box',
			//Misc
			'cff_feed_width',
			'cff_feed_width_resp',
			'cff_like_box_outside',
			'cff_like_box_faces',
			'cff_like_box_border',
			'cff_like_box_cover',
			'cff_like_box_small_header',
			'cff_like_box_hide_cta',
			'cff_disable_styles',
			'cff_enable_narrow',
			'cff_one_image',
			'cff_show_author',
			'cff_format_issue',
			'cff_disable_svgs',
			'cff_restricted_page',
			'cff_hide_supporter_posts',
			'cff_disable_ajax_cache',
			'cff_minify',
			'disable_admin_notice',
			'cff_header_outside',
			'cff_title_link',
			'cff_post_tags',
			'cff_link_hashtags',
			'cff_event_title_link',
			'cff_show_facebook_link',
			'cff_show_facebook_share',
			'enable_email_report'
		);
		$custom_text_settings = array(
			'cff_reviews_link_text',
			'cff_load_more_text',
			'cff_no_more_posts_text',
			'cff_nocomments_text',
			'cff_header_text',
			'cff_see_more_text',
			'cff_see_less_text',
			'cff_map_text',
			'cff_no_events_text',
			'cff_facebook_link_text',
			'cff_facebook_share_text',
			'cff_buy_tickets_text',
			'cff_interested_text',
			'cff_going_text',
			'cff_translate_view_previous_comments_text',
			'cff_translate_comment_on_facebook_text',
			'cff_translate_photos_text',
			'cff_translate_likes_this_text',
			'cff_translate_like_this_text',
			'cff_translate_reacted_text',
			'cff_translate_and_text',
			'cff_translate_other_text',
			'cff_translate_others_text',
			'cff_translate_reply_text',
			'cff_translate_replies_text',
			'cff_custom_css',
			'cff_custom_js',
			'cff_translate_learn_more_text',
			'cff_translate_shop_now_text',
			'cff_translate_message_page_text',
			'cff_translate_get_directions_text',
			'cff_class',
			'cff_app_id',
			'cff_show_credit',

			//Translate - date
			'cff_translate_second',
			'cff_translate_seconds',
			'cff_translate_minute',
			'cff_translate_minutes',
			'cff_translate_hour',
			'cff_translate_hours',
			'cff_translate_day',
			'cff_translate_days',
			'cff_translate_week',
			'cff_translate_weeks',
			'cff_translate_month',
			'cff_translate_months',
			'cff_translate_year',
			'cff_translate_years',
			'cff_translate_ago',
			'email_notification_addresses'
		);
		$comma_separate_counts_settings = array(
			'cff_filter_string',
			'cff_exclude_string',
		);

		if ( is_array( $value ) ) {
			if ( empty( $value ) ) {
				return 0;
			}
			return count( $value );
			// 0 for anything that might be false, 1 for everything else
		} elseif ( in_array( $key, $normal_bools, true ) ) {
			if ( in_array( $value, array( false, 0, '0', 'false', '' ), true ) ) {
				return 0;
			}
			return 1;

			// if a custom text setting, we just want to know if it's different than the default
		} elseif ( in_array( $key, $custom_text_settings, true ) ) {
			if ( $defaults[ $key ] === $value ) {
				return 0;
			}
			return 1;
		} elseif ( in_array( $key, $comma_separate_counts_settings, true ) ) {
			if ( str_replace( ' ', '', $value ) === '' ) {
				return 0;
			}
			$split_at_comma = explode( ',', $value );
			return count( $split_at_comma );
		}

		return $value;

	}

	private function get_data() {
		$data = array();

		// Retrieve current theme info
		$theme_data    = wp_get_theme();

		$count_b = 1;
		if ( is_multisite() ) {
			if ( function_exists( 'get_blog_count' ) ) {
				$count_b = get_blog_count();
			} else {
				$count_b = 'Not Set';
			}
		}

		$php_version = rtrim( ltrim( sanitize_text_field( phpversion() ) ) );
		$php_version = ! empty( $php_version ) ? substr( $php_version, 0, strpos( $php_version, '.', strpos( $php_version, '.' ) + 1 ) ) : phpversion();

		global $wp_version;
		$data['this_plugin'] = 'fb';
		$data['php_version']   = $php_version;
		$data['mi_version']    = CFFVER;
		$data['wp_version']    = $wp_version;
		$data['server']        = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '';
		$data['multisite']     = is_multisite();
		$data['url']           = home_url();
		$data['themename']     = $theme_data->Name;
		$data['themeversion']  = $theme_data->Version;
		$data['settings']      = array();
		$data['pro']           = CFF_Utils::cff_is_pro_version() ? '1' : '';
		$data['sites']         = $count_b;
		$data['usagetracking'] = get_option( 'cff_usage_tracking_config', false );
		$num_users = function_exists( 'count_users' ) ? count_users() : 'Not Set';
		$data['usercount']     = is_array( $num_users ) ? $num_users['total_users'] : 1;
		$data['timezoneoffset']= date('P');

		$page_id = get_option( 'cff_page_id' );
		$own_token = get_option( 'cff_show_access_token' );
		$by_others = get_option( 'cff_show_others' );
		$number_posts = get_option( 'cff_num_show' );
		$posts_limit = get_option( 'cff_post_limit' );
		$page_type = get_option( 'cff_page_type' );
		$caching_type = get_option( 'cff_caching_type' );
		$caching_time = get_option( 'cff_cache_time' );
		$caching_unit = get_option( 'cff_cache_time_unit' );
		$locale = get_option( 'cff_locale' );
		$connected_accounts = get_option( 'cff_connected_accounts', '{}' );
		$connected_accounts = json_decode( stripslashes( $connected_accounts ), true );
		$settings_to_send = array(
			'page_id' => $page_id,
			'own_token' => $own_token,
			'show_others' => $by_others,
			'num_posts' => $number_posts,
			'posts_limit' => $posts_limit,
			'page_type' => $page_type,
			'caching_type' => $caching_type,
			'caching_time' => $caching_time,
			'caching_unit' => $caching_unit,
			'locale' => $locale,
			'num_connected_accounts' => count( $connected_accounts ),
		);
		$raw_settings = get_option( 'cff_style_settings', array() );
		foreach ( $raw_settings as $key => $value ) {
			$value = $this->normalize_and_format( $key, $value );

			if ( $value !== false ) {
				$key = str_replace( array( 'sb_instagram_', 'cff_' ), '', $key );
				$settings_to_send[ $key ] = $value;
			}
		}

		$oembed_token = get_option( 'cff_oembed_token', false );

		$settings_to_send['oembed_expiring_token'] = isset( $oembed_token['access_token'] ) ? (int)$oembed_token['access_token'] > 0 : false;
		
		global $wpdb;
		$feed_caches = array();

		$results = $wpdb->get_results( "
		SELECT option_name
        FROM $wpdb->options
        WHERE `option_name` LIKE ('%\_transient\_cff\_%')
        AND `option_name` NOT LIKE ('%\_transient\_cff\_header%');", ARRAY_A );

		if ( isset( $results[0] ) ) {
			$feed_caches = $results;
		}
		$settings_to_send['num_found_feed_caches'] = count( $feed_caches );

		$data['settings']      = $settings_to_send;

		// Retrieve current plugin information
		if( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );
		$plugins_to_send = array();

		foreach ( $plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) )
				continue;

			$plugins_to_send[] = $plugin['Name'];
		}

		$data['active_plugins']   = $plugins_to_send;
		$data['locale']           = get_locale();

		return $data;
	}

	public function send_checkin( $override = false, $ignore_last_checkin = false ) {
		$home_url = trailingslashit( home_url() );
		if ( strpos( $home_url, 'smashballoon.com' ) !== false ) {
			return false;
		}

		if( ! $this->tracking_allowed() && ! $override ) {
			return false;
		}

		// Send a maximum of once per week
		$usage_tracking = get_option( 'cff_usage_tracking', array( 'last_send' => 0, 'enabled' => CFF_Utils::cff_is_pro_version() ) );
		if ( is_numeric( $usage_tracking['last_send'] ) && $usage_tracking['last_send'] > strtotime( '-1 week' ) && ! $ignore_last_checkin ) {
			return false;
		}

		$request = wp_remote_post( 'https://usage.smashballoon.com/v1/checkin/', array(
			'method'      => 'POST',
			'timeout'     => 5,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking'    => false,
			'body'        => $this->get_data(),
			'user-agent'  => 'MI/' . CFFVER . '; ' . get_bloginfo( 'url' )
		) );

		// If we have completed successfully, recheck in 1 week
		$usage_tracking = array(
			'enabled' => true,
			'last_send' => time(),
		);
		update_option( 'cff_usage_tracking', $usage_tracking, false );
		return true;
	}

	private function tracking_allowed() {
		$usage_tracking = get_option( 'cff_usage_tracking', array( 'last_send' => 0, 'enabled' => CFF_Utils::cff_is_pro_version() ) );
		$tracking_allowed = isset( $usage_tracking['enabled'] ) ? $usage_tracking['enabled'] : CFF_Utils::cff_is_pro_version();

		return $tracking_allowed;
	}

	public function schedule_send() {
		if ( ! wp_next_scheduled( 'cff_usage_tracking_cron' ) ) {
			$tracking             = array();
			$tracking['day']      = rand( 0, 6  );
			$tracking['hour']     = rand( 0, 23 );
			$tracking['minute']   = rand( 0, 59 );
			$tracking['second']   = rand( 0, 59 );
			$tracking['offset']   = ( $tracking['day']    * DAY_IN_SECONDS    ) +
			                        ( $tracking['hour']   * HOUR_IN_SECONDS   ) +
			                        ( $tracking['minute'] * MINUTE_IN_SECONDS ) +
			                        $tracking['second'];
			$last_sunday = strtotime("next sunday") - (7 * DAY_IN_SECONDS);
			if ( ($last_sunday + $tracking['offset']) > time() + 6 * HOUR_IN_SECONDS ) {
				$tracking['initsend'] = $last_sunday + $tracking['offset'];
			} else {
				$tracking['initsend'] = strtotime("next sunday") + $tracking['offset'];
			}

			wp_schedule_event( $tracking['initsend'], 'weekly', 'cff_usage_tracking_cron' );
			update_option( 'cff_usage_tracking_config', $tracking );
		}
	}

	public function add_schedules( $schedules = array() ) {
		// Adds once weekly to the existing schedules.
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'custom-facebook-feed' )
		);
		return $schedules;
	}

	public function usage_opt_in() {
		if ( isset( $_GET['trackingdismiss'] ) ) {
			$usage_tracking = get_option( 'cff_usage_tracking', array( 'last_send' => 0, 'enabled' => false ) );

			$usage_tracking['enabled'] = false;

			update_option( 'cff_usage_tracking', $usage_tracking, false );

			return;
		}

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';

		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			return;
		}
		$usage_tracking = get_option( 'cff_usage_tracking', false );
		if ( $usage_tracking || isset( $_GET['feed_id'] ) ) {
			return;
		}

		if ( \CustomFacebookFeed\Builder\CFF_Db::feeds_count() < 1
			&& $_GET['page'] === 'cff-feed-builder' ) {
			return;
		}
		wp_enqueue_style(
			'cff-admin-notifications',
			CFF_PLUGIN_URL . "admin/assets/css/admin-notifications.css",
			array(),
			CFFVER
		);
		$img_src = CFF_PLUGIN_URL . 'admin/assets/img/cff-icon.png';
		?>
		<div id="cff-notifications" class="cff_discount_notice cff-usage-tracking-notice">
			<a
				class="dismiss cff-no-usage-opt-out"
				title="<?php echo esc_attr__( 'Dismiss this message', 'custom-facebook-feed' ); ?>"
				href="<?php echo admin_url('admin.php?page=cff-top&trackingdismiss=1'); ?>"
			>
				<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M9.66683 1.27325L8.72683 0.333252L5.00016 4.05992L1.2735 0.333252L0.333496 1.27325L4.06016 4.99992L0.333496 8.72659L1.2735 9.66659L5.00016 5.93992L8.72683 9.66659L9.66683 8.72659L5.94016 4.99992L9.66683 1.27325Z" fill="white"/>
				</svg>
			</a>
			<div class="bell"><img src="<?php echo esc_url( $img_src ); ?>" alt="notice"></div>
			<div class="messages">
				<div class="message" style="display: block;">

					<h3 class="title">
						<?php echo esc_html__( 'Help us improve the Custom Facebook Feed plugin', 'custom-facebook-feed') ; ?>
					</h3>

					<p class="content">
						<?php echo __( 'Understanding how you are using the plugin allows us to further improve it. Opt-in below to agree to send a weekly report of plugin usage data.', 'custom-facebook-feed' ); ?>
						<a target="_blank" rel="noopener noreferrer" href="https://smashballoon.com/custom-facebook-feed/docs/usage-tracking/"><?php echo __( 'More information', 'custom-facebook-feed' ); ?></a>
					</p>

					<div class="buttons">
						<a href="<?php echo admin_url('admin.php?page=cff-top&trackingdismiss=1') ?>" type="submit" class="cff-opt-in cff-btn cff-btn-blue"><?php echo __( 'Yes, I\'d like to help', 'custom-facebook-feed' ); ?></a>
						<a href="<?php echo admin_url('admin.php?page=cff-top&trackingdismiss=1') ?>" type="submit" class="cff-no-usage-opt-out cff-btn cff-btn-grey"><?php echo __( 'No, thanks', 'custom-facebook-feed' ); ?></a>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	public function usage_opt_in_or_out() {
		if ( ! isset( $_POST['opted_in'] ) ) {
			die ( 'You did not do this the right way!' );
		}

		$usage_tracking = get_option( 'cff_usage_tracking', array( 'last_send' => 0, 'enabled' => false ) );

		$usage_tracking['enabled'] = isset( $_POST['opted_in'] ) ? $_POST['opted_in'] === 'true' : false;

		update_option( 'cff_usage_tracking', $usage_tracking, false );

		die();
	}
}

#new CFF_Tracking();