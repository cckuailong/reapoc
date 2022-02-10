<?php
/**
 * Custom Facebook Feed Feed Post Set
 *
 * @since 4.0
 */

namespace CustomFacebookFeed\Builder;


use CustomFacebookFeed\CFF_Cache_System;
use CustomFacebookFeed\CFF_Parse;

class CFF_Post_Set {

	/**
	 * @var int
	 */
	private $feed_id;

	/**
	 * @var array
	 */
	private $feed_settings;

	/**
	 * @var array
	 */
	private $converted_settings;

	/**
	 * @var string
	 */
	private $transient_name;

	/**
	 * @var array|object
	 */
	private $data;

	/**
	 * @var array|object
	 */
	private $comments_data;

	public function __construct( $feed_id ) {
		$this->feed_id = $feed_id;
		$this->transient_name = '*' . $feed_id;

		$this->data = array();
	}

	/**
	 * @return array|object
	 *
	 * @since 4.0
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * @return array|object
	 *
	 * @since 4.0
	 */
	public function get_comments_data() {
		return $this->comments_data;
	}

	/**
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_feed_settings() {
		return $this->feed_settings;
	}

	/**
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_converted_settings() {
		return $this->converted_settings;
	}

	/**
	 * Sets the settings in builder form as well as converted
	 * settings for general use in the plugin
	 *
	 * @since 4.0
	 */
	public function init( $customizerBuilder = false, $previewSettings = false ) {
		$saver = new CFF_Feed_Saver( $this->feed_id );
		if( $customizerBuilder && $previewSettings != false){

			if ( isset( $previewSettings['album'] ) ) {
				$previewSettings['album'] = CFF_Source::extract_id( $previewSettings['album'], 'album' );
			}
			if ( isset( $previewSettings['playlist'] ) ) {
				$previewSettings['playlist'] = CFF_Source::extract_id( $previewSettings['playlist'], 'playlist' );
			}
			$this->feed_settings = $saver->get_feed_settings_preview( $previewSettings );
		} else{
			$this->feed_settings = $saver->get_feed_settings();
		}

		$this->converted_settings = CFF_Post_Set::builder_to_general_settings_convert( $this->feed_settings );
	}

	/**
	 * Gathers posts from the API until the minimum number of posts
	 * for the feed are retrieved then stores the results
	 *
	 * @since 4.0
	 */
	public function fetch() {
		$settings = $this->converted_settings;

		$facebook_feed = new \CustomFacebookFeed\CFF_Feed_Pro( $this->transient_name, true );

		if ( $facebook_feed->need_posts( $settings['num'] ) && $facebook_feed->can_get_more_posts() ) {
			while ( $facebook_feed->need_posts( $settings['num'] ) && $facebook_feed->can_get_more_posts() ) {
				$facebook_feed->add_remote_posts( $settings );
			}
		}

		$post_data = $facebook_feed->get_post_data();
		$this->data = $post_data;
	}

	/**
	 * Gathers comments for posts.
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function fetch_comments() {
		if ( empty( $this->data ) ) {
			return array();
		}

		$settings = $this->converted_settings;

		$comments = [];
		foreach ( $this->data as $single_post ) {
			$id = CFF_Parse::get_post_id( $single_post );
			$json_object = \CustomFacebookFeed\CFF_Utils::cff_fetchUrl( "https://graph.facebook.com/" . $id . "/?fields=comments.limit(5){created_time,from{name,id,picture{url},link},id,message,message_tags,attachment,like_count}&access_token=" . $settings['accesstoken'] );
			$comments_return = json_decode( $json_object );
			if ( isset( $comments_return->comments->data ) ) {
				$comments[ $id ] = $comments_return->comments->data;
			}
		}

		$this->comments_data = $comments;

		return $comments;
	}

	/**
	 * Converts raw settings from the cff_feed_settings table into the
	 * more general way that the "CFF_Shortcode" class,
	 * "cff_get_processed_options" method does
	 *
	 * @param array $builder_settings
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function builder_to_general_settings_convert( $builder_settings ) {
		$settings_with_multiples = array(
			'type',
			'include',
			'exclude',
			'filter',
			'exfilter'
		);

		foreach ( $settings_with_multiples as $array_setting ) {
			if ( is_array( $builder_settings[ $array_setting ] ) ) {
				$builder_settings[ $array_setting ] = implode( ',', $builder_settings[ $array_setting ] );
			}
		}

		if ( isset( $builder_settings['sources'] ) && is_array($builder_settings['sources'])) {

			$access_tokens = array();
			$ids = array();
			$id_access_tokens = array();
			$sources_setting = array();
			foreach ( $builder_settings['sources'] as $source ) {
				$source_array = array();
				if ( ! is_array( $source ) ) {
					$args = array( 'id' => $source );
					if ( isset( $builder_settings['feedtype'] ) && $builder_settings['feedtype'] == 'events' ){
						$args['privilege'] = 'events';
					}
					$source_query = \CustomFacebookFeed\Builder\CFF_Db::source_query( $args );

					if ( isset( $source_query[0] ) ) {
						$source_array = $source_query[0];
						$sources_setting[] = $source_query[0];
					}
				} else {
					$source_array = $source;
				}


				if ( ! empty( $source_array ) ) {
					$access_tokens[] = $source_array['access_token'];
					$ids[] = $source_array['account_id'];
					$id_access_tokens[ $source_array['account_id'] ] = $source_array['access_token'];
					$builder_settings['pagetype'] = $source_array['account_type'];
				}

			}

			if ( ! empty( $sources_setting ) ) {
				$builder_settings['sources'] = $sources_setting;
			}

			if ( count( $builder_settings['sources'] ) > 1 ) {
				$builder_settings['accesstoken'] = $id_access_tokens;
				$builder_settings['id'] = implode( ',', $ids );
			} else {
				$builder_settings['accesstoken'] = implode( ',', $access_tokens );
				$builder_settings['id'] = implode( ',', $ids );
			}

		}
		$builder_settings['multifeedactive'] = \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'multifeed' );
		$builder_settings['daterangeactive'] = \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'date_range' );
		$builder_settings['daterangeactive'] = false; // Not sure why plugin things date range is active when not
		$builder_settings['featuredpostactive'] = \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'featured_post' );
		$builder_settings['albumactive'] = \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'album' );
		$builder_settings['carouselactive'] = \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'carousel' );
		$builder_settings['reviewsactive'] = \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'reviews' );


		if ( empty( $builder_settings['pagetoken'] )
		     || (is_array( $builder_settings['pagetoken'] ) && empty( $builder_settings['pagetoken'][0]) ) ) {
			$builder_settings['pagetoken'] = $builder_settings['accesstoken'];
		}

		if ( $builder_settings['feedtype'] === 'reviews' ) {
			$builder_settings['type'] = 'review';
		}

		return $builder_settings;
	}

	/**
	 * Convert settings from 3.x for use in the builder in 4.0+
	 *
	 * @param array $atts
	 *
	 * @return array
	 *
	 * @since 4.0.3
	 */
	public static function legacy_to_builder_convert( $atts = array() ) {
		$options    = get_option( 'cff_style_settings', array() );
		$legacy_feed_settings_obj = new \CustomFacebookFeed\CFF_FB_Settings( $atts, $options );

		$processed_settings = $legacy_feed_settings_obj->get_settings();

		$settings_with_multiples = CFF_Post_Set::get_settings_with_multiple();

		foreach ( $settings_with_multiples as $multiple_key ) {
			if ( isset( $processed_settings[ $multiple_key ] )
			     && ! is_array( $processed_settings[ $multiple_key ] ) ) {
				$processed_settings[ $multiple_key ] = explode( ',', $processed_settings[ $multiple_key ] );
			}
		}

		if ( $processed_settings['cols'] > 1
		     || $processed_settings['colsmobile'] > 1 ) {
			$processed_settings['feedlayout'] = 'masonry';
		} else {
			$processed_settings['feedlayout'] = 'list';
		}
		$processed_settings['feedtype'] = 'timeline';

		if ( ! in_array( 'likebox', $processed_settings['include'] ) ) {
			$processed_settings['showlikebox'] = 'off';
		}
		if ( ! in_array( 'author', $processed_settings['include'] ) ) {
			$processed_settings['showauthornew'] = false;
		} else {
			$processed_settings['showauthornew'] = true;
		}
		if ( ! in_array( 'link', $processed_settings['include'] ) ) {
			$processed_settings['showfacebooklink'] = '';
			$processed_settings['showsharelink'] = '';
		} else {
			$processed_settings['showfacebooklink'] = 'on';
			$processed_settings['showsharelink'] = 'on';
		}
		$processed_settings['showfacebooklink'] = $processed_settings['showfacebooklink'] === 'on' || $processed_settings['showfacebooklink'] === 'true' ? 'on' : '';
		$processed_settings['showsharelink'] = $processed_settings['showsharelink'] === 'on' || $processed_settings['showsharelink'] === 'true' ? 'on' : '';

		$processed_settings['textlength'] = get_option( 'cff_title_length', '400' );
		$processed_settings['desclength'] = get_option( 'cff_body_length', '200' );

		$processed_settings['num'] = isset( $processed_settings['num'] ) ? (string)$processed_settings['num'] : '5';

		$processed_settings = CFF_Post_Set::filter_builder_settings( $processed_settings );

		return $processed_settings;
	}

	/**
	 * Settings that can include an array of values
	 *
	 * @return array
	 *
	 * @since 4.0.3
	 */
	public static function get_settings_with_multiple() {
		$settings_with_multiples = array(
			'type',
			'include',
			'exclude',
		);

		if ( \CustomFacebookFeed\CFF_Utils::cff_is_pro_version() ) {
			$settings_with_multiples = \CustomFacebookFeed\Builder\Pro\CFF_Post_Set_Pro::add_pro_settings_with_multiple( $settings_with_multiples );
		}

		return $settings_with_multiples;
	}

	/**
	 * Used for changing the settings used for general front end feeds
	 *
	 * @param array $builder_settings
	 *
	 * @return array
	 *
	 * @since 4.0.3
	 */
	public static function filter_general_settings( $builder_settings ) {
		if ( \CustomFacebookFeed\CFF_Utils::cff_is_pro_version() ) {
			$builder_settings = \CustomFacebookFeed\Builder\Pro\CFF_Post_Set_Pro::add_general_pro_settings( $builder_settings );
		}
		return $builder_settings;
	}

	/**
	 * Used for changing the settings for feeds being edited in the customizer
	 *
	 * @param array $processed_settings
	 *
	 * @return array
	 *
	 * @since 4.0.3
	 */
	public static function filter_builder_settings( $processed_settings ) {
		if ( \CustomFacebookFeed\CFF_Utils::cff_is_pro_version() ) {
			$processed_settings = \CustomFacebookFeed\Builder\Pro\CFF_Post_Set_Pro::add_builder_pro_settings( $processed_settings );
		}
		return $processed_settings;
	}
}