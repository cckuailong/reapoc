<?php
/**
 * Class Shortcode
 * @package TUTOR
 *
 * @since v.1.0.0
 */

namespace TUTOR;

if (!defined('ABSPATH'))
	exit;

class Shortcode {

	private $instructor_layout = array(
        'pp-top-full',
        'pp-cp',
        'pp-top-left',
        'pp-left-middle',
        'pp-left-full'
	);
	
	public function __construct() {
		add_shortcode('tutor_student_registration_form', array($this, 'student_registration_form'));
		add_shortcode('tutor_dashboard', array($this, 'tutor_dashboard'));
		add_shortcode('tutor_instructor_registration_form', array($this, 'instructor_registration_form'));
		add_shortcode('tutor_course', array($this, 'tutor_course'));

		add_shortcode('tutor_instructor_list', array($this, 'tutor_instructor_list'));
		add_action('tutor_options_after_instructors', array($this, 'tutor_instructor_layout'));
		add_action( 'wp_ajax_load_filtered_instructor', array($this, 'load_filtered_instructor') );
		add_action( 'wp_ajax_nopriv_load_filtered_instructor', array($this, 'load_filtered_instructor') );
	}

	/**
	 * @return mixed
	 *
	 * Instructor Registration Shortcode
	 *
	 * @since v.1.0.0
	 */
	public function student_registration_form() {
		ob_start();
		if (is_user_logged_in()) {
			tutor_load_template('dashboard.logged-in');
		} else {
			tutor_load_template('dashboard.registration');
		}
		return apply_filters('tutor/student/register', ob_get_clean());
	}

	/**
	 * @return mixed
	 *
	 * Tutor Dashboard for students
	 *
	 * @since v.1.0.0
	 */
	public function tutor_dashboard() {
		global $wp_query;

		ob_start();
		if (is_user_logged_in()) {
			/**
			 * Added isset() Condition to avoid infinite loop since v.1.5.4
			 * This has cause error by others plugin, Such AS SEO
			 */

			if (!isset($wp_query->query_vars['tutor_dashboard_page'])) {
				tutor_load_template('dashboard', array('is_shortcode' => true));
			}
		} else {
			tutor_load_template('global.login');
		}
		return apply_filters('tutor_dashboard/index', ob_get_clean());
	}

	/**
	 * @return mixed
	 *
	 * Instructor Registration Shortcode
	 *
	 * @since v.1.0.0
	 */
	public function instructor_registration_form() {
		ob_start();
		if (is_user_logged_in()) {
			tutor_load_template('dashboard.instructor.logged-in');
		} else {
			tutor_load_template('dashboard.instructor.registration');
		}
		return apply_filters('tutor_dashboard/student/index', ob_get_clean());
	}

	/**
	 * @param $atts
	 *
	 * @return string
	 *
	 * Shortcode for getting course
	 */
	public function tutor_course($atts) {
		$course_post_type = tutor()->course_post_type;

		$a = shortcode_atts(array(
			'post_type' 	=> $course_post_type,
			'post_status'   => 'publish',

			'id'       		=> '',
			'exclude_ids'   => '',
			'category'     	=> '',

			'orderby'       => 'ID',
			'order'         => 'DESC',
			'count'     	=> 6,
			'paged'			=> get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
		), $atts);

		if (!empty($a['id'])) {
			$ids = (array) explode(',', $a['id']);
			$a['post__in'] = $ids;
		}

		if (!empty($a['exclude_ids'])) {
			$exclude_ids = (array) explode(',', $a['exclude_ids']);
			$a['post__not_in'] = $exclude_ids;
		}
		if (!empty($a['category'])) {
			$category = (array) explode(',', $a['category']);

			$a['tax_query'] = array(
				array(
					'taxonomy' => 'course-category',
					'field'    => 'term_id',
					'terms'    => $category,
					'operator' => 'IN',
				),
			);
		}
		$a['posts_per_page'] = (int) $a['count'];

		wp_reset_query();
		$the_query = new \WP_Query( $a );
		ob_start();

		$GLOBALS['the_custom_query'] = $the_query;

		$GLOBALS['tutor_shortcode_arg'] = array(
			'include_course_filter' => isset($atts['course_filter']) ? $atts['course_filter'] === 'on' : null,
			'column_per_row' => isset($atts['column_per_row']) ? $atts['column_per_row'] : null,
			'course_per_page' => $a['posts_per_page'],
			'show_pagination' => isset( $atts['show_pagination'] ) ? $atts['show_pagination'] : 'off',
		);

		tutor_load_template('shortcode.tutor-course');
		$output = ob_get_clean();
		wp_reset_postdata();

		return $output;
	}

	private function prepare_instructor_list($current_page, $atts, $cat_ids = array(), $keyword = '') {

		$limit = (int)sanitize_text_field(tutils()->array_get('count', $atts, 9));
		$page = $current_page - 1;

		$instructors = tutor_utils()->get_instructors($limit*$page, $limit, $keyword, '', '', '', 'approved', $cat_ids);
		$next_instructors = tutor_utils()->get_instructors($limit*$current_page, $limit, $keyword, '', '', '', 'approved', $cat_ids);

		$previous_page = $page>0 ? $current_page-1 : null;
		$next_page = (is_array($next_instructors) && count($next_instructors)>0) ? $current_page+1 : null;
		
		$layout = sanitize_text_field(tutils()->array_get('layout', $atts, ''));
		$layout = in_array($layout, $this->instructor_layout) ? $layout : tutor_utils()->get_option('instructor_list_layout', $this->instructor_layout[0]);

		$payload=array(
			'instructors' 	=> is_array($instructors) ? $instructors : array(), 
			'next_page' 	=> $next_page, 
			'previous_page' => $previous_page,
			'column_count' 	=> sanitize_text_field(tutils()->array_get('column_per_row', $atts, 3)),
			'layout' 		=> $layout,
			'limit' 		=> $limit,
			'current_page' 	=> $current_page
		);

		return $payload;
	}
	
	/**
	 * @param $atts
	 *
	 * @return string
	 *
	 * Shortcode for getting instructors
	 */
	public function tutor_instructor_list($atts) {

		!is_array( $atts ) ? $atts = array() : 0;

		$current_page = (int)tutor_utils()->array_get('instructor-page', $_GET, 1);
		$current_page = $current_page>=1 ? $current_page : 1;
		
		$show_filter = isset( $atts['filter'] ) ? $atts['filter']=='on' : tutor_utils()->get_option( 'instructor_list_show_filter', false );
		
		// Get instructor list to sow
		$payload = $this->prepare_instructor_list($current_page, $atts);
		$payload['show_filter'] = $show_filter;

		ob_start();
		tutor_load_template('shortcode.tutor-instructor', $payload);
		$content = ob_get_clean();

		if($show_filter) {
			
			$course_cats = get_terms( array(
				'taxonomy' => 'course-category',
				'hide_empty' => true,
				'childless' => true
			) );

			$attributes = $payload;
			unset( $attributes['instructors'] );

			$payload = array( 
				'show_filter' => $show_filter,
				'content' => $content,
				'categories' => $course_cats,
				'attributes' => array_merge( $atts, $attributes )
			);

			ob_start();

			tutor_load_template('shortcode.instructor-filter',  $payload);
		
			$content = ob_get_clean();
		}

		return $content;
	}

	public function load_filtered_instructor() {
		tutor_utils()->checking_nonce();

		$attributes = (array)tutils()->array_get('attributes', $_POST, array());
		$current_page = (int)sanitize_text_field(tutils()->array_get('current_page', $attributes, 1));
		$keyword = (string)sanitize_text_field(tutils()->array_get('keyword', $_POST, ''));

		$category = (array)tutils()->array_get('category', $_POST, array());
		$category = array_filter($category, function($cat) {
			return is_numeric($cat);
		});

		$payload = $this->prepare_instructor_list($current_page, $attributes, $category, $keyword);

		tutor_load_template('shortcode.tutor-instructor', $payload);
		exit;
	}
	
	/**
	 * Show layout selection dashboard in instructor setting
	 */
	public function tutor_instructor_layout(){
		tutor_load_template('instructor-setting', array('templates'=>$this->instructor_layout));
	}
}
