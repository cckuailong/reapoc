<?php
/**
 * Custom Facebook Feed Database
 *
 * @since 4.0
 */

namespace CustomFacebookFeed\Builder;

use CustomFacebookFeed\CFF_FB_Settings;
use CustomFacebookFeed\SB_Facebook_Data_Encryption;

class CFF_Feed_Saver {

	/**
	 * @var int
	 *
	 * @since 4.0
	 */
	private $insert_id;

	/**
	 * @var array
	 *
	 * @since 4.0
	 */
	private $data;

	/**
	 * @var array
	 *
	 * @since 4.0
	 */
	private $sanitized_and_sorted_data;

	/**
	 * @var array
	 *
	 * @since 4.0
	 */
	private $feed_db_data;


	/**
	 * @var string
	 *
	 * @since 4.0
	 */
	private $feed_name;

	/**
	 * @var bool
	 *
	 * @since 4.0
	 */
	private $is_legacy;

	/**
	 * CFF_Feed_Saver constructor.
	 *
	 * @param int $insert_id
	 *
	 * @since 4.0
	 */
	public function __construct( $insert_id ) {
		if ( $insert_id === 'legacy' ) {
			$this->is_legacy = true;
			$this->insert_id = 0;
		} else {
			$this->is_legacy = false;
			$this->insert_id = $insert_id;
		}
	}

	/**
	 * Feed insert ID if it exists
	 *
	 * @return bool|int
	 *
	 * @since 4.0
	 */
	public function get_feed_id() {
		if ( $this->is_legacy ) {
			return 'legacy';
		}
		if ( ! empty( $this->insert_id ) ) {
			return $this->insert_id;
		} else {
			return false;
		}
	}

	/**
	 * @param array $data
	 *
	 * @since 4.0
	 */
	public function set_data( $data ) {
		$this->data = $data;
	}

	/**
	 * @param string $feed_name
	 *
	 * @since 4.0
	 */
	public function set_feed_name( $feed_name ) {
		$this->feed_name = $feed_name;
	}

	/**
	 * @param array $feed_db_data
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_feed_db_data() {
		return $this->feed_db_data;
	}

	/**
	 * Adds a new feed if there is no associated feed
	 * found. Otherwise updates the exiting feed.
	 *
	 * @return false|int
	 *
	 * @since 4.0
	 */
	public function update_or_insert() {
		$this->sanitize_and_sort_data();

		if ( $this->exists_in_database() ) {
			return $this->update();
		} else {
			return $this->insert();
		}
	}

	/**
	 * Whether or not a feed exists with the
	 * associated insert ID
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function exists_in_database() {
		if ( $this->is_legacy ) {
			return true;
		}

		if ( $this->insert_id === false ) {
			return false;
		}

		$args = array(
			'id' => $this->insert_id
		);

		$results = CFF_Db::feeds_query( $args );

		return isset( $results[0] );
	}

	/**
	 * Inserts a new feed from sanitized and sorted data.
	 * Some data is saved in the cff_feeds table and some is
	 * saved in the cff_feed_settings table.
	 *
	 * @return false|int
	 *
	 * @since 4.0
	 */
	public function insert() {
		if ( $this->is_legacy ) {
			return $this->update();
		}

		if ( ! isset( $this->sanitized_and_sorted_data ) ) {
			return false;
		}

		$settings_array = CFF_Feed_Saver::format_settings( $this->sanitized_and_sorted_data['feed_settings'] );

		$this->sanitized_and_sorted_data['feeds'][] = array(
			'key' => 'settings',
			'values' => array( \CustomFacebookFeed\CFF_Utils::cff_json_encode( $settings_array ) )
		);

		if ( ! empty( $this->feed_name ) ) {
			$this->sanitized_and_sorted_data['feeds'][] = array(
				'key' => 'feed_name',
				'values' => array( $this->feed_name )
			);
		}

		$this->sanitized_and_sorted_data['feeds'][] = array(
			'key' => 'status',
			'values' => array( 'publish' )
		);

		$insert_id = CFF_Db::feeds_insert( $this->sanitized_and_sorted_data['feeds'] );

		if ( $insert_id ) {
			$this->insert_id = $insert_id;

			return $insert_id;
		}

		return false;
	}

	/**
	 * Updates an existing feed and related settings from
	 * sanitized and sorted data.
	 *
	 * @return false|int
	 *
	 * @since 4.0
	 */
	public function update() {
		$encryption = new SB_Facebook_Data_Encryption();
		if ( ! isset( $this->sanitized_and_sorted_data ) ) {
			return false;
		}

		$args = array(
			'id' => $this->insert_id
		);

		$settings_array = CFF_Feed_Saver::format_settings( $this->sanitized_and_sorted_data['feed_settings'] );

		if ( $this->is_legacy ) {
			$to_save_json = \CustomFacebookFeed\CFF_Utils::cff_json_encode( $settings_array );
			$to_save_json = $encryption->maybe_encrypt( $to_save_json );

			return update_option( 'cff_legacy_feed_settings', $to_save_json );
		}

		$this->sanitized_and_sorted_data['feeds'][] = array(
			'key' => 'settings',
			'values' => array( \CustomFacebookFeed\CFF_Utils::cff_json_encode( $settings_array ) )
		);

		$this->sanitized_and_sorted_data['feeds'][] = array(
			'key' => 'feed_name',
			'values' => [sanitize_text_field($this->feed_name)]
		);

		$success = CFF_Db::feeds_update( $this->sanitized_and_sorted_data['feeds'], $args );

		return $success;
	}

	/**
	 * Converts settings that have been sanitized into an associative array
	 * that can be saved as JSON in the database
	 *
	 * @param $raw_settings
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function format_settings( $raw_settings ) {
		$settings_array = array();
		foreach ( $raw_settings as $single_setting ) {
			if ( count( $single_setting['values'] ) > 1 ) {
				$settings_array[ $single_setting['key'] ] = $single_setting['values'];

			} else {
				$settings_array[ $single_setting['key'] ] = $single_setting['values'][0];

			}
		}

		return $settings_array;
	}

	/**
	 * Retrieves and organizes feed setting data for easy use in
	 * the builder
	 *
	 * @return array|bool
	 *
	 * @since 4.0
	 */
	public function get_feed_settings( $is_export = false ) {
		$encryption = new SB_Facebook_Data_Encryption();
		if ( $this->is_legacy ) {
			$return =  CFF_FB_Settings::get_legacy_settings( array() ) ;
			$this->feed_db_data = array(
				'id' => 'legacy',
				'feed_name' => __( 'Legacy Feeds', 'custom-facebook-feed' ),
				'feed_title' => __( 'Legacy Feeds', 'custom-facebook-feed' ),
				'status' => 'publish',
				'last_modified' => date( 'Y-m-d H:i:s' ),
			);
		} else if ( empty( $this->insert_id ) ) {
			return false;
		} else {
			$args = array(
				'id' => $this->insert_id,
			);
			$settings_db_data = CFF_Db::feeds_query( $args );
			if ( false === $settings_db_data || sizeof($settings_db_data) == 0) {
				return false;
			}
			$this->feed_db_data = array(
				'id' => $settings_db_data[0]['id'],
				'feed_name' => $settings_db_data[0]['feed_name'],
				'feed_title' => $settings_db_data[0]['feed_title'],
				'status' => $settings_db_data[0]['status'],
				'last_modified' => $settings_db_data[0]['last_modified'],
			);

			$return = json_decode( $settings_db_data[0]['settings'], true );
			$return['feed_name'] = $settings_db_data[0]['feed_name'];
		}

		$return = wp_parse_args( $return, CFF_Feed_Saver::settings_defaults() );


		if ( empty( $return['sources'] ) ) {
			return $return;
		}
		$args = array( 'id' => $return['sources'] );

		if ( ! empty( $return['type'] )
		     && $return['type'] === 'events'
		     && ! empty( $return['eventsource'] )
		     && $return['eventsource'] === 'eventspage' ) {
			$args['privilege'] = 'events';
		}

		if ( isset( $return['feedtype'] ) && $return['feedtype'] == 'events' ){
			$args['privilege'] = 'events';
		}

		$source_query = CFF_Db::source_query( $args );

		$return['sources'] = array();

		if ( ! empty( $source_query ) ) {

			foreach ( $source_query as $source ) {

				$info = ! empty( $source['info'] ) ? json_decode( stripslashes( $source['info'] ) ) : array();
				$avatar = \CustomFacebookFeed\CFF_Parse::get_avatar( $info );

				$source['avatar_url'] = $avatar;
				$return['sources'][] = array(
					'record_id' => stripslashes( $source['id'] ),
					'account_id' => stripslashes( $source['account_id'] ),
					'account_type' => stripslashes( $source['account_type'] ),
					'privilege' => stripslashes( $source['privilege'] ),
					'access_token' => $is_export === true ? stripslashes( $source['access_token'] ) : stripslashes( $encryption->decrypt( $source['access_token'] ) ),
					'username' => stripslashes( $source['username'] ),
					'info' => stripslashes( $encryption->decrypt( $source['info'] ) ),
					'error' => stripslashes( $source['error'] ),
					'expires' => stripslashes( $source['expires'] ),
					'avatar_url' => stripslashes( $source['avatar_url'] ),

				);
			}

			$return['accesstoken'] = stripslashes( $source_query[0]['access_token'] );
			$return['id'] = stripslashes( $source_query[0]['account_id'] );
		} else {
			if ( isset( $args['privilege'] ) ){
				unset( $args['privilege'] );
				$source_query = CFF_Db::source_query( $args );
			} else {
				$args['privilege'] = 'events';
				$source_query = CFF_Db::source_query( $args );
			}

			if ( empty( $source_query ) ) {
				$source_query = CFF_Db::source_query();
			}
			if ( isset( $source_query[0] ) ) {
				$return['sources'][] = array(
					'record_id' => stripslashes( $source_query[0]['id'] ),
					'account_id' => stripslashes( $source_query[0]['account_id'] ),
					'account_type' => stripslashes( $source_query[0]['account_type'] ),
					'privilege' => stripslashes( $source_query[0]['privilege'] ),
					'access_token' => stripslashes( $encryption->decrypt( $source_query[0]['access_token'] ) ),
					'username' => stripslashes( $source_query[0]['username'] ),
					'info' => stripslashes( $encryption->decrypt( $source_query[0]['info'] ) ),
					'expires' => stripslashes( $source_query[0]['expires'] ),
				);

				$return['accesstoken'] = stripslashes( $source_query[0]['access_token'] );
				$return['id'] = stripslashes( $source_query[0]['account_id'] );

			}
		}

		return $return;
	}

	/**
	 * Retrieves and organizes feed setting data for easy use in
	 * the builder
	 * It will NOT get the settings from the DB, but from the Customizer builder
	 * To be used for updating feed preview on the fly
	 *
	 * @return array|bool
	 *
	 * @since 4.0
	 */
	public function get_feed_settings_preview( $settings_db_data ) {
		if ( false === $settings_db_data || sizeof($settings_db_data) == 0) {
			return false;
		}
		$return = $settings_db_data;
		$return = wp_parse_args( $return, CFF_Feed_Saver::settings_defaults() );
		if ( empty( $return['sources'] ) ) {
			return $return;
		}
		$sources = [];
		foreach ($return['sources'] as $single_source) {
			array_push($sources, $single_source['account_id']);
		}

		$args = array( 'id' => $sources );
		$source_query = CFF_Db::source_query( $args );
		$encryption = new SB_Facebook_Data_Encryption();

		$return['sources'] = array();
		if ( ! empty( $source_query ) ) {
			foreach ( $source_query as $source ) {
				$return['sources'][] = array(
					'record_id' => stripslashes( $source['id'] ),
					'account_id' => stripslashes( $source['account_id'] ),
					'account_type' => stripslashes( $source['account_type'] ),
					'access_token' => stripslashes( $encryption->decrypt( $source['access_token'] ) ),
					'username' => stripslashes( $source['username'] ),
					'info' => stripslashes( $encryption->decrypt( $source['info'] ) ),
					'expires' => stripslashes( $source['expires'] ),
				);
			}
		}

		return $return;
	}



	/**
	 * Default settings, $return_array equalling false will return
	 * the settings in the general way that the "CFF_Shortcode" class,
	 * "cff_get_processed_options" method does
	 *
	 * @param bool $return_array
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function settings_defaults( $return_array = true ) {
		{
			$translations = get_option( 'cff_style_settings', array() );

			$final_translations = [];
			$final_translations['facebooklinktext'] = isset( $translations['cff_facebook_link_text'] ) ? stripslashes( esc_attr( $translations['cff_facebook_link_text'] ) ) : __( 'View on Facebook', 'custom-facebook-feed' );
			$final_translations['sharelinktext'] = isset( $translations['cff_facebook_share_text'] ) ? stripslashes( esc_attr( $translations['cff_facebook_share_text'] ) ) : __( 'Share', 'custom-facebook-feed' );
			$final_translations['buttontext'] = isset( $translations[ 'cff_load_more_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_load_more_text' ] ) ) : __( 'Load more', 'custom-facebook-feed' );

			$defaults = array(
				'sources' => '',
				'accesstoken' => '',
				'ownaccesstoken' => true,
				'pagetoken' => '',
				'id' => '',
				'pagetype' => 'page',
				'num' => '5',
				'limit' => '',
				'others' => '',
				'showpostsby' => 'me',
				'cachetype' => 'background',
				'cachetime' => '1',
				'cacheunit' => 'hours',
				'locale' => 'en_US',
				'storytags' => 'false',
				'ajax' => '',
				'offset' => '',
				'account' => '',
				'width' => '100%',
				'widthresp' => '',
				'height' => '',
				'padding' => '',
				'bgcolor' => '#',
				'showauthor' => '',
				'showauthornew' => true, // test this
				'class' => '',
				'type' => 'links,events,videos,photos,albums,statuses',
				'gdpr' => 'auto',
				'loadiframes' => 'false',
				'eventsource' => 'eventspage',
				'eventoffset' => '6',
				'eventimage' => 'full',
				'pastevents' => 'false',
				'albumsource' => 'photospage',
				'showalbumtitle' => 'on',
				'showalbumnum' => 'on',
				'albumcols' => '4',
				'photosource' => 'photospage',
				'photocols' => '4',
				'videosource' => 'videospage',
				'showvideoname' => 'on',
				'showvideodesc' => 'on',
				'videocols' => '4',
				'playlist' => '',
				'disablelightbox' => 'on',
				'filter' => '',
				'exfilter' => '',
				'layout' => 'full',
				'enablenarrow' => 'on',
				'oneimage' => '',
				'mediaposition' => 'below',
				'include' => 'author,text,desc,sharedlinks,date,medialink,eventtitle,eventdetails,link,likebox',
				'exclude' => '',
				'masonry' => '',
				'masonrycols' => '',
				'masonrycolsmobile' => '',
				'masonryjs' => true,
				'cols' => 3,
				'colsmobile' => 1,
				'colsjs' => true,
				'nummobile' => '',
				'poststyle' => 'regular',
				'postbgcolor' => '#',
				'postcorners' => '0',
				'boxshadow' => '',
				'textformat' => 'p',
				'textsize' => 'inherit',
				'textweight' => 'inherit',
				'textcolor' => '#',
				'textlinkcolor' => '#',
				'textlink' => '',
				'posttags' => 'on',
				'linkhashtags' => 'on',
				'lightboxcomments' => 'off',
				'authorsize' => 'inherit',
				'authorcolor' => '#',
				'descsize' => '12',
				'descweight' => 'inherit',
				'desccolor' => '#',
				'linktitleformat' => 'p',
				'linktitlesize' => 'inherit',
				'linkdescsize' => 'inherit',
				'linkurlsize' => '12',
				'linkdesccolor' => '#',
				'linktitlecolor' => '#',
				'linkurlcolor' => '#',
				'linkbgcolor' => '#',
				'linkbordercolor' => '#',
				'disablelinkbox' => 'off',
				'eventtitleformat' => 'p',
				'eventtitlesize' => 'inherit',
				'eventtitleweight' => 'inherit',
				'eventtitlecolor' => '#',
				'eventtitlelink' => true,
				'eventdatesize' => 'inherit',
				'eventdateweight' => 'inherit',
				'eventdatecolor' => '#',
				'eventdatepos' => 'below',
				'eventdateformat' => '14',
				'eventdatecustom' => '',
				'timezoneoffset' => 'false',
				'cff_enqueue_with_shortcode' => false,
				'eventdetailssize' => 'inherit',
				'eventdetailsweight' => 'inherit',
				'eventdetailscolor' => '#',
				'eventlinkcolor' => '#',
				'datepos' => 'author',
				'datesize' => 'inherit',
				'dateweight' => 'inherit',
				'datecolor' => '#',
				'dateformat' => '1',
				'datecustom' => '',
				'timezone' => 'America/Chicago',
				'beforedate' => '',
				'afterdate' => '',
				'linksize' => 'inherit',
				'linkweight' => 'inherit',
				'linkcolor' => '#',
				'linktotimeline' => false,
				'buttoncolor' => '',
				'buttonhovercolor' => '',
				'buttontextcolor' => '',
				'buttontext' =>  $final_translations['buttontext'],
				'facebooklinktext' => $final_translations['facebooklinktext'],
				'sharelinktext' => $final_translations['sharelinktext'],
				'iconstyle' => 'light',
				'socialtextcolor' => '#',
				'socialbgcolor' => '#',
				'sociallinkcolor' => '#',
				'expandcomments' => '',
				'commentsnum' => '4',
				'hidecommentimages' => '',
				'loadcommentsjs' => 'false',
				'salesposts' => 'false',
				'textlength' => '400',
				'desclength' => '200',
				'showlikebox' => 'on',
				'likeboxpos' => 'bottom',
				'likeboxoutside' => '',
				'likeboxcolor' => '',
				'likeboxtextcolor' => 'blue',
				'likeboxwidth' => '',
				'likeboxfaces' => '',
				'likeboxborder' => '',
				'likeboxcover' => 'on',
				'likeboxsmallheader' => 'off',
				'likeboxhidebtn' => '',
				'credit' => '',
				'textissue' => '',
				'disablesvgs' => '',
				'restrictedpage' => '',
				'hidesupporterposts' => '',
				'privategroup' => 'false',
				'nofollow' => 'true',
				'timelinepag' => 'date',
				'gridpag' => 'auto',
				'disableresize' => false,
				'showheader' => 'on',
				'headertype' => 'visual',
				'headercover' => 'on',
				'headeravatar' => '',
				'headername' => 'on',
				'headerbio' => 'on',
				'headercoverheight' => '300',
				'headerlikes' => '',
				'headeroutside' => 'on',
				'headertext' => __( 'Facebook Posts', 'custom-facebook-feed' ),
				'headerbg' => '#',
				'headerpadding' => '',
				'headertextsize' => 'inherit',
				'headertextweight' => 'inherit',
				'headertextcolor' => '#',
				'headericon' => 'facebook-square',
				'headericoncolor' => '#',
				'headericonsize' => '28',
				'headerinc' => '',
				'headerexclude' => '',
				'loadmore' => 'on',
				'fulllinkimages' => 'on',
				'linkimagesize' => 'largesquare',
				'postimagesize' => 'large',
				'videoheight' => '',
				'videoaction' => 'post',
				'videoplayer' => 'facebook',
				'sepcolor' => '#ddd',
				'sepsize' => '1',
				'photostext' => 'photos',
				'showfacebooklink' => 'true', // test this
				'showsharelink' => 'true', // test this
				'multifeedactive' => false,
				'daterangeactive' => false,
				'featuredpostactive' => false,
				'albumactive' => false,
				'masonryactive' => false,
				'carouselactive' => false,
				'reviewsactive' => false,
				//Date Range
				'from' => '',
				'until' => '',
				'daterangefromtype' => 'specific',
				'daterangefromspecific' => '',
				'daterangefromrelative' => '',

				'daterangeuntiltype' => 'specific',
				'daterangeuntilspecific' => '',
				'daterangeuntilrelative' => '',

				'featuredpost' => '',
				'album' => '',
				'daterange' => 'off',
				'lightbox' => 'off',
				'reviewsrated' => '1,2,3,4,5',
				'starsize' => '12',
				'hidenegative' => '',
				'reviewslinktext' => __( 'View all Reviews', 'custom-facebook-feed' ),
				'reviewshidenotext' => '',
				'reviewsmethod' => 'all',
				//TO BE CHECKED
				'feedtype' 			=> 'timeline', // working
				'likeboxcustomwidth' => '', // working
				'colstablet'	=> 2, // working 400/800
				'feedlayout'	=> 'list', // working
				'colorpalette'	=> 'inherit', // working
				'custombgcolor1'=>	'', // working
				'custombgcolor2'=>	'', // working
				'textcolor1'	=>	'', // working
				'textcolor2'	=>	'', // working
				'customlinkcolor'		=>	'', // working
				'posttextcolor'	=>	'', // working
				'misctextcolor'	=>	'', // working
				'misclinkcolor'	=>	'', // working
				'headericonenabled' => 'on', // working
				'lightboxbgcolor'	=> '', // working
				'lightboxtextcolor'	=> '', // working
				'lightboxlinkcolor'	=> '', // working
				'beforedateenabled'	=> 'off', // working
				'afterdateenabled'	=> 'off', // working
				'showpoststypes' => 'custom', // working
				'headerbiosize' => 'inherit', // working
				'headerbiocolor' => '#', // working
				'apipostlimit'	=> 'auto', // working
				'carouselheight'		=> 'tallest',
				'carouseldesktop_cols'	=> 1,
				'carouselmobile_cols'	=> 1,
				'carouselnavigation'	=> 'none',
				'carouselpagination'	=> 'true',
				'carouselautoplay'		=> 'false',
				'carouselinterval'		=> 5000,
			);

			$defaults = CFF_Feed_Saver::filter_defaults( $defaults );

			// some settings are comma separated and not arrays when the feed is created
			if ( $return_array ) {
				$settings_with_multiples = array(
					'sources',
					'accesstoken',
					'id',
					'type',
					'include',
					'exclude',
					'filter',
					'exfilter'
				);

				foreach ( $settings_with_multiples as $multiple_key ) {
					if ( isset( $defaults[ $multiple_key ] ) ) {
						$defaults[ $multiple_key ] = explode( ',', $defaults[ $multiple_key ] );
					}
				}
			}

			return $defaults;
		}
	}

	/**
	 * Provides backwards compatibility for extensions
	 *
	 * @param array $defaults
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function filter_defaults( $defaults ) {

		if ( \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'carousel' ) ) {
			$cff_carousel_options = get_option( 'cff_carousel_options' );
			// If an option is set, use the saved value, otherwise use the default
			$enabled = isset( $cff_carousel_options['cff_carousel_enabled'] ) ? $cff_carousel_options['cff_carousel_enabled'] : false;
			$height = isset( $cff_carousel_options['cff_carousel_height'] ) ? $cff_carousel_options['cff_carousel_height'] : 'tallest';
			$desktop_cols = isset( $cff_carousel_options['cff_carousel_desktop_cols'] ) ? $cff_carousel_options['cff_carousel_desktop_cols'] : 1;
			$mobile_cols = isset( $cff_carousel_options['cff_carousel_mobile_cols'] ) ? $cff_carousel_options['cff_carousel_mobile_cols'] : 1;
			$arrows = isset( $cff_carousel_options['cff_carousel_navigation'] ) ? $cff_carousel_options['cff_carousel_navigation'] : 'none';
			$pagination = isset( $cff_carousel_options['cff_carousel_pagination'] ) ? $cff_carousel_options['cff_carousel_pagination'] : true;
			$autoplay = isset( $cff_carousel_options['cff_carousel_autoplay'] ) ? $cff_carousel_options['cff_carousel_autoplay'] : false;
			$interval = isset( $cff_carousel_options['cff_carousel_interval'] ) ? $cff_carousel_options['cff_carousel_interval'] : 5000;

			$defaults['carouselheight'] = $height;
			$defaults['carouseldesktop_cols'] = $desktop_cols;
			$defaults['carouselmobile_cols'] = $mobile_cols;
			$defaults['carouselnavigation'] = $arrows;
			$defaults['carouselpagination'] = $pagination || $pagination === 'on';
			$defaults['carouselautoplay'] = $autoplay || $autoplay === 'on';
			$defaults['carouselinterval'] = $interval;

			if ( $enabled ) {
				$defaults['feedlayout'] = 'carousel';
			}

		}

		if ( \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'reviews' ) ) {
			$options = get_option( 'cff_style_settings', array() );

			$defaults['starsize'] = isset($options[ 'cff_star_size' ]) ? $options[ 'cff_star_size' ] : '';
			$defaults['hidenegative'] = isset( $options[ 'cff_reviews_hide_negative' ] ) ? stripslashes( esc_attr( $options[ 'cff_reviews_hide_negative' ] ) ) : '';
			$defaults['reviewslinktext'] = isset( $options[ 'cff_reviews_link_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_reviews_link_text' ] ) ) : __( 'View all Reviews', 'custom-facebook-feed' );
			$defaults['reviewshidenotext'] = isset( $options[ 'cff_reviews_no_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_reviews_no_text' ] ) ) : '';
			$defaults['reviewsmethod'] = isset( $options[ 'cff_reviews_method' ] ) ? stripslashes( esc_attr( $options[ 'cff_reviews_method' ] ) ) : '';

			$defaults['cff_reviews_rated_5'] = isset($options[ 'cff_reviews_rated_5' ]) ? $options[ 'cff_reviews_rated_5' ] : 'true';
			$defaults['cff_reviews_rated_4'] = isset($options[ 'cff_reviews_rated_4' ]) ? $options[ 'cff_reviews_rated_4' ] : 'true';
			$defaults['cff_reviews_rated_3'] = isset($options[ 'cff_reviews_rated_3' ]) ? $options[ 'cff_reviews_rated_3' ] : 'true';
			$defaults['cff_reviews_rated_2'] = isset($options[ 'cff_reviews_rated_2' ]) ? $options[ 'cff_reviews_rated_2' ] : 'true';
			$defaults['cff_reviews_rated_1'] = isset($options[ 'cff_reviews_rated_1' ]) ? $options[ 'cff_reviews_rated_1' ] : 'true';
		}

		return $defaults;
	}

	public static function set_legacy_feed_settings() {
		$to_save = CFF_Post_Set::legacy_to_builder_convert();
		$encryption = new SB_Facebook_Data_Encryption();
		$to_save_json = \CustomFacebookFeed\CFF_Utils::cff_json_encode( $to_save );
		$to_save_json = $encryption->maybe_encrypt( $to_save_json );

		update_option( 'cff_legacy_feed_settings', $to_save_json );
	}

	/**
	 * Used for taking raw post data related to settings
	 * an sanitizing it and sorting it to easily use in
	 * the database tables
	 *
	 * @since 4.0
	 */
	private function sanitize_and_sort_data() {
		$data = $this->data;

		$sanitized_and_sorted = array(
			'feeds' => array(),
			'feed_settings' => array()
		);

		foreach ( $data as $key => $value ) {

			$data_type = CFF_Feed_Saver_Manager::get_data_type( $key );
			$sanitized_values = array();
			if ( is_array( $value ) ) {
				foreach ( $value as $item ) {
					$sanitized_values[] = CFF_Feed_Saver_Manager::sanitize( $data_type['sanitization'], $item );
				}
			} else {
				$sanitized_values[] = CFF_Feed_Saver_Manager::sanitize( $data_type['sanitization'], $value );
			}

			$single_sanitized = array(
				'key' => $key,
				'values' => $sanitized_values
			);

			$sanitized_and_sorted[ $data_type['table'] ][] = $single_sanitized;
		}

		$this->sanitized_and_sorted_data = $sanitized_and_sorted;
	}
}