<?php
namespace TUTOR;
use WP_REST_Request;

if(!defined( 'ABSPATH' ))
exit;

class REST_Author {

	use REST_Response;

	private $user_id;

	/*
	*require user id
	*return json object with user detail 
	*/
	public function author_detail(WP_REST_Request $request) {
		$this->user_id = $request->get_param('id');
		global $wpdb;
		$table = $wpdb->prefix."users";
		//author obj
		$author = $wpdb->get_row(
			$wpdb->prepare(
			"SELECT user_email, user_registered, display_name FROM $table WHERE ID = %d", 
			$this->user_id
		));

		if($author) {
			//get author course id
			$author->courses = get_user_meta($this->user_id,'_tutor_instructor_course_id',false);

			$response = array(
				'status_code'=> 'success',
				'message'=> __('Author details retrieved successfully','tutor'),
				'data'=> $author
			);

			return self::send($response);
		}
		
		$response = array(
			'status_code'=> 'invalid_id',
			'message'=> __('Author not found','tutor'),
			'data'=> []
		);

		return self::send($response);		
	}
}
