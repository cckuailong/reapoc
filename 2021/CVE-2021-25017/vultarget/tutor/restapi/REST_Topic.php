<?php
/*
@REST API for course topics
@author : themeum
*/

namespace TUTOR;
use WP_REST_Request;
use WP_Query;

if(!defined('ABSPATH'))
exit;

class REST_Topic {
	use REST_Response;

	private $post_parent;
	private $post_type = "topics";

	/*
	*require rest request
	*return topic by course id
	*/
	public function course_topic(WP_REST_Request $request) {
		$this->post_parent = $request->get_param('id');

		global $wpdb;

		$table = $wpdb->prefix."posts";

		$result = $wpdb->get_results(
			$wpdb->prepare("SELECT ID, post_title, post_content, post_name FROM $table WHERE post_type = %s AND post_parent = %d", $this->post_type, $this->post_parent)
		);

		if (count($result)>0) {
			$response = array(
				'status_code'=> "get_topic",
				"message"=> __('Topic retrieved successfully','tutor'),
				'data'=> $result
			);			
			
			return self::send($response);
		}
		$response = array(
			'status_code'=> "not_found",
			"message"=> __('Topic not found for given ID','tutor'),
			'data'=> []
		);			
		
		return self::send($response);
	}
}
