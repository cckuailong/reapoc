<?php
/**
 * RestAPI class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.5.0
 */

namespace TUTOR;

if (!defined('ABSPATH'))
	exit;

class RestAPI {

	use Custom_Validation;

	private $namespace = 'tutor/v1';

	protected $course_post_type;

	private $path;

	private $courseObj;

	private $topicObj;

	private $lessonObj;

	private $annoucementObj;

	private $quizObj;

	private $authorObj;

	private $ratingObj;
	
	public function __construct() {

		$this->path = plugin_dir_path(TUTOR_FILE);
		
		spl_autoload_register(array($this, 'loader'));


		$this->courseObj = new REST_Course;
		$this->topicObj = new REST_Topic;
		$this->lessonObj = new REST_Lesson;
		$this->annoucementObj = new REST_Course_Announcement;
		$this->quizObj = new REST_Quiz;
		$this->authorObj = new REST_Author;
		$this->ratingObj = new REST_Rating;

		add_action('rest_api_init', array($this, 'init_routes'));
	}


	private function loader($className) {
		if (!class_exists($className)) {
			$className = preg_replace(
				array('/([a-z])([A-Z])/', '/\\\/'),
				array('$1$2', DIRECTORY_SEPARATOR),
				$className
			);

			$className = str_replace('TUTOR' . DIRECTORY_SEPARATOR, 'restapi' . DIRECTORY_SEPARATOR, $className);
			$file_name = $this->path . $className . '.php';

			if (file_exists($file_name)) {
				require_once $file_name;
			}
		}
	}

	/*
	init all routes for api
	*/
	public function init_routes() {
		//courses
		register_rest_route(
			$this->namespace,
			'/courses',
			array(
				'methods' => "GET",
				'callback' => array(
					$this->courseObj, 'course'
				),
				'permission_callback' => '__return_true'
			)
		);

		//courses by terms cat and tag
		register_rest_route(
			$this->namespace,
			'/course-by-terms',
			array(
				'methods' => "POST",
				'callback' => array(
					$this->courseObj, 'course_by_terms'
				),
				'permission_callback' => '__return_true'
			)
		);

		//courses by terms cat and tag
		register_rest_route(
			$this->namespace,
			'/course-sorting-by-price',
			array(
				'methods' => "GET",
				'callback' => array(
					$this->courseObj, 'course_sort_by_price'
				),
				'args' => array(
					'order' => array(
						'required' => true,
						'type' => 'string',
						'validate_callback' => function ($order) {
							return $this->validate_order($order);
						}
					),
					'page' => array(
						'required' => false,
						'type' => 'number'
					)
				),
				'permission_callback' => '__return_true'
			)
		);

		//course details
		register_rest_route(
			$this->namespace,
			'/course-detail/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array(
					$this->courseObj, 'course_detail'
				),
				'args' => array(
					'id' => array(
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					)
				),
				'permission_callback' => '__return_true'
			)
		);

		//course topic
		register_rest_route(
			$this->namespace,
			'/course-topic/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array(
					$this->topicObj, 'course_topic'
				),
				'args' => array(
					'id' => array(
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					)
				),
				'permission_callback' => '__return_true'
			)
		);

		//lesson by topic
		register_rest_route(
			$this->namespace,
			'/lesson/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array(
					$this->lessonObj, 'topic_lesson'
				),
				'args' => array(
					'id' => array(
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					)
				),
				'permission_callback' => '__return_true'
			)
		);

		//course annoucement by course id
		register_rest_route(
			$this->namespace,
			'/course-annoucement/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array(
					$this->annoucementObj, 'course_annoucement'
				),
				'args' => array(
					'id' => array(
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					)
				),
				'permission_callback' => '__return_true'
			)
		);

		//quiz by topic id
		register_rest_route(
			$this->namespace,
			'/quiz/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array(
					$this->quizObj, 'quiz_with_settings'
				),
				'args' => array(
					'id' => array(
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					)
				),
				'permission_callback' => '__return_true'
			)
		);

		//quiz question answer by quiz id
		register_rest_route(
			$this->namespace,
			'/quiz-question-answer/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array(
					$this->quizObj, 'quiz_question_ans'
				),
				'args' => array(
					'id' => array(
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					)
				),
				'permission_callback' => '__return_true'
			)
		);

		//quiz attempt details by quiz id
		register_rest_route(
			$this->namespace,
			'/quiz-attempt-details/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array(
					$this->quizObj, 'quiz_attempt_details'
				),
				'args' => array(
					'id' => array(
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					)
				),
				'permission_callback' => '__return_true'
			)
		);

		//author detail by id
		register_rest_route(
			$this->namespace,
			'/author-information/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array(
					$this->authorObj, 'author_detail'
				),
				'args' => array(
					'id' => array(
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					)
				),
				'permission_callback' => '__return_true'
			)
		);

		//reviews by course id
		register_rest_route(
			$this->namespace,
			'/course-rating/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => array(
					$this->ratingObj, 'course_rating'
				),
				'args' => array(
					'id' => array(
						'validate_callback' => function ($param) {
							return is_numeric($param);
						}
					)
				),
				'permission_callback' => '__return_true'
			)
		);
	}
}
