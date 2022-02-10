<?php

/**
 * Quize class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.0.0
 */

namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Quiz {

	private $allowed_attributes = array( 
		'src' 	   => array(), 
		'style'    => array(), 
		'class'    => array(), 
		'id' 	   => array(), 
		'href' 	   => array(), 
		'alt' 	   => array(), 
		'title'    => array(),
		'type'     => array(),
		'controls' => array(),
		'muted'    => array(),
		'loop'	   => array(),
		'poster'   => array(),
		'preload'  => array(),
		'autoplay' => array(),
		'width'    => array(),
		'height'   => array()
	);
		
	private $allowed_html = array( 'img', 'b', 'i', 'br', 'a', 'audio', 'video', 'source' );

	public function __construct() {
		
		add_action('save_post_tutor_quiz', array($this, 'save_quiz_meta'));

		add_action('wp_ajax_tutor_load_quiz_builder_modal', array($this, 'tutor_load_quiz_builder_modal'));
		add_action('wp_ajax_remove_quiz_from_post', array($this, 'remove_quiz_from_post'));

		add_action('wp_ajax_tutor_quiz_timeout', array($this, 'tutor_quiz_timeout'));

		//User take the quiz
		add_action('template_redirect', array($this, 'start_the_quiz'));
		add_action('template_redirect', array($this, 'answering_quiz'));
		add_action('template_redirect', array($this, 'finishing_quiz_attempt'));

		add_action('admin_action_review_quiz_answer', array($this, 'review_quiz_answer'));
		add_action('wp_ajax_review_quiz_answer', array($this, 'review_quiz_answer'));
		add_action('wp_ajax_tutor_instructor_feedback', array($this, 'tutor_instructor_feedback')); // Instructor Feedback Action

		/**
		 * New Design Quiz
		 */

		add_action('wp_ajax_tutor_create_quiz_and_load_modal', array($this, 'tutor_create_quiz_and_load_modal'));
		add_action('wp_ajax_tutor_delete_quiz_by_id', array($this, 'tutor_delete_quiz_by_id'));
		add_action('wp_ajax_tutor_quiz_builder_quiz_update', array($this, 'tutor_quiz_builder_quiz_update'));
		add_action('wp_ajax_tutor_load_edit_quiz_modal', array($this, 'tutor_load_edit_quiz_modal'));
		add_action('wp_ajax_tutor_quiz_builder_get_question_form', array($this, 'tutor_quiz_builder_get_question_form'));
		add_action('wp_ajax_tutor_quiz_modal_update_question', array($this, 'tutor_quiz_modal_update_question'));
		add_action('wp_ajax_tutor_quiz_builder_question_delete', array($this, 'tutor_quiz_builder_question_delete'));
		add_action('wp_ajax_tutor_quiz_add_question_answers', array($this, 'tutor_quiz_add_question_answers'));
		add_action('wp_ajax_tutor_quiz_edit_question_answer', array($this, 'tutor_quiz_edit_question_answer'));
		add_action('wp_ajax_tutor_save_quiz_answer_options', array($this, 'tutor_save_quiz_answer_options'));
		add_action('wp_ajax_tutor_update_quiz_answer_options', array($this, 'tutor_update_quiz_answer_options'));
		add_action('wp_ajax_tutor_quiz_builder_get_answers_by_question', array($this, 'tutor_quiz_builder_get_answers_by_question'));
		add_action('wp_ajax_tutor_quiz_builder_delete_answer', array($this, 'tutor_quiz_builder_delete_answer'));
		add_action('wp_ajax_tutor_quiz_question_sorting', array($this, 'tutor_quiz_question_sorting'));
		add_action('wp_ajax_tutor_quiz_answer_sorting', array($this, 'tutor_quiz_answer_sorting'));
		add_action('wp_ajax_tutor_mark_answer_as_correct', array($this, 'tutor_mark_answer_as_correct'));
		add_action('wp_ajax_tutor_quiz_modal_update_settings', array($this, 'tutor_quiz_modal_update_settings'));

		/**
         * Frontend Stuff
         */
		add_action('wp_ajax_tutor_render_quiz_content', array($this, 'tutor_render_quiz_content'));

		/**
		 * Quiz abandon action
		 * 
		 * @since 1.9.6
		 */
		add_action('wp_ajax_tutor_quiz_abandon', array($this, 'tutor_quiz_abandon'));

		$this->prepare_allowed_html();
	}

	private function prepare_allowed_html() {
		
		$allowed = array();

		foreach($this->allowed_html as $tag) {
			$allowed[$tag] = $this->allowed_attributes;
		}

		$this->allowed_html = $allowed;
	}

	public function tutor_instructor_feedback(){
		tutils()->checking_nonce();

		$feedback = sanitize_text_field($_POST['feedback']);
		$attempt_id = (int) tutor_utils()->avalue_dot('attempts_id', $_POST);

		if ($attempt_id && tutils()->can_user_manage('attempt', $attempt_id)) {
			update_post_meta($attempt_id, 'instructor_feedback', $feedback);
			do_action('tutor_quiz/attempt/submitted/feedback', $attempt_id);

			wp_send_json_success( );
		}
	}

	public function save_quiz_meta($post_ID){
		if (isset($_POST['quiz_option'])){
			$quiz_option = tutor_utils()->sanitize_array($_POST['quiz_option']);
			update_post_meta($post_ID, 'tutor_quiz_option', $quiz_option);
		}
	}

	/**
	 * Tutor Quiz Builder Modal
	 */
	public function tutor_load_quiz_builder_modal(){
		tutils()->checking_nonce();

		ob_start();
		include  tutor()->path.'views/modal/add_quiz.php';
		$output = ob_get_clean();

		wp_send_json_success(array('output' => $output));
	}

	public function remove_quiz_from_post(){
		tutils()->checking_nonce();

		global $wpdb;
		$quiz_id = (int) tutor_utils()->avalue_dot('quiz_id', $_POST);

		if(!tutils()->can_user_manage('quiz', $quiz_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		$wpdb->update($wpdb->posts, array('post_parent' => 0), array('ID' => $quiz_id) );
		wp_send_json_success();
	}

	/**
	 *
     * Start Quiz from here...
     *
     * @since v.1.0.0
	 */

	public function start_the_quiz(){
		if ( ! isset($_POST['tutor_action'])  ||  $_POST['tutor_action'] !== 'tutor_start_quiz' ){
			return;
		}
		//Checking nonce
		tutor_utils()->checking_nonce();

		if ( ! is_user_logged_in()){
			//TODO: need to set a view in the next version
			die('Please sign in to do this operation');
		}

		global $wpdb;

		$user_id = get_current_user_id();
		$user = get_userdata($user_id);

		$quiz_id = (int) sanitize_text_field($_POST['quiz_id']);

		$quiz = get_post($quiz_id);
		$course = tutor_utils()->get_course_by_quiz($quiz_id);
		if ( empty($course->ID)){
		    die('There is something went wrong with course, please check if quiz attached with a course');
        }

        do_action('tutor_quiz/start/before', $quiz_id, $user_id);

		$date = date("Y-m-d H:i:s", tutor_time());

		$tutor_quiz_option = (array) maybe_unserialize(get_post_meta($quiz_id, 'tutor_quiz_option', true));
		$attempts_allowed = tutor_utils()->get_quiz_option($quiz_id, 'attempts_allowed', 0);

		$time_limit = tutor_utils()->get_quiz_option($quiz_id, 'time_limit.time_value');
		$time_limit_seconds = 0;
		$time_type = 'seconds';
		if ($time_limit){
			$time_type = tutor_utils()->get_quiz_option($quiz_id, 'time_limit.time_type');

			switch ($time_type){
				case 'seconds':
					$time_limit_seconds = $time_limit;
					break;
				case 'minutes':
					$time_limit_seconds = $time_limit * 60;
					break;
				case 'hours':
					$time_limit_seconds = $time_limit * 60 * 60;
					break;
				case 'days':
					$time_limit_seconds = $time_limit * 60 * 60 * 24;
					break;
				case 'weeks':
					$time_limit_seconds = $time_limit * 60 * 60 * 24 * 7;
					break;
			}
		}

		$max_question_allowed = tutor_utils()->max_questions_for_take_quiz($quiz_id);
		$tutor_quiz_option['time_limit']['time_limit_seconds'] = $time_limit_seconds;

		$attempt_data = array(
		        'course_id'                 => $course->ID,
		        'quiz_id'                   => $quiz_id,
		        'user_id'                   => $user_id,
		        'total_questions'           => $max_question_allowed,
		        'total_answered_questions'  => 0,
		        'attempt_info'              => maybe_serialize($tutor_quiz_option),
		        'attempt_status'            => 'attempt_started',
		        'attempt_ip'                => tutor_utils()->get_ip(),
		        'attempt_started_at'        => $date,
        );

		$wpdb->insert($wpdb->prefix.'tutor_quiz_attempts', $attempt_data);
		$attempt_id = (int) $wpdb->insert_id;

		do_action('tutor_quiz/start/after', $quiz_id, $user_id, $attempt_id);

		wp_redirect(get_permalink($quiz_id));
		die();
	}

	public function answering_quiz(){

		if ( tutils()->array_get('tutor_action', $_POST) !== 'tutor_answering_quiz_question' ){
			return;
		}
		//submit quiz attempts
		self::tutor_quiz_attemp_submit();

		wp_redirect(get_the_permalink());
		die();
	}

	/**
	 * Quiz abandon submission handler
	 * 
	 * @return JSON response
	 * 
	 * @since 1.9.6
	 */
	public function tutor_quiz_abandon(){
		if ( tutils()->array_get('tutor_action', $_POST) !== 'tutor_answering_quiz_question' ){
			return;
		}
		//submit quiz attempts
		if ( self::tutor_quiz_attemp_submit() ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * This is  a unified method for handling normal quiz submit or abandon submit
	 * 
	 * It will handle ajax or normal form submit and can be used with different hooks
	 * 
	 * @return true | false
	 * 
	 * @since 1.9.6 
	 */
	public static function tutor_quiz_attemp_submit() {
		tutor_utils()->checking_nonce();

		$attempt_id = (int) sanitize_text_field(tutor_utils()->avalue_dot('attempt_id', $_POST));
		$attempt = tutor_utils()->get_attempt($attempt_id);
		$course_id = tutor_utils()->get_course_by_quiz($attempt->quiz_id)->ID;

		$attempt_answers = isset($_POST['attempt']) ? $_POST['attempt'] : false;
		if ( ! is_user_logged_in()){
			die('Please sign in to do this operation');
		}

		global $wpdb;
		$user_id = get_current_user_id();

		do_action('tutor_quiz/attempt_analysing/before', $attempt_id);

		if ($attempt_answers && is_array($attempt_answers) && count($attempt_answers)){
		    foreach ($attempt_answers as $attempt_id => $attempt_answer){

			    /**
			     * Get total marks of all question comes
			     */
			    $question_ids = tutor_utils()->avalue_dot('quiz_question_ids', $attempt_answer);
			    if (is_array($question_ids) && count($question_ids)){
			        $question_ids_string = "'".implode("','", $question_ids)."'";
			        $total_question_marks = $wpdb->get_var("SELECT SUM(question_mark) FROM {$wpdb->prefix}tutor_quiz_questions WHERE question_id IN({$question_ids_string}) ;");
			        $wpdb->update($wpdb->prefix.'tutor_quiz_attempts', array('total_marks' =>$total_question_marks ), array('attempt_id' => $attempt_id ));
                }

			    if ( ! $attempt || $user_id != $attempt->user_id){
				    die('Operation not allowed, attempt not found or permission denied');
			    }

			    $quiz_answers = tutor_utils()->avalue_dot('quiz_question', $attempt_answer);

			    $total_marks = 0;
                $review_required = false;

                if ( tutils()->count($quiz_answers)) {

				    foreach ( $quiz_answers as $question_id => $answers ) {
					    $question      = tutor_utils()->get_quiz_question_by_id( $question_id );
					    $question_type = $question->question_type;

					    $is_answer_was_correct = false;
					    $given_answer          = '';

					    if ( $question_type === 'true_false' || $question_type === 'single_choice' ) {

							if(!is_numeric($answers) || !$answers) {
								wp_send_json_error();
								exit;
							}

						    $given_answer          = $answers;
						    $is_answer_was_correct = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT is_correct FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE answer_id = %d ", $answers ) );

					    } elseif ( $question_type === 'multiple_choice' ) {

							$given_answer = (array) ( $answers );
							
							$given_answer = array_filter( $given_answer, function($id) {
								return is_numeric($id) && $id>0;
							} );

							$get_original_answers = (array) $wpdb->get_col($wpdb->prepare(
								"SELECT 
									answer_id 
								FROM 
									{$wpdb->prefix}tutor_quiz_question_answers 
								WHERE 
									belongs_question_id = %d 
									AND belongs_question_type = %s 
									AND is_correct = 1 ;
								", 
								$question->question_id, 
								$question_type
							) );
							
							
							if (count(array_diff($get_original_answers, $given_answer)) === 0 && count($get_original_answers) === count($given_answer)) {
							    $is_answer_was_correct = true;
							}
							$given_answer = maybe_serialize( $answers );

					    } elseif ( $question_type === 'fill_in_the_blank' ) {

						    $given_answer = (array) array_map( 'sanitize_text_field', $answers );
						    $given_answer = maybe_serialize( $given_answer );

						    $get_original_answer = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id = %d AND belongs_question_type = %s ;", $question->question_id, $question_type ) );
						    $gap_answer          = (array) explode( '|', $get_original_answer->answer_two_gap_match );

						    $gap_answer = array_map( 'sanitize_text_field', $gap_answer );
                            if ( strtolower($given_answer) == strtolower(maybe_serialize( $gap_answer )) ) {
                                $is_answer_was_correct = true;
                            }
					    } elseif ( $question_type === 'open_ended' || $question_type === 'short_answer' ) {
					        $review_required = true;
						    $given_answer = wp_kses_post( $answers );

					    } elseif ( $question_type === 'ordering' || $question_type === 'matching' || $question_type === 'image_matching' ) {

						    $given_answer = (array) array_map( 'sanitize_text_field', tutor_utils()->avalue_dot( 'answers', $answers ) );
						    $given_answer = maybe_serialize( $given_answer );

						    $get_original_answers = (array) $wpdb->get_col($wpdb->prepare(
								"SELECT answer_id 
								FROM {$wpdb->prefix}tutor_quiz_question_answers 
								WHERE belongs_question_id = %d AND belongs_question_type = %s ORDER BY answer_order ASC ;", $question->question_id, $question_type));
							
							$get_original_answers = array_map( 'sanitize_text_field', $get_original_answers );

						    if ( $given_answer == maybe_serialize( $get_original_answers ) ) {
							    $is_answer_was_correct = true;
						    }

					    } elseif ( $question_type === 'image_answering' ) {
						    $image_inputs          = tutor_utils()->avalue_dot( 'answer_id', $answers );
						    $image_inputs          = (array) array_map( 'sanitize_text_field', $image_inputs );
						    $given_answer          = maybe_serialize( $image_inputs );
						    $is_answer_was_correct = false;

						    $db_answer = $wpdb->get_col($wpdb->prepare(
								"SELECT answer_title 
								FROM {$wpdb->prefix}tutor_quiz_question_answers 
								WHERE belongs_question_id = %d AND belongs_question_type = 'image_answering' ORDER BY answer_order asc ;", $question_id));

						    if ( is_array( $db_answer ) && count( $db_answer ) ) {
							    $is_answer_was_correct = ( strtolower( maybe_serialize( array_values( $image_inputs ) ) ) == strtolower( maybe_serialize( $db_answer ) ) );
						    }
					    }

					    $question_mark = $is_answer_was_correct ? $question->question_mark : 0;
					    $total_marks   += $question_mark;

					    $answers_data = array(
						    'user_id'         => $user_id,
						    'quiz_id'         => $attempt->quiz_id,
						    'question_id'     => $question_id,
						    'quiz_attempt_id' => $attempt_id,
						    'given_answer'    => $given_answer,
						    'question_mark'   => $question->question_mark,
						    'achieved_mark'   => $question_mark,
						    'minus_mark'      => 0,
						    'is_correct'      => $is_answer_was_correct ? 1 : 0,
					    );
					
					 	/*
						check if question_type open ended or short ans the set is_correct default value null before saving 
					 	*/
						if($question_type==="open_ended" || $question_type ==="short_answer")
						{
							$answers_data['is_correct'] = NULL;
						}
						
					    $wpdb->insert( $wpdb->prefix . 'tutor_quiz_attempt_answers', $answers_data );
				    }
			    }

			    $attempt_info = array(
			            'total_answered_questions'  => tutils()->count($quiz_answers),
			            'earned_marks'              => $total_marks,
			            'attempt_status'            => 'attempt_ended',
			            'attempt_ended_at'          => date("Y-m-d H:i:s", tutor_time()),
                );

                if ($review_required){
                    $attempt_info['attempt_status'] = 'review_required';
                }

			    $wpdb->update($wpdb->prefix.'tutor_quiz_attempts', $attempt_info, array('attempt_id' => $attempt_id));
            }

            do_action('tutor_quiz/attempt_ended', $attempt_id, $course_id, $user_id);
			return true;
        }
		return false;
	}


	/**
	 * Quiz attempt will be finish here
	 *
	 */

	public function finishing_quiz_attempt(){

		if ( ! isset($_POST['tutor_action'])  ||  $_POST['tutor_action'] !== 'tutor_finish_quiz_attempt' ){
			return;
		}
		//Checking nonce
		tutor_utils()->checking_nonce();

		if ( ! is_user_logged_in()){
			die('Please sign in to do this operation');
		}

		global $wpdb;

		$quiz_id = (int) sanitize_text_field($_POST['quiz_id']);
		$attempt = tutor_utils()->is_started_quiz($quiz_id);
		$attempt_id = $attempt->attempt_id;

		$attempt_info = array(
			'total_answered_questions'  => 0,
			'earned_marks'              => 0,
			'attempt_status'            => 'attempt_ended',
			'attempt_ended_at'          => date("Y-m-d H:i:s", tutor_time()),
		);

		do_action('tutor_quiz_before_finish', $attempt_id, $quiz_id, $attempt->user_id);
		$wpdb->update($wpdb->prefix.'tutor_quiz_attempts', $attempt_info, array('attempt_id' => $attempt_id));
		do_action('tutor_quiz_finished', $attempt_id, $quiz_id, $attempt->user_id);

		wp_redirect(tutor_utils()->input_old('_wp_http_referer'));
	}

	/**
	 * Quiz timeout by ajax
	 */
	public function tutor_quiz_timeout(){
		tutils()->checking_nonce();

		global $wpdb;

		$quiz_id = (int) sanitize_text_field($_POST['quiz_id']);

		// if(!tutils()->can_user_manage('quiz', $quiz_id)) {
		// 	wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		// }

		$attempt = tutor_utils()->is_started_quiz($quiz_id);

		if ($attempt) {
			$attempt_id = $attempt->attempt_id;

			$data = array(
			    'attempt_status' => 'attempt_timeout',
			    'attempt_ended_at'          => date("Y-m-d H:i:s", tutor_time()),
		    );
		    $wpdb->update($wpdb->prefix.'tutor_quiz_attempts', $data, array('attempt_id' => $attempt->attempt_id));

			do_action('tutor_quiz_timeout', $attempt_id, $quiz_id, $attempt->user_id);

			wp_send_json_success();
		}

		wp_send_json_error(__('Quiz has been timeout already', 'tutor'));
	}

	/**
	 * Review the answer and change individual answer result
	 */

	public function review_quiz_answer() {
		
		tutils()->checking_nonce(strtolower($_SERVER['REQUEST_METHOD']));

		global $wpdb;

		$attempt_id = (int) sanitize_text_field($_GET['attempt_id']);
		$attempt_answer_id = (int) sanitize_text_field($_GET['attempt_answer_id']);
		$mark_as = sanitize_text_field($_GET['mark_as']);

		if(!tutils()->can_user_manage('attempt', $attempt_id) || !tutils()->can_user_manage('attempt_answer', $attempt_answer_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		$attempt_answer = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}tutor_quiz_attempt_answers 
			WHERE attempt_answer_id = %d ", 
			$attempt_answer_id
		));

		$attempt = tutor_utils()->get_attempt($attempt_id);
		$question = tutils()->get_quiz_question_by_id($attempt_answer->question_id);
		$course_id = $attempt->course_id;
		$student_id = $attempt->user_id;
		$previous_ans =  $attempt_answer->is_correct;

		do_action('tutor_quiz_review_answer_before', $attempt_answer_id, $attempt_id, $mark_as);

		if ($mark_as === 'correct'){

			$answer_update_data = array(
				'achieved_mark' => $attempt_answer->question_mark,
				'is_correct' => 1,
			);
			$wpdb->update($wpdb->prefix.'tutor_quiz_attempt_answers', $answer_update_data, array('attempt_answer_id' => $attempt_answer_id ));
			if($previous_ans ==0 OR $previous_ans ==null)
			{
				
				//if previous answer was wrong or in review then add point as correct
				$attempt_update_data = array(
					'earned_marks' => $attempt->earned_marks + $attempt_answer->question_mark,
	                'is_manually_reviewed' => 1,
					'manually_reviewed_at' => date("Y-m-d H:i:s", tutor_time()),
				);

				
			}
			
			if ($question->question_type === 'open_ended' || $question->question_type === 'short_answer' ){
                $attempt_update_data['attempt_status'] = 'attempt_ended';
            }				
			$wpdb->update($wpdb->prefix.'tutor_quiz_attempts', $attempt_update_data, array('attempt_id' => $attempt_id ));
		}
		elseif($mark_as === 'incorrect')
		{

			$answer_update_data = array(
				'achieved_mark' => '0.00',
				'is_correct' => 0,
			);
			$wpdb->update($wpdb->prefix.'tutor_quiz_attempt_answers', $answer_update_data, array('attempt_answer_id' => $attempt_answer_id ));


			if($previous_ans ==1)
			{
			
				//if previous ans was right then mynus
				$attempt_update_data = array(
					'earned_marks'          => $attempt->earned_marks - $attempt_answer->question_mark,
					'is_manually_reviewed'  => 1,
					'manually_reviewed_at'  => date("Y-m-d H:i:s", tutor_time()),
				);

			}
            if ($question->question_type === 'open_ended' || $question->question_type === 'short_answer' ){
                $attempt_update_data['attempt_status'] = 'attempt_ended';
            }	
				
			$wpdb->update($wpdb->prefix.'tutor_quiz_attempts', $attempt_update_data, array('attempt_id' => $attempt_id ));			
		}
		do_action('tutor_quiz_review_answer_after', $attempt_answer_id, $attempt_id, $mark_as);
		do_action('tutor_quiz/answer/review/after', $attempt_answer_id, $course_id, $student_id);

		if (wp_doing_ajax())
		{
		    wp_send_json_success();
        }
        else{
			wp_redirect(admin_url("admin.php?page=tutor_quiz_attempts&sub_page=view_attempt&attempt_id=".$attempt_id));
		}

		die();
	}


	/**
	 * New Design Quiz
	 */
	public function tutor_create_quiz_and_load_modal(){
		tutils()->checking_nonce();

		$topic_id           = sanitize_text_field($_POST['topic_id']);
		$quiz_title         = sanitize_text_field($_POST['quiz_title']);
		$quiz_description   = wp_kses( $_POST['quiz_description'], $this->allowed_html );
		$next_order_id      = tutor_utils()->get_next_course_content_order_id($topic_id);

		if(!tutils()->can_user_manage('topic', $topic_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor'), 'data'=>$_POST) );
		}

		$post_arr = array(
			'post_type'     => 'tutor_quiz',
			'post_title'    => $quiz_title,
			'post_content'  => $quiz_description,
			'post_status'   => 'publish',
			'post_author'   => get_current_user_id(),
			'post_parent'   => $topic_id,
			'menu_order'    => $next_order_id,
		);
		$quiz_id = wp_insert_post( $post_arr );
		do_action('tutor_initial_quiz_created', $quiz_id);

		ob_start();
		include  tutor()->path.'views/modal/edit_quiz.php';
		$output = ob_get_clean();

		ob_start();
		?>
        <div id="tutor-quiz-<?php echo $quiz_id; ?>" class="course-content-item tutor-quiz tutor-quiz-<?php echo $quiz_id; ?>">
            <div class="tutor-lesson-top">
                <i class="tutor-icon-move"></i>
                <a href="javascript:;" class="open-tutor-quiz-modal" data-quiz-id="<?php echo $quiz_id; ?>" data-topic-id="<?php echo $topic_id;
				?>"> <i class=" tutor-icon-doubt"></i>[<?php _e('QUIZ', 'tutor'); ?>] <?php echo stripslashes($quiz_title); ?> </a>
				<?php do_action('tutor_course_builder_before_quiz_btn_action', $quiz_id); ?>
                <a href="javascript:;" class="tutor-delete-quiz-btn" data-quiz-id="<?php echo $quiz_id; ?>"><i class="tutor-icon-garbage"></i></a>
            </div>
        </div>
		<?php
		$output_quiz_row = ob_get_clean();

		wp_send_json_success(array('output' => $output, 'output_quiz_row' => $output_quiz_row));
	}

	public function tutor_delete_quiz_by_id(){
		tutils()->checking_nonce();

	    global $wpdb;

	    $quiz_id = (int) sanitize_text_field($_POST['quiz_id']);
	    $post = get_post($quiz_id);

		
		if(!tutils()->can_user_manage('quiz', $quiz_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

	    if ( $post->post_type === 'tutor_quiz'){
	        do_action('tutor_delete_quiz_before', $quiz_id);

	        $wpdb->delete($wpdb->prefix.'tutor_quiz_attempts', array('quiz_id' => $quiz_id));
	        $wpdb->delete($wpdb->prefix.'tutor_quiz_attempt_answers', array('quiz_id' => $quiz_id));

            $questions_ids = $wpdb->get_col($wpdb->prepare("SELECT question_id FROM {$wpdb->prefix}tutor_quiz_questions WHERE quiz_id = %d ", $quiz_id));
			
			if (is_array($questions_ids) && count($questions_ids)){
                $in_question_ids = "'".implode("','", $questions_ids)."'";
                $wpdb->query("DELETE FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id IN({$in_question_ids}) ");
			}
			
		    $wpdb->delete($wpdb->prefix.'tutor_quiz_questions', array('quiz_id' => $quiz_id));

		    wp_delete_post($quiz_id, true);
		    delete_post_meta($quiz_id, '_tutor_course_id_for_lesson');

		    do_action('tutor_delete_quiz_after', $quiz_id);


		    wp_send_json_success();
        }

        wp_send_json_error();
    }

	/**
	 * Update Quiz from quiz builder modal
	 *
	 * @since v.1.0.0
	 */
	public function tutor_quiz_builder_quiz_update(){
		tutils()->checking_nonce();

		$quiz_id         	= sanitize_text_field($_POST['quiz_id']);
		$topic_id         	= sanitize_text_field($_POST['topic_id']);
		$quiz_title         = sanitize_text_field($_POST['quiz_title']);
		$quiz_description   = wp_kses( $_POST['quiz_description'], $this->allowed_html );

		if(!tutils()->can_user_manage('quiz', $quiz_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		$post_arr = array(
			'ID'    => $quiz_id,
			'post_title'    => $quiz_title,
			'post_content'  => $quiz_description,

		);
		$quiz_id = wp_update_post( $post_arr );

		do_action('tutor_quiz_updated', $quiz_id);

		ob_start();
		?>
        <div class="tutor-lesson-top">
            <i class="tutor-icon-move"></i>
            <a href="javascript:;" class="open-tutor-quiz-modal" data-quiz-id="<?php echo $quiz_id; ?>" data-topic-id="<?php echo $topic_id;
			?>"> <i class=" tutor-icon-doubt"></i>[<?php _e('QUIZ', 'tutor'); ?>] <?php echo stripslashes($quiz_title); ?> </a>
			<?php do_action('tutor_course_builder_before_quiz_btn_action', $quiz_id); ?>
            <a href="javascript:;" class="tutor-delete-quiz-btn" data-quiz-id="<?php echo $quiz_id; ?>"><i class="tutor-icon-garbage"></i></a>
        </div>
		<?php
		$output_quiz_row = ob_get_clean();

		wp_send_json_success(array('output_quiz_row' => $output_quiz_row));
	}

	/**
	 * Load quiz Modal for edit quiz
	 *
	 * @since v.1.0.0
	 */
	public function tutor_load_edit_quiz_modal(){
		tutils()->checking_nonce();

		$quiz_id 	= sanitize_text_field ($_POST['quiz_id'] );
		$topic_id 	= sanitize_text_field( $_POST['topic_id'] ); 
		
		if(!tutils()->can_user_manage('quiz', $quiz_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}
		
		ob_start();
		include  tutor()->path.'views/modal/edit_quiz.php';
		$output = ob_get_clean();

		wp_send_json_success(array('output' => $output));
	}

	/**
	 * Load quiz question form for quiz
	 *
	 * @since v.1.0.0
	 */
	public function tutor_quiz_builder_get_question_form(){
		tutils()->checking_nonce();

		global $wpdb;
		$quiz_id = sanitize_text_field($_POST['quiz_id']);
		$question_id = sanitize_text_field(tutor_utils()->avalue_dot('question_id', $_POST));

		if(!tutils()->can_user_manage('quiz', $quiz_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}
		
		if ( ! $question_id){
			$next_question_id = tutor_utils()->quiz_next_question_id();
			$next_question_order = tutor_utils()->quiz_next_question_order_id($quiz_id);

			$new_question_data = array(
				'quiz_id'               => $quiz_id,
				'question_title'        => __('Question', 'tutor').' '.$next_question_id,
				'question_description'  => '',
				'question_type'         => 'true_false',
				'question_mark'         => 1,
				'question_settings'     => maybe_serialize(array()),
				'question_order'        => esc_sql( $next_question_order ) ,
			);

			$wpdb->insert($wpdb->prefix.'tutor_quiz_questions', $new_question_data);
			$question_id = $wpdb->insert_id;
		}

		$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tutor_quiz_questions where question_id = %d ", $question_id));

		ob_start();
		include  tutor()->path.'views/modal/question_form.php';
		$output = ob_get_clean();

		wp_send_json_success(array('output' => $output));
	}

	public function tutor_quiz_modal_update_question(){
		tutils()->checking_nonce();

		global $wpdb;

		$question_data = $_POST['tutor_quiz_question'];

		foreach ($question_data as $question_id => $question) {

			if(!tutils()->can_user_manage('question', $question_id)) {
				continue;
			}

			$question_title         = sanitize_text_field($question['question_title']);
			$question_description   = wp_kses( $question['question_description'], $this->allowed_html ); // sanitize_text_field($question['question_description']);
			$question_type          = sanitize_text_field($question['question_type']);
			$question_mark          = sanitize_text_field($question['question_mark']);

			unset($question['question_title']);
			unset($question['question_description']);

			$data = array(
				'question_title'        => $question_title,
				'question_description'  => $question_description,
				'question_type'         => $question_type,
				'question_mark'         => $question_mark,
				'question_settings'     => maybe_serialize($question),
			);

			$wpdb->update($wpdb->prefix.'tutor_quiz_questions', $data, array('question_id' => $question_id) );


			/**
			 * Validation
			 */
			if ($question_type === 'true_false' || $question_type === 'single_choice'){
			    $question_options = tutils()->get_answers_by_quiz_question($question_id);
			    if (tutils()->count($question_options)){
			        $required_validate = true;
			        foreach ($question_options as $question_option){
			            if ($question_option->is_correct){
				            $required_validate = false;
                        }
                    }
                    if ($required_validate){
	                    $validation_msg = "<p class='tutor-error-msg'>".__('Please select the correct answer', 'tutor')."</p>";
	                    wp_send_json_error(array('validation_msg' => $validation_msg ));
                    }
                }else{
			        $validation_msg = "<p class='tutor-error-msg'>".__('Please make sure you have added more than one option and saved them', 'tutor')."</p>";
				    wp_send_json_error(array('validation_msg' => $validation_msg ));
			    }
            }
		}

		wp_send_json_success();
	}

	public function tutor_quiz_builder_question_delete(){
		tutils()->checking_nonce();

		global $wpdb;

		$question_id = sanitize_text_field(tutor_utils()->avalue_dot('question_id', $_POST));
		
		if(!tutils()->can_user_manage('question', $question_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		if ($question_id){
			$wpdb->delete($wpdb->prefix.'tutor_quiz_questions', array('question_id' => esc_sql( $question_id ) ));
		}

		wp_send_json_success();
	}

	/**
	 * Get answers options form for quiz question
	 *
	 * @since v.1.0.0
	 */
	public function tutor_quiz_add_question_answers(){
		tutils()->checking_nonce();

		$question_id = sanitize_text_field($_POST['question_id']);
		$question = tutor_utils()->avalue_dot($question_id, $_POST['tutor_quiz_question']);
		$question_type = $question['question_type'];

		if(!tutils()->can_user_manage('question', $question_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		ob_start();
		include  tutor()->path.'views/modal/question_answer_form.php';
		$output = ob_get_clean();

		wp_send_json_success(array('output' => $output));
	}

	/**
	 * Edit Answer Form
     *
     * @since v.1.0.0
	 */
	public function tutor_quiz_edit_question_answer(){
		tutils()->checking_nonce();

		$answer_id = (int) sanitize_text_field($_POST['answer_id']);

		if(!tutils()->can_user_manage('quiz_answer', $answer_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}
		
		$old_answer = tutor_utils()->get_answer_by_id($answer_id);
		foreach ($old_answer as $old_answer);
		$question_id = $old_answer->belongs_question_id;
		$question_type = $old_answer->belongs_question_type;

		ob_start();
		include  tutor()->path.'views/modal/question_answer_edit_form.php';
		$output = ob_get_clean();

		wp_send_json_success(array('output' => $output));
    }

	public function tutor_save_quiz_answer_options(){
		tutils()->checking_nonce();

		global $wpdb;

		$questions = $_POST['tutor_quiz_question'];
		$answers = $_POST['quiz_answer'];

		foreach ($answers as $question_id => $answer){

			if(!tutils()->can_user_manage('question', $question_id)) {
				continue;
			}

			$question = tutor_utils()->avalue_dot($question_id, $questions);
			$question_type = $question['question_type'];

			//Getting next sorting order
			$next_order_id = (int) $wpdb->get_var($wpdb->prepare(
				"SELECT MAX(answer_order) 
				FROM {$wpdb->prefix}tutor_quiz_question_answers 
				where belongs_question_id = %d 
				AND belongs_question_type = %s ", $question_id, esc_sql( $question_type )));

			$next_order_id = $next_order_id + 1;

			if ($question){
				if ($question_type === 'true_false'){
					$wpdb->delete($wpdb->prefix.'tutor_quiz_question_answers', array('belongs_question_id' => $question_id, 'belongs_question_type' => $question_type));
					$data_true_false = array(
						array(
							'belongs_question_id'   => esc_sql( $question_id ) ,
							'belongs_question_type' => $question_type,
							'answer_title'          => __('True', 'tutor'),
							'is_correct'            => $answer['true_false'] == 'true' ? 1 : 0,
							'answer_two_gap_match'  => 'true',
						),
						array(
							'belongs_question_id'   => esc_sql( $question_id ) ,
							'belongs_question_type' => $question_type,
							'answer_title'          => __('False', 'tutor'),
							'is_correct'            => $answer['true_false'] == 'false' ? 1 : 0,
							'answer_two_gap_match'  => 'false',
						),
					);

					foreach ($data_true_false as $true_false_data){
						$wpdb->insert($wpdb->prefix.'tutor_quiz_question_answers', $true_false_data);
					}

				}elseif($question_type === 'multiple_choice' || $question_type === 'single_choice' || $question_type === 'ordering' ||
                        $question_type === 'matching' || $question_type === 'image_matching' || $question_type === 'image_answering'  ){

					$answer_data = array(
						'belongs_question_id'   => sanitize_text_field( $question_id ),
						'belongs_question_type' => $question_type,
						'answer_title'          => sanitize_text_field( $answer['answer_title'] ),
						'image_id'              => isset($answer['image_id']) ? $answer['image_id'] : 0,
						'answer_view_format'    => isset($answer['answer_view_format']) ? $answer['answer_view_format'] : 0,
						'answer_order'          => $next_order_id,
					);
					if (isset($answer['matched_answer_title'])){
						$answer_data['answer_two_gap_match'] = sanitize_text_field( $answer['matched_answer_title'] );
                    }

					$wpdb->insert($wpdb->prefix.'tutor_quiz_question_answers', $answer_data);

				}elseif($question_type === 'fill_in_the_blank'){
					$wpdb->delete($wpdb->prefix.'tutor_quiz_question_answers', array('belongs_question_id' => $question_id, 'belongs_question_type' => $question_type));
					$answer_data = array(
						'belongs_question_id'   => sanitize_text_field( $question_id ) ,
						'belongs_question_type' => $question_type,
						'answer_title'          => sanitize_text_field( $answer['answer_title'] ),
						'answer_two_gap_match'  => isset($answer['answer_two_gap_match']) ? sanitize_text_field( trim($answer['answer_two_gap_match']) ) : null,
					);
					$wpdb->insert($wpdb->prefix.'tutor_quiz_question_answers', $answer_data);
				}
			}
		}

		wp_send_json_success();
	}

	/**
	 * Tutor Update Answer
     *
     * @since v.1.0.0
	 */
	public function tutor_update_quiz_answer_options(){
		tutils()->checking_nonce();

		global $wpdb;

		$answer_id = (int) sanitize_text_field($_POST['tutor_quiz_answer_id']);

		if(!tutils()->can_user_manage('quiz_answer', $answer_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		$questions = $_POST['tutor_quiz_question'];
		$answers = $_POST['quiz_answer'];

		foreach ($answers as $question_id => $answer){
			$question = tutor_utils()->avalue_dot($question_id, $questions);
			$question_type = $question['question_type'];

			if ($question){
				if($question_type === 'multiple_choice' || $question_type === 'single_choice' || $question_type === 'ordering' || $question_type === 'matching' || $question_type === 'image_matching' || $question_type === 'fill_in_the_blank' || $question_type === 'image_answering'  ){

					$answer_data = array(
						'belongs_question_id'   => $question_id,
						'belongs_question_type' => $question_type,
						'answer_title'          => sanitize_text_field( $answer['answer_title'] ) ,
						'image_id'              => isset($answer['image_id']) ? $answer['image_id'] : 0,
						'answer_view_format'    => isset($answer['answer_view_format']) ? sanitize_text_field( $answer['answer_view_format'] )  : '',
					);
					if (isset($answer['matched_answer_title'])){
						$answer_data['answer_two_gap_match'] = sanitize_text_field( $answer['matched_answer_title'] ) ;
					}

					if ($question_type === 'fill_in_the_blank'){
						$answer_data['answer_two_gap_match'] = isset($answer['answer_two_gap_match']) ? sanitize_text_field(trim($answer['answer_two_gap_match'])) : null;
					}

					$wpdb->update($wpdb->prefix.'tutor_quiz_question_answers', $answer_data, array('answer_id' => $answer_id));
				}
			}
		}

		//die(print_r($_POST));
		wp_send_json_success();
    }

	public function tutor_quiz_builder_get_answers_by_question(){
		tutils()->checking_nonce();

		global $wpdb;
		$question_id = sanitize_text_field($_POST['question_id']);
		$question_type = sanitize_text_field($_POST['question_type']);

		if(!tutils()->can_user_manage('question', $question_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tutor_quiz_questions WHERE question_id = %d ", $question_id));
		$answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tutor_quiz_question_answers where belongs_question_id = %d AND belongs_question_type = %s order by answer_order asc ;", $question_id, esc_sql( $question_type ) ));

		ob_start();

		switch ($question_type){
			case 'true_false':
				echo '<label>'.__('Answer options &amp; mark correct', 'tutor').'</label>';
				break;
			case 'ordering':
				echo '<label>'.__('Make sure you’re saving the answers in the right order. Students will have to match this order exactly.', 'tutor').'</label>';
				break;
		}

		if (is_array($answers) && count($answers)){
			foreach ($answers as $answer){
				?>
                <div class="tutor-quiz-answer-wrap" data-answer-id="<?php echo $answer->answer_id; ?>">
                    <div class="tutor-quiz-answer">
                        <span class="tutor-quiz-answer-title">
                            <?php
                            echo stripslashes($answer->answer_title);
                            if ($answer->belongs_question_type === 'fill_in_the_blank'){
                                echo ' ('.__('Answer', 'tutor').' : ';
                                echo '<strong>'. stripslashes($answer->answer_two_gap_match). '</strong>)';
                            }
                            if ($answer->belongs_question_type === 'matching'){
                                echo ' - '.stripslashes($answer->answer_two_gap_match);
                            }
                            ?>
                        </span>

						<?php
						if ($answer->image_id){
							echo '<span class="tutor-question-answer-image"><img src="'.wp_get_attachment_image_url($answer->image_id).'" /> </span>';
						}
						if ($question_type === 'true_false' || $question_type === 'single_choice'){
							?>
                            <span class="tutor-quiz-answers-mark-correct-wrap">
                                <input type="radio" name="mark_as_correct[<?php echo $answer->belongs_question_id; ?>]" value="<?php echo $answer->answer_id; ?>" title="<?php _e('Mark as correct', 'tutor'); ?>" <?php checked(1, $answer->is_correct); ?> >
                            </span>
							<?php
						}elseif ($question_type === 'multiple_choice'){
							?>
                            <span class="tutor-quiz-answers-mark-correct-wrap">
                                <input type="checkbox" name="mark_as_correct[<?php echo $answer->belongs_question_id; ?>]" value="<?php echo $answer->answer_id; ?>" title="<?php _e('Mark as correct', 'tutor'); ?>" <?php checked(1, $answer->is_correct); ?> >
                            </span>
							<?php
						}
						?>
						<?php if( "true_false" != $question_type ):?>
							<span class="tutor-quiz-answer-edit">
								<a href="javascript:;"><i class="tutor-icon-pencil"></i> </a>
							</span>
						<?php endif;?>
                        <span class="tutor-quiz-answer-sort-icon"><i class="tutor-icon-menu-2"></i> </span>
                    </div>
					<?php if( "true_false" != $question_type ):?>
						<div class="tutor-quiz-answer-trash-wrap">
							<a href="javascript:;" class="answer-trash-btn" data-answer-id="<?php echo $answer->answer_id; ?>"><i class="tutor-icon-garbage"></i> </a>
						</div>
					<?php endif;?>
                </div>
				<?php
			}
		}
		$output = ob_get_clean();

		wp_send_json_success(array('output' => $output));
	}

	public function tutor_quiz_builder_delete_answer(){
		tutils()->checking_nonce();

		global $wpdb;
		$answer_id = sanitize_text_field($_POST['answer_id']);
		
		if(!tutils()->can_user_manage('quiz_answer', $answer_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		$wpdb->delete($wpdb->prefix.'tutor_quiz_question_answers', array('answer_id' => esc_sql( $answer_id ) ));
		wp_send_json_success();
	}

	/**
	 * Save quiz questions sorting
	 */
	public function tutor_quiz_question_sorting(){
		tutils()->checking_nonce();

		global $wpdb;

		$question_ids = tutor_utils()->avalue_dot('sorted_question_ids', $_POST);
		if (is_array($question_ids) && count($question_ids) ){
			$i = 0;
			foreach ($question_ids as $key => $question_id){
				if(tutils()->can_user_manage('question', $question_id)) {
					$i++;
					$wpdb->update($wpdb->prefix.'tutor_quiz_questions', array('question_order' => $i), array('question_id' => $question_id));
				}
			}
		}
    }

	/**
	 * Save sorting data for quiz answers
	 */
	public function tutor_quiz_answer_sorting(){
		tutils()->checking_nonce();

	    global $wpdb;

	    if ( ! empty($_POST['sorted_answer_ids']) && is_array($_POST['sorted_answer_ids']) && count($_POST['sorted_answer_ids']) ){
	        $answer_ids = $_POST['sorted_answer_ids'];
	        $i = 0;
	        foreach ($answer_ids as $key => $answer_id){
				if(tutils()->can_user_manage('quiz_answer', $answer_id)) {
					$i++;
		        	$wpdb->update($wpdb->prefix.'tutor_quiz_question_answers', array('answer_order' => $i), array('answer_id' => $answer_id));
				}
            }
        }
    }

	/**
	 * Mark answer as correct
	 */

    public function tutor_mark_answer_as_correct(){
		tutils()->checking_nonce();

	    global $wpdb;

	    $answer_id = sanitize_text_field($_POST['answer_id']);
	    $inputValue = sanitize_text_field($_POST['inputValue']);
		
		if(!tutils()->can_user_manage('quiz_answer', $answer_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

	    $answer = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE answer_id = %d LIMIT 0,1 ;", $answer_id));
	    if ($answer->belongs_question_type === 'single_choice') {
		    $wpdb->update($wpdb->prefix.'tutor_quiz_question_answers', array('is_correct' => 0), array('belongs_question_id' => esc_sql( $answer->belongs_question_id ) ));
	    }
	    $wpdb->update($wpdb->prefix.'tutor_quiz_question_answers', array('is_correct' => esc_sql( $inputValue ) ), array('answer_id' => esc_sql( $answer_id ) ));
    }

	/**
	 * Update quiz settings from modal
	 *
	 * @since : v.1.0.0
	 */
	public function tutor_quiz_modal_update_settings(){
		tutils()->checking_nonce();
		//while creating quiz if creating step is not follow then it may throw error that why check added
		$quiz_id = ( isset( $_POST['quiz_id'] ) ) ? sanitize_text_field( $_POST['quiz_id'] ) : '' ;
		$current_topic_id = sanitize_text_field( $_POST['topic_id'] );
		$course_id = tutor_utils()->get_course_id_by('topic', sanitize_textarea_field( $current_topic_id ) );

		$quiz_option = tutor_utils()->sanitize_array( $_POST['quiz_option'] );
				
		if( !tutils()->can_user_manage('quiz', $quiz_id) ) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		update_post_meta($quiz_id, 'tutor_quiz_option', $quiz_option);
		do_action('tutor_quiz_settings_updated', $quiz_id);

		//@since 1.9.6
		ob_start();
		include  tutor()->path.'views/metabox/course-contents.php';
		$course_contents = ob_get_clean();

		wp_send_json_success( array( 'course_contents' => $course_contents ) );
	}


	//=========================//
    // Front end stuffs
    //=========================//

	/**
	 * Rendering quiz for frontend
     *
     * @since v.1.0.0
	 */

	public function tutor_render_quiz_content(){

		tutils()->checking_nonce();

		$quiz_id = (int) sanitize_text_field(tutor_utils()->avalue_dot('quiz_id', $_POST));

		if(!tutils()->has_enrolled_content_access('quiz', $quiz_id)) {
			wp_send_json_error( array('message'=>__('Access Denied.', 'tutor')) );
		}

		ob_start();
		global $post;

		$post = get_post($quiz_id);
		setup_postdata($post);
		//tutor_lesson_content();

		single_quiz_contents();
		wp_reset_postdata();

		$html = ob_get_clean();
		wp_send_json_success(array('html' => $html));
	}

}