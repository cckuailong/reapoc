<?php
/*
@REST API Lesson
@author : themeum
*/


namespace TUTOR;
use WP_REST_Request;

if(!defined('ABSPATH'))
exit;

class REST_Lesson {

	use REST_Response;
	
	private $post_type;
	private $post_parent;

	public function __construct() {
		$this->post_type = tutor()->lesson_post_type;
	}

	public function topic_lesson(WP_REST_Request $request) {
		$this->post_parent = $request->get_param('id');
		global $wpdb;

		$table = $wpdb->prefix."posts";

		$lessons = $wpdb->get_results(
			$wpdb->prepare("SELECT ID, post_title, post_content, post_name, (SELECT post_parent from {$table} WHERE ID = {$this->post_parent} ) as course_id FROM $table WHERE post_type = %s AND post_parent = %d", $this->post_type, $this->post_parent)
		);	
		
		$data = array();

		if(count($lessons)>0) {
			foreach ($lessons as $lesson) {
				$attachments = [];
				$attachments_id = get_post_meta($lesson->ID,'_tutor_attachments',false);
				$attachments_id = $attachments_id[0];
				foreach($attachments_id as $id) {
					$guid = get_the_guid($id);
					array_push($attachments, $guid);		
				}

				$lesson->attachments = $attachments;
				$lesson->thumbnail = get_the_post_thumbnail_url($lesson->ID);
				$lesson->video = get_post_meta($lesson->ID, '_video',false);
				array_push($data, $lesson);
			}

			$response = array(
				'status_code'=> "success",
				"message"=> __('Lesson retrieved successfully','tutor'),
				'data'=> $data
			);

			return self::send($response);		
		}
		$response = array(
			'status_code'=> "not_found",
			"message"=> __('Lesson not found for given topic ID','tutor'),
			'data'=> []
		);
		return self::send($response);
	} 
}
