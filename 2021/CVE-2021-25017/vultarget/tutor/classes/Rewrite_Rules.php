<?php

namespace TUTOR;

if (!defined('ABSPATH'))
	exit;

class Rewrite_Rules extends Tutor_Base {

	public function __construct() {
		parent::__construct();

		add_filter('query_vars', array($this, 'tutor_register_query_vars'));
		add_action('generate_rewrite_rules', array($this, 'add_rewrite_rules'));

		//Lesson Permalink
		add_filter('post_type_link', array($this, 'change_lesson_single_url'), 1, 2);
	}

	public function tutor_register_query_vars($vars) {
		$vars[] = 'course_subpage';
		$vars[] = 'lesson_video';
		$vars[] = 'tutor_dashboard_page';
		$vars[] = 'tutor_dashboard_sub_page';

		$enable_public_profile = tutor_utils()->get_option('enable_public_profile');
		if ($enable_public_profile) {
			$vars[] = 'tutor_student_username';
			$vars[] = 'profile_sub_page';
		}

		return $vars;
	}

	public function add_rewrite_rules($wp_rewrite) {
		$new_rules = array(
			//Lesson Permalink
			$this->course_post_type . "/(.+?)/{$this->lesson_base_permalink}/(.+?)/?$" => "index.php?post_type={$this->lesson_post_type}&name=" . $wp_rewrite->preg_index(2),
			//Quiz Permalink
			$this->course_post_type . "/(.+?)/tutor_quiz/(.+?)/?$" => "index.php?post_type=tutor_quiz&name=" . $wp_rewrite->preg_index(2),
			//Assignments URL
			$this->course_post_type . "/(.+?)/assignments/(.+?)/?$" => "index.php?post_type=tutor_assignments&name=" . $wp_rewrite->preg_index(2),
			//Zoom Meeting
			$this->course_post_type . "/(.+?)/zoom-meeting/(.+?)/?$" => "index.php?post_type=tutor_zoom_meeting&name=" . $wp_rewrite->preg_index(2),

			//Private Video URL
			"video-url/(.+?)/?$" => "index.php?post_type={$this->lesson_post_type}&lesson_video=true&name=" . $wp_rewrite->preg_index(1),
			//Student Public Profile URL
			"profile/(.+?)/(.+?)/?$" => "index.php?tutor_student_username=" . $wp_rewrite->preg_index(1) . "&profile_sub_page=" . $wp_rewrite->preg_index(2),
			"profile/(.+?)/?$" => "index.php?tutor_student_username=" . $wp_rewrite->preg_index(1),
		);

		//Nav Items
		$course_nav_items = tutor_utils()->course_sub_pages();
		$course_nav_items = apply_filters('tutor_course/single/enrolled/nav_items_rewrite', $course_nav_items);
		//$course_nav_items = array_keys($course_nav_items);

		if (is_array($course_nav_items) && count($course_nav_items)) {
			foreach ($course_nav_items as $nav_key => $nav_item) {
				$new_rules[$this->course_post_type . "/(.+?)/{$nav_key}/?$"] = "index.php?post_type={$this->course_post_type}&name=" . $wp_rewrite->preg_index(1) . '&course_subpage=' . $nav_key;
			}
		}

		//Student Dashboard URL
		$dashboard_pages = tutor_utils()->tutor_dashboard_permalinks();
		$dashboard_page_id = (int) tutor_utils()->get_option('tutor_dashboard_page_id');
		$dashboard_page_slug = get_post_field('post_name', $dashboard_page_id);

		foreach ($dashboard_pages as $dashboard_key => $dashboard_page) {
			$new_rules["({$dashboard_page_slug})/{$dashboard_key}/?$"] = 'index.php?pagename=' . $wp_rewrite->preg_index(1) . '&tutor_dashboard_page=' . $dashboard_key;

			//Sub Page of dashboard sub page
			//regext = ([^/]*)
			$new_rules["({$dashboard_page_slug})/{$dashboard_key}/(.+?)/?$"] = 'index.php?pagename=' . $wp_rewrite->preg_index(1) . '&tutor_dashboard_page=' . $dashboard_key . '&tutor_dashboard_sub_page=' . $wp_rewrite->preg_index(2);
		}

		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}

	/**
	 * @param $post_link
	 * @param int $id
	 *
	 * @return string
	 *
	 * Change the lesson permalink
	 */
	function change_lesson_single_url($post_link, $id = 0) {
		$post = get_post($id);

		global $wpdb;

		$course_base_slug = 'sample-course';

		if (is_object($post) && $post->post_type == $this->lesson_post_type) {
			//Lesson Permalink
			$course_id = tutor_utils()->get_course_id_by('lesson', $post->ID);

			if ($course_id) {
				$course = $wpdb->get_row($wpdb->prepare("SELECT post_name from {$wpdb->posts} where ID = %d ", $course_id));
				if ($course) {
					$course_base_slug = $course->post_name;
				}
				return home_url("/{$this->course_post_type}/{$course_base_slug}/{$this->lesson_base_permalink}/" . $post->post_name . '/');
			} else {
				return home_url("/{$this->course_post_type}/sample-course/{$this->lesson_base_permalink}/" . $post->post_name . '/');
			}
		} elseif (is_object($post) && $post->post_type === 'tutor_quiz') {
			//Quiz Permalink
			$course = $wpdb->get_row($wpdb->prepare("SELECT ID, post_name, post_type, post_parent from {$wpdb->posts} where ID = %d ", $post->post_parent));
			if ($course) {
				//Checking if this topic
				if ($course->post_type !== $this->course_post_type) {
					$course = $wpdb->get_row($wpdb->prepare("SELECT ID, post_name, post_type, post_parent from {$wpdb->posts} where ID = %d ", $course->post_parent));
				}
				//Checking if this lesson
				if (isset($course->post_type) && $course->post_type !== $this->course_post_type) {
					$course = $wpdb->get_row($wpdb->prepare("SELECT ID, post_name, post_type, post_parent from {$wpdb->posts} where ID = %d ", $course->post_parent));
				}

				$course_post_name = isset($course->post_name) ? $course->post_name : 'sample-course';
				return home_url("/{$this->course_post_type}/{$course_post_name}/tutor_quiz/{$post->post_name}/");
			} else {
				return home_url("/{$this->course_post_type}/sample-course/tutor_quiz/{$post->post_name}/");
			}
		}
		return $post_link;
	}
}
