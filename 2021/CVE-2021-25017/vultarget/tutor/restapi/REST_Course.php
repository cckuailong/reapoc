<?php
/*
@REST API for courses
@author : themeum
*/

namespace TUTOR;
use WP_REST_Request;
use WP_Query;

if( !defined ('ABSPATH'))
exit;

class REST_Course  {
	
	use REST_Response;

	private $post_type;

	private $course_cat_tax = "course-category";

	private $course_tag_tax = "course-tag";

	public function __construct() {
		$this->post_type = tutor()->course_post_type;
	}

	/*
	*require rest request
	*return course info
	*pagination enable
	*category,tags terms included
	*/
	public function course(WP_REST_Request $request) {
		$order = sanitize_text_field($request->get_param('order'));
		$orderby = sanitize_text_field($request->get_param('orderby'));
		$paged = sanitize_text_field($request->get_param('paged'));

		$args = array(
	        'post_type' => $this->post_type,
	        'post_status' => 'publish',
	        'posts_per_page' => 10, 
	        'paged' => $paged ? $paged : 1,
	        'order' => $order ? $order : 'ASC',
	        'orderby' => $orderby ? $orderby :'title'
		);

		$query = new WP_Query($args);


		//if post found
		if(count($query->posts)>0) {
			//unset filter properpty
			array_map(function($post){
				unset($post->filter);
			}, $query->posts);

			$data = [
				'posts'=> [],
				'total_course' => $query->found_posts,
				'total_page' => $query->max_num_pages				
			];

			foreach($query->posts as $post) {
				$category = wp_get_post_terms($post->ID,$this->course_cat_tax);

				$tag = wp_get_post_terms($post->ID,$this->course_tag_tax);

				$post->course_category = $category;

				$post->course_tag = $tag;

				array_push($data['posts'], $post);

			}

			$response = array(
				'status_code'=> "success",
				"message"=> __('Course retrieved successfully','tutor'),
				'data'=> $data
			);

			return self::send($response);			
		}

		$response = array(
			'status_code'=> "not_found",
			"message"=> __('Course not found','tutor'),
			'data'=> []
		);

		return self::send($response);
	}

	/*
	*require rest request
	*return post meta items
	*/
	function course_detail(WP_REST_Request $request) {
		$post_id = $request->get_param('id');

		$detail = array(

			'course_settings' =>get_post_meta($post_id,'_tutor_course_settings',false),

			'course_price_type' =>get_post_meta($post_id,'_tutor_course_price_type',false),

			'course_duration' =>get_post_meta($post_id,'_course_duration',false),

			'course_level' =>get_post_meta($post_id,'_tutor_course_level',false),

			'course_benefits' =>get_post_meta($post_id,'_tutor_course_benefits',false),

			'course_requirements' =>get_post_meta($post_id,'_tutor_course_requirements',false),

			'course_target_audience' =>get_post_meta($post_id,'_tutor_course_target_audience',false),

			'course_material_includes' =>get_post_meta($post_id,'_tutor_course_material_includes',false),

			'video' =>get_post_meta($post_id,'_video',false),
			
			'disable_qa' =>get_post_meta($post_id,'_tutor_disable_qa','_video',false)
		);

		if($detail) {
			$response = array(
				'status_code'=> "course_detail",
				"message"=> __('Course detail retrieved successfully','tutor'),
				'data'=> $detail
			);
			return self::send($response);				
		}
		$response = array(
			'status_code'=> "course_detail",
			"message"=> __('Detail not found for given ID','tutor'),
			'data'=> []
		);		

		return self::send($response);
	}

	/*
	*return post type terms
	*/
	public function course_by_terms(WP_REST_Request $request) {
		$post_fields = $request->get_params();
		$validate_err = $this->validate_terms($post_fields);
		
		//check array or not 
		if(count($validate_err)>0) {
			$response = array(
				'status_code'=> "validation_error",
				"message"=> $validate_err,
				'data'=> []
			);	

			return self::send($response);
		}

		//sanitize terms
		$categories = sanitize_term( $request['categories'], $this->course_cat_tax, $context = 'db' );

		$tags = sanitize_term( $request['tags'], $this->course_tag_tax, $context = 'db' );

		$args = array(
		    'post_type' => $this->post_type,
		    'tax_query' => array(
		        'relation' => 'OR',
		        array(
		            'taxonomy' => $this->course_cat_tax,
		            'field'    => 'name',
		            'terms'    => $categories
		        ),
		        array(
		            'taxonomy' => $this->course_tag_tax,
		            'field'    => 'name',
		            'terms'    => $tags
		            
		        )
		    )
		);

		$query = new WP_Query ($args);

		if(count($query->posts)>0) {
			//unset filter proterty
			array_map(function($post){
				unset($post->filter);
			}, $query->posts);			

			$response = array(
				'status_code'=> "success",
				"message"=> __("Course retrieved successfully",'tutor'),
				'data'=> $query->posts
			);	

			return self::send($response);
		}

		$response = array(
			'status_code'=> "not_found",
			"message"=> __("Course not found for given terms",'tutor'),
			'data'=> []
		);	
		return self::send($response);
	}

	/*
	*categories array validation
	*tags array validation
	*/
	public function validate_terms(array $post) {
		$categories = $post['categories'];
		$tags = $post['tags'];

		$error = [];

		if (!is_array($categories))  {
			array_push($error,__('Categories field is not an array','tutor'));
		}
					
		if (!is_array($tags)) {
			array_push($error,__('Tags field is not an array','tutor'));
		}

		return $error;
	}

	public function course_sort_by_price(WP_REST_Request $request) {
		$order = $request->get_param('order');
		$paged = $request->get_param('page');

		$order = sanitize_text_field($order);
		$paged = sanitize_text_field($paged);

		$args = array(
		    'post_type'=> 'product',
		    'post_status'=> 'publish',
		   	'posts_per_page' => 10, 
        	'paged'=> $paged ? $paged : 1,

		    'meta_key'=> '_regular_price',
		    'orderby'=> 'meta_value_num',
		    'order'=> $order,
		    'meta_query'=> array(
		    	'relation'=>'AND',
		        array(
		            'key'=> '_tutor_product',
		            'value'=> "yes"
		           
		        )
		    )
		);

		$query = new WP_Query( $args );

		if (count($query->posts)>0) {
			//unset filter property
			array_map(function($post){
				unset($post->filter);
			}, $query->posts);

			$posts = [];

			foreach ($query->posts as $post) {
				$post->price = get_post_meta($post->ID,'_regular_price', true);
				array_push($posts, $post);
			}

			$data = array(
				'posts'=> $posts,
				'total_course' => $query->found_posts,
				'total_page' => $query->max_num_pages
			);

			$response = array(
				'status_code'=> 'success',
				'message'=> __('Course retrieved successfully','tutor'),
				'data'=> $data
			);

			return self::send($response);
		}
	
		$response = array(
			'status'=> 'not_found',
			'message'=> __('Course not found','tutor'),
			'data'=> []
		);
		return self::send($response);
	}
}
