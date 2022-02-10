<?php
/*
@REST API for course annoucements
@author : themeum
*/


namespace TUTOR;
use WP_REST_Request;
use WP_Comment_Query;

if(!defined( 'ABSPATH' ))
exit;

class REST_Rating {
	use REST_Response;

	private $post_id;
	private $post_type = "tutor_course_rating";

	/*
	*require course id
	*return comment/review with meta by course id and post type  
	*/
	public function course_rating(WP_REST_Request $request) {
		$this->post_id = $request->get_param('id');

		global $wpdb;
		$t_comment = $wpdb->prefix."comments";
		$t_commentmeta = $wpdb->prefix."commentmeta";

		$ratings = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT c.comment_author,c.comment_author_email,comment_date,
					comment_content,comment_approved, cm.meta_value as rating 
					FROM $t_comment as c JOIN $t_commentmeta as cm ON cm.comment_id = c.comment_ID 
					WHERE c.comment_post_ID = %d AND c.comment_type = %s ",
					$this->post_id,$this->post_type
				));

		if (count($ratings)>0) {

			$response = array(
				'status_code'=> 'success',
				'message'=> __('Course rating retrieved successfully','tutor'),
				'data'=> $ratings
			);

			return self::send($response);
		}
		
		$response = array(
			'status_code'=> 'not_found',
			'message'=> __('Rating not found for given ID','tutor'),
			'data'=> []
		);

		return self::send($response);		
	}
}
