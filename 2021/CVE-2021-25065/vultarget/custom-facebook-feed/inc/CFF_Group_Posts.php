<?php
/**
 * Class CFF_Groups_Post
 *
 *
 *
 * @since 3.19.3
 */
namespace CustomFacebookFeed;
use CustomFacebookFeed\SB_Facebook_Data_Encryption;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class CFF_Group_Posts{

	/**
	 * @var string
	*/
	private $cache_name;

	/**
	 * @var string
	*/
	private $api_call_url;

	/**
	 * @var string
	*/
	private $data_att_html;

	/**
	 * @var array
	*/
	private $posts_array;

	/**
	 * @var array
	*/
	private $json_data;

	/**
	 * @var array
	*/
	private $posts_cache_data;

	/**
	 * @var array
	*/
	private $feed_options;

	/**
	 * @var int
	*/
	private $next_urls_arr_safe;

	/**
	 * @var bool
	*/
	private $is_event_page;

	/**
	 * @var class
	*/
	private $encryption;

	/**
	 * Construct.
	 *
	 * Construct Caching System
	 *
	 * @since 3.19.3
	 * @access public
	 */
	function __construct($group_id, $feed_options, $api_call_url, $data_att_html,$is_event_page) {
		$this->encryption = new SB_Facebook_Data_Encryption();
		$this->cache_name = '!cff_group_'. $group_id . '_' . str_replace(',', '_', $feed_options['type']);
		$this->posts_cache_data = get_option($this->cache_name);
		$this->feed_options = $feed_options;
		$this->api_call_url = $api_call_url;
		#$this->api_call_url = $api_call_url . '&limit=100';

		$this->data_att_html = $data_att_html;
		$this->is_event_page = $is_event_page;
		if(!$this->posts_cache_data){
			$this->posts_cache_data = new \stdClass();
			$this->posts_cache_data->api_url = $this->api_call_url;
			$this->posts_cache_data->shortcode_options = $this->data_att_html;
			$this->posts_cache_data->data = [];
		}else{
			$this->posts_cache_data = json_decode( $this->encryption->maybe_decrypt( $this->posts_cache_data ) ) ;
		}

	}


	/**
	 *
	 * @since 3.19.3
	 * Returns Needed Information for the Group Posts
	 *
	 * @access public
	 */
	function init_group_posts($json_data, $load_more_date, $show_posts){
		$this->json_data = json_decode($json_data);
		$this->json_data = (isset($this->json_data->data) && $this->json_data->data > 0) ? $this->json_data->data : [];
		$this->posts_array = isset($this->posts_cache_data->data) ? (array)$this->posts_cache_data->data : [];
		$this->add_update_posts();
		$this->update_cache();
		$latest_record_date = CFF_Group_Posts::create_next_pagination($this->json_data, $show_posts);
		$load_from_cache = false;
		if(sizeof($this->json_data) <= 0){
			if($load_more_date === false){
				$json_data = $this->get_data_json(false, null);
			}else{
				$json_data = $this->get_data_json(true, $load_more_date);
			}
			$latest_record_date = CFF_Group_Posts::create_next_pagination($this->posts_cache_data->data, $show_posts);
			$load_from_cache = true;
		}else{
			$json_data_check = json_decode($json_data);
			$json_data = $this->check_posts_returned($json_data_check, $show_posts);
			$this->json_data = json_decode($json_data);
			$this->json_data = (isset($this->json_data->data) && $this->json_data->data > 0) ? $this->json_data->data : [];
			$latest_record_date = CFF_Group_Posts::create_next_pagination($this->json_data, $show_posts);
		}

		return [
			'posts_json' => $json_data,
			'latest_record_date' => $latest_record_date,
			'load_from_cache' => $load_from_cache
		];
	}



	/**
	 *
	 * @since 3.19.3
	 * Checks if the returned posts number from the API o the transient cache
	 * Is equal to the number of posts defined
	 * IF NOT it does look for the posts from the Persistent Cache
	 *
	 * @access public
	 */
	function check_posts_returned($json_data, $show_posts){
		if(isset($json_data->data)){
			$prev_post = [];
			$result_array = [];
			foreach ($json_data->data as $single_post) {
				$is_returned =  !CFF_Group_Posts::check_duplicated_posts($prev_post, $single_post) && (!empty($single_post->message) || isset($single_post->call_to_action->type)) ;
				$prev_post = $single_post;
				if($is_returned){
					array_push($result_array, $single_post);
				}
			}
			$json_data->data = array_slice($result_array, 0, $show_posts);
			if(sizeof($json_data->data) < $show_posts){
				$remaining_num = $show_posts - sizeof($json_data->data);
				$remaining_json_data = json_decode($this->get_data_json(true, CFF_Group_Posts::create_next_pagination($json_data->data, sizeof($json_data->data))));
				$json_data->data = array_merge( $json_data->data, $remaining_json_data->data );
			}
		}
		return json_encode($json_data);
	}



	/**
	 *
	 * @since 3.19.3
	 * Add more POSTS to the Post array
	 * It can add or update
	 * @access public
	 */
	function add_update_posts(){
		$new_cached_posts = $this->posts_array;
		$posts_array_api = $this->json_data;
		foreach ($posts_array_api as $single_post) {
			if(isset($single_post->id) && ( $this->is_event_page || (!empty($single_post->message) || isset($single_post->call_to_action->type) ) ) ){
				$key = array_search($single_post->id, array_column($this->posts_array , 'id'));
				if($key !== false){
					$new_cached_posts[$key] = $single_post;
				}else{
					array_push($new_cached_posts, $single_post);
				}
			}
		}
		$this->posts_array = $new_cached_posts;
	}


	/**
	 *
	 * @since 3.19.3
	 * @access public
	 */
	function get_data_json($is_load_more, $next_urls_arr_safe){
		if($is_load_more && isset($next_urls_arr_safe)){
			$this->next_urls_arr_safe = $next_urls_arr_safe;
			$new_array = array_filter($this->posts_cache_data->data, function($single_post){
				$the_time = isset($single_post->updated_time) ? $single_post->updated_time : $single_post->created_time;
				$is_returned = ( (int)strtotime($the_time) < (int)$this->next_urls_arr_safe ) &&
				( !empty($single_post->message) || isset($single_post->call_to_action->type) );
				return $is_returned;
			});
			$latest_record = end($this->posts_cache_data->data);
			$new_array = array_slice($new_array , 0, $this->feed_options['num']);
			$this->posts_cache_data->data = sizeof($new_array) > 0 ? $new_array : [];
			if($latest_record == end($this->posts_cache_data->data)){
				$this->posts_cache_data->no_more = true;
			}
		}else{
			$this->posts_cache_data->data = array_slice($this->posts_cache_data->data,
				0, $this->feed_options['num']
			);
		}

		return json_encode($this->posts_cache_data);
	}




	/**
	 * Save latest 100 posts
	 *
	 * @since 3.19.3
	 * @access public
	 */
	function update_cache(){
		usort($this->posts_array , function($post_1, $post_2){
			$time_1 = isset($post_1->updated_time) ? $post_1->updated_time : $post_1->created_time;
			$time_2 = isset($post_2->updated_time) ? $post_2->updated_time : $post_2->created_time;
		    return strcmp(strtotime($time_2), strtotime($time_1));
		});
		$this->posts_cache_data->is_event_page = $this->is_event_page;
		$this->posts_cache_data->data = array_slice( $this->posts_array, 0, 100 );
		if(sizeof($this->posts_array) > 0){
			update_option( $this->cache_name, $this->encryption->maybe_encrypt( json_encode($this->posts_cache_data) ) , false );
		}

	}

	/**
	 * Get The pagination Values
	 *
	 * @since 3.19.3
	 * @access public
	 */

	static function check_duplicated_posts($prev_post, $ac_post){
		$ac_message		 = isset($ac_post->message) ? $ac_post->message : '';
		$ac_link 		= isset($ac_post->link) ? $ac_post->link : '';
		$ac_description = isset($ac_post->description) ? $ac_post->description : '';

		$prev_message		 = isset($prev_post->message) ? $prev_post->message : '';
		$prev_link 			= isset($prev_post->link) ? $prev_post->link : '';
		$prev_description 	= isset($prev_post->description) ? $prev_post->description : '';
		$is_duplicate = (($prev_message == $ac_message) && ($prev_link == $ac_link) && ($prev_description == $ac_description)) ? true : false;
		return $is_duplicate;
	}

	static function create_next_pagination( $json_data_arr,$show_posts ){
		if(isset($json_data_arr) && sizeof((array)$json_data_arr) > 0){
			$result_array = [];
			$prev_post = [];
			foreach ($json_data_arr as $single_post) {
				$is_returned =  (!CFF_Group_Posts::check_duplicated_posts($prev_post, $single_post));
				if($is_returned){
					array_push($result_array, $single_post);
				}
				$prev_post = $single_post;
			}
			$json_data_arr = array_slice($result_array,0,$show_posts);
			$latest_one = end($json_data_arr);
			return isset($latest_one->updated_time) ? strtotime($latest_one->updated_time) : strtotime($latest_one->created_time);
		}
		return 0;
	}


	/*
		API CALL FUNCTIONS *CRON JOB*
	*/
	/**
	 *
	 * @since 3.19.3
	 * Cron to Update the Persistent Cache for Group Posts
	 * Get the latest 100 Posts from groups and Update in
	 * @access public
	 */
	static function cron_update_group_persistent_cache(){
	    global $wpdb;
	    $table_name = $wpdb->prefix . "options";
	    $encryption = new SB_Facebook_Data_Encryption();
	    $persistent_groups = $wpdb->get_results( "
	        SELECT `option_name` AS `name`, `option_value` AS `value`
	        FROM  $table_name
	        WHERE `option_name` LIKE ('%!cff\_group\_%')
	      " );
	    foreach ($persistent_groups as $group) {
			$group_json = json_decode( $encryption->maybe_decrypt( $group->value ), true);
	    	CFF_Group_Posts::update_or_add_group($group->name, $group_json);
	    }
	}

	/**
	 * Save latest 100 posts
	 *
	 * @since 3.19.3
	 * @access public
	 */
	static function update_or_add_group($cache_name, $group_cache){
		$api_url 			= $group_cache['api_url'];
		$cached_posts 		= $group_cache['data'];
		$is_event_page 		= isset($group_cache['is_event_page']) ? $group_cache['is_event_page'] : false;
		$data_att_html 		= $group_cache['shortcode_options'];
		$new_cached_posts 	= $cached_posts;
		$encryption = new SB_Facebook_Data_Encryption();

		$posts_array_api = json_decode(CFF_Group_Posts::api_call($api_url, $data_att_html));
		foreach ($posts_array_api->data as $single_post) {
			if(isset($single_post->id) && ( $is_event_page || (!empty($single_post->message) || isset($single_post->call_to_action->type) ) ) ){
				$key = array_search($single_post->id, array_column($cached_posts , 'id'));
				if($key !== false){
					$new_cached_posts[$key] = $single_post;
				}else{
					array_push($new_cached_posts, $single_post);
				}
			}
		}

		$posts_cache_data = new \stdClass();
		$posts_cache_data->api_url = $api_url;
		$posts_cache_data->shortcode_options = $data_att_html;
		$posts_cache_data->is_event_page = $is_event_page;
		usort($new_cached_posts , function($post_1, $post_2){
			$time_1 = isset($post_1->updated_time) ? $post_1->updated_time : $post_1->created_time;
			$time_2 = isset($post_2->updated_time) ? $post_2->updated_time : $post_2->created_time;
		    return strcmp(strtotime($time_2), strtotime($time_1));
		});
		$new_cached_posts = array_slice( $new_cached_posts, 0, 100 );
		$posts_cache_data->data = $new_cached_posts;
		if(sizeof($new_cached_posts) > 0){
			update_option( $cache_name, $encryption->maybe_encrypt( json_encode( $posts_cache_data ) ), false );
		}

	}


	/**
	 * Api Call to get 100 posts from Group
	 *
	 * @since 3.19.3
	 * @access public
	 */
	static function api_call($api_url, $data_att_html){
		$api_url_100 = $api_url . '&limit=100';
		$posts_json = CFF_Utils::cff_fetchUrl( $api_url_100 );
		$FBdata = json_decode($posts_json);
		$prefix_data = '{"data":';
		$cff_featured_post =  (substr($posts_json, 0, strlen($prefix_data)) == $prefix_data)  ? false : true;
		$prefix = '{';
		if (substr($posts_json, 0, strlen($prefix)) == $prefix) $posts_json = substr($posts_json, strlen($prefix));
		$posts_json = '{"api_url":"'. $api_url .'", "shortcode_options":"'. $data_att_html .'", ' . $posts_json;
		( $cff_featured_post ) ? $FBdata = $FBdata : $FBdata = $FBdata->data;

		if( !empty($FBdata) ) {
			if( !isset($FBdata->error) ){
				return $posts_json;
			}
		}

		return '{"data":[]}';
	}

	static function group_schedule_event($cff_cache_cron_time_unix, $cff_cron_schedule){
		if ( ! wp_next_scheduled( 'group_post_scheduler_cron' ) ) {
			wp_schedule_event( $cff_cache_cron_time_unix, $cff_cron_schedule, 'group_post_scheduler_cron' );
		}
	}

	static function group_reschedule_event($cff_cache_cron_time_unix, $cff_cron_schedule){
		$timestamp = wp_next_scheduled( 'group_post_scheduler_cron' );
		if ( $timestamp ) {
			wp_clear_scheduled_hook( 'group_post_scheduler_cron' );
			wp_unschedule_event( $timestamp , 'group_post_scheduler_cron' );
		}
		if ( ! wp_next_scheduled( 'group_post_scheduler_cron' ) ) {
			wp_schedule_event( $cff_cache_cron_time_unix, $cff_cron_schedule, 'group_post_scheduler_cron' );
		}
	}

}