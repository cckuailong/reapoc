<?php
namespace TUTOR;
if ( ! defined( 'ABSPATH' ) )
	exit;

class Post_types{
	
	public $course_post_type;
	public $lesson_post_type;

	public function __construct() {
		$this->course_post_type = tutor()->course_post_type;
		$this->lesson_post_type = tutor()->lesson_post_type;
		
		add_action( 'init', array($this, 'register_course_post_types') );
		add_action( 'init', array($this, 'register_lesson_post_types') );
		add_action( 'init', array($this, 'register_quiz_post_types') );
		add_action( 'init', array($this, 'register_topic_post_types') );
		add_action( 'init', array($this, 'register_assignments_post_types') );

		add_filter( 'gutenberg_can_edit_post_type', array( $this, 'gutenberg_can_edit_post_type' ), 10, 2 );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'gutenberg_can_edit_post_type' ), 10, 2 );

		/**
		 * Customize the message of course
		 */
		add_filter( 'post_updated_messages', array($this, 'course_updated_messages') );

		/**
		 * Since 1.4.0
		 */
		add_action( 'init', array($this, 'register_tutor_enrolled_post_types') );
	}
	
	public function register_course_post_types() {
		$course_post_type = $this->course_post_type;
		$courses_base_slug = apply_filters('tutor_courses_base_slug', $course_post_type);

		$labels = array(
			'name'                      => _x( 'Courses', 'post type general name', 'tutor' ),
			'singular_name'             => _x( 'Course', 'post type singular name', 'tutor' ),
			'menu_name'                 => _x( 'Courses', 'admin menu', 'tutor' ),
			'name_admin_bar'            => _x( 'Course', 'add new on admin bar', 'tutor' ),
			'add_new'                   => _x( 'Add New', 'tutor course add', 'tutor' ),
			'add_new_item'              => __( 'Add New Course', 'tutor' ),
			'new_item'                  => __( 'New Course', 'tutor' ),
			'edit_item'                 => __( 'Edit Course', 'tutor' ),
			'view_item'                 => __( 'View Course', 'tutor' ),
			'all_items'                 => __( 'Courses', 'tutor' ),
			'search_items'              => __( 'Search Courses', 'tutor' ),
			'parent_item_colon'         => __( 'Parent Courses:', 'tutor' ),
			'not_found'                 => __( 'No courses found.', 'tutor' ),
			'not_found_in_trash'        => __( 'No courses found in Trash.', 'tutor' )
		);

		$args = array(
			'labels'                    => $labels,
			'description'               => __( 'Description.', 'tutor' ),
			'public'                    => true,
			'publicly_queryable'        => true,
			'show_ui'                   => true,
			'show_in_menu'              => 'tutor',
			'query_var'                 => true,
			'rewrite'                   => array( 'slug' => $courses_base_slug, 'with_front' => false ),
			'menu_icon'                 => 'dashicons-book-alt',
			'capability_type'           => 'post',
			'has_archive'               => true,
			'hierarchical'              => false,
			'menu_position'             => null,
			'taxonomies'                => array( 'course-category', 'course-tag' ),
			'supports'                  => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author'),
			'show_in_rest'              => true,

			'capabilities' => array(
				'edit_post'             => 'edit_tutor_course',
				'read_post'             => 'read_tutor_course',
				'delete_post'           => 'delete_tutor_course',
				'delete_posts'          => 'delete_tutor_courses',
				'edit_posts'            => 'edit_tutor_courses',
				'edit_others_posts'     => 'edit_others_tutor_courses',
				'publish_posts'         => 'publish_tutor_courses',
				'read_private_posts'    => 'read_private_tutor_courses',
				'create_posts'          => 'edit_tutor_courses',
			),

		);

		register_post_type($course_post_type, $args);

		/**
		 * Taxonomy
		 */
		$labels = array(
			'name'                       => _x( 'Course Categories', 'taxonomy general name', 'tutor' ),
			'singular_name'              => _x( 'Category', 'taxonomy singular name', 'tutor' ),
			'search_items'               => __( 'Search Categories', 'tutor' ),
			'popular_items'              => __( 'Popular Categories', 'tutor' ),
			'all_items'                  => __( 'All Categories', 'tutor' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Category', 'tutor' ),
			'update_item'                => __( 'Update Category', 'tutor' ),
			'add_new_item'               => __( 'Add New Category', 'tutor' ),
			'new_item_name'              => __( 'New Category Name', 'tutor' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'tutor' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'tutor' ),
			'choose_from_most_used'      => __( 'Choose from the most used categories', 'tutor' ),
			'not_found'                  => __( 'No categories found.', 'tutor' ),
			'menu_name'                  => __( 'Course Categories', 'tutor' ),
		);

		$args = array(
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'show_in_rest'          => true,
			'rewrite'               => array( 'slug' => 'course-category' ),
		);

		register_taxonomy( 'course-category', $this->course_post_type, $args );

		$labels = array(
			'name'                       => _x( 'Tags', 'taxonomy general name', 'tutor' ),
			'singular_name'              => _x( 'Tag', 'taxonomy singular name', 'tutor' ),
			'search_items'               => __( 'Search Tags', 'tutor' ),
			'popular_items'              => __( 'Popular Tags', 'tutor' ),
			'all_items'                  => __( 'All Tags', 'tutor' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Tag', 'tutor' ),
			'update_item'                => __( 'Update Tag', 'tutor' ),
			'add_new_item'               => __( 'Add New Tag', 'tutor' ),
			'new_item_name'              => __( 'New Tag Name', 'tutor' ),
			'separate_items_with_commas' => __( 'Separate Tags with commas', 'tutor' ),
			'add_or_remove_items'        => __( 'Add or remove Tags', 'tutor' ),
			'choose_from_most_used'      => __( 'Choose from the most used Tags', 'tutor' ),
			'not_found'                  => __( 'No Tags found.', 'tutor' ),
			'menu_name'                  => __( 'Tags', 'tutor' ),
		);

		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'show_in_rest'          => true,
			'rewrite'               => array( 'slug' => 'course-tag' ),
		);

		register_taxonomy( 'course-tag', $this->course_post_type, $args );
	}

	public function register_lesson_post_types() {
		$lesson_post_type = $this->lesson_post_type;
		$lesson_base_slug = apply_filters('tutor_lesson_base_slug', $lesson_post_type);

		$labels = array(
			'name'               => _x( 'Lessons', 'post type general name', 'tutor' ),
			'singular_name'      => _x( 'Lesson', 'post type singular name', 'tutor' ),
			'menu_name'          => _x( 'Lessons', 'admin menu', 'tutor' ),
			'name_admin_bar'     => _x( 'Lesson', 'add new on admin bar', 'tutor' ),
			'add_new'            => _x( 'Add New', "tutor lesson add", 'tutor' ),
			'add_new_item'       => __( 'Add New Lesson', 'tutor' ),
			'new_item'           => __( 'New Lesson', 'tutor' ),
			'edit_item'          => __( 'Edit Lesson', 'tutor' ),
			'view_item'          => __( 'View Lesson', 'tutor' ),
			'all_items'          => __( 'Lessons', 'tutor' ),
			'search_items'       => __( 'Search Lessons', 'tutor' ),
			'parent_item_colon'  => __( 'Parent Lessons:', 'tutor' ),
			'not_found'          => __( 'No lessons found.', 'tutor' ),
			'not_found_in_trash' => __( 'No lessons found in Trash.', 'tutor' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'tutor' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $lesson_base_slug ),
			'menu_icon'         => 'dashicons-list-view',
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor'),
			'exclude_from_search' => apply_filters('tutor_lesson_exclude_from_search', true),
			'capabilities' => array(
				'edit_post'          => 'edit_tutor_lesson',
				'read_post'          => 'read_tutor_lesson',
				'delete_post'        => 'delete_tutor_lesson',
				'delete_posts'       => 'delete_tutor_lessons',
				'edit_posts'         => 'edit_tutor_lessons',
				'edit_others_posts'  => 'edit_others_tutor_lessons',
				'publish_posts'      => 'publish_tutor_lessons',
				'read_private_posts' => 'read_private_tutor_lessons',
				'create_posts'       => 'edit_tutor_lessons',
			),
		);

		register_post_type($lesson_post_type, $args );
	}
	
	public function register_quiz_post_types() {
		$labels = array(
			'name'               => _x( 'Quizzes', 'post type general name', 'tutor' ),
			'singular_name'      => _x( 'Quiz', 'post type singular name', 'tutor' ),
			'menu_name'          => _x( 'Quizzes', 'admin menu', 'tutor' ),
			'name_admin_bar'     => _x( 'Quiz', 'add new on admin bar', 'tutor' ),
			'add_new'            => _x( 'Add New', "tutor quiz add", 'tutor' ),
			'add_new_item'       => __( 'Add New Quiz', 'tutor' ),
			'new_item'           => __( 'New Quiz', 'tutor' ),
			'edit_item'          => __( 'Edit Quiz', 'tutor' ),
			'view_item'          => __( 'View Quiz', 'tutor' ),
			'all_items'          => __( 'Quizzes', 'tutor' ),
			'search_items'       => __( 'Search Quizzes', 'tutor' ),
			'parent_item_colon'  => __( 'Parent Quizzes:', 'tutor' ),
			'not_found'          => __( 'No quizzes found.', 'tutor' ),
			'not_found_in_trash' => __( 'No quizzes found in Trash.', 'tutor' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'tutor' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => false,
			'show_in_menu'       => 'tutor',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $this->lesson_post_type ),
			'menu_icon'          => 'dashicons-editor-help',
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor'),
			'exclude_from_search' => apply_filters('tutor_quiz_exclude_from_search', true),
			'capabilities' => array(
				'edit_post'          => 'edit_tutor_quiz',
				'read_post'          => 'read_tutor_quiz',
				'delete_post'        => 'delete_tutor_quiz',
				'delete_posts'       => 'delete_tutor_quizzes',
				'edit_posts'         => 'edit_tutor_quizzes',
				'edit_others_posts'  => 'edit_others_tutor_quizzes',
				'publish_posts'      => 'publish_tutor_quizzes',
				'read_private_posts' => 'read_private_tutor_quizzes',
				'create_posts'       => 'edit_tutor_quizzes',
			),
		);

		register_post_type( 'tutor_quiz', $args );
	}

	public function register_topic_post_types(){
		$args = array(
			'label'  => 'Topics',
			'description'        => __( 'Description.', 'tutor' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'query_var'          => false,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
		);
		register_post_type( 'topics', $args );
	}

	public function register_assignments_post_types() {
		$labels = array(
			'name'               => _x( 'Assignments', 'post type general name', 'tutor' ),
			'singular_name'      => _x( 'Assignment', 'post type singular name', 'tutor' ),
			'menu_name'          => _x( 'Assignments', 'admin menu', 'tutor' ),
			'name_admin_bar'     => _x( 'Assignment', 'add new on admin bar', 'tutor' ),
			'add_new'            => _x( 'Add New', "tutor assignment add", 'tutor' ),
			'add_new_item'       => __( 'Add New Assignment', 'tutor' ),
			'new_item'           => __( 'New Assignment', 'tutor' ),
			'edit_item'          => __( 'Edit Assignment', 'tutor' ),
			'view_item'          => __( 'View Assignment', 'tutor' ),
			'all_items'          => __( 'Assignments', 'tutor' ),
			'search_items'       => __( 'Search Assignments', 'tutor' ),
			'parent_item_colon'  => __( 'Parent Assignments:', 'tutor' ),
			'not_found'          => __( 'No Assignments found.', 'tutor' ),
			'not_found_in_trash' => __( 'No Assignments found in Trash.', 'tutor' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'tutor' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => false,
			'show_in_menu'       => 'tutor',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $this->lesson_post_type ),
			'menu_icon'          => 'dashicons-editor-help',
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor'),
			'exclude_from_search' => apply_filters('tutor_assignment_exclude_from_search', true),
			'capabilities' => array(
				'edit_post'          => 'edit_tutor_assignment',
				'read_post'          => 'read_tutor_assignment',
				'delete_post'        => 'delete_tutor_assignment',
				'delete_posts'       => 'delete_tutor_assignments',
				'edit_posts'         => 'edit_tutor_assignments',
				'edit_others_posts'  => 'edit_others_tutor_assignments',
				'publish_posts'      => 'publish_tutor_assignments',
				'read_private_posts' => 'read_private_tutor_assignments',
				'create_posts'       => 'edit_tutor_assignments',
			),
		);

		register_post_type( 'tutor_assignments', $args );
	}

	function course_updated_messages( $messages ) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );
		
		$course_post_type = tutor()->course_post_type;

		$messages[$course_post_type] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Course updated.', 'tutor' ),
			2  => __( 'Custom field updated.', 'tutor' ),
			3  => __( 'Custom field deleted.', 'tutor' ),
			4  => __( 'Course updated.', 'tutor' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Course restored to revision from %s', 'tutor' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Course published.', 'tutor' ),
			7  => __( 'Course saved.', 'tutor' ),
			8  => __( 'Course submitted.', 'tutor' ),
			9  => sprintf(
				__( 'Course scheduled for: <strong>%1$s</strong>.', 'tutor' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'tutor' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Course draft updated.', 'tutor' )
		);

		if ( $post_type_object->publicly_queryable && $course_post_type === $post_type ) {
			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View course', 'tutor' ) );
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview course', 'tutor' ) );
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;
		}

		return $messages;
	}

	/**
	 * @param $can_edit
	 * @param $post_type
	 *
	 * @return bool
	 *
	 * Enable / Disable Gutenberg Editor
	 * @since v.1.3.4
	 */
	public function gutenberg_can_edit_post_type( $can_edit, $post_type ) {
		$enable_gutenberg = (bool) tutor_utils()->get_option('enable_gutenberg_course_edit');
		return $this->course_post_type === $post_type ? $enable_gutenberg : $can_edit;
	}

	/**
	 * Register tutor_enrolled post type
	 * @since v.1.4.0
	 */
	public function register_tutor_enrolled_post_types(){
		$args = array(
			'label'  => 'Tutor Enrolled',
			'description'        => __( 'Description.', 'tutor' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'query_var'          => false,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
		);
		register_post_type( 'tutor_enrolled', $args );
	}

}
