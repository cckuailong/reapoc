<?php
/*
@REST API for quiz
@author : themeum
*/

namespace TUTOR;
use WP_REST_Request;

if(!defined ('ABSPATH'))
exit;

class REST_Quiz {

	use REST_Response;

	private $post_type = "tutor_quiz";
	private $post_parent;
	private $t_quiz_question = "tutor_quiz_questions";
	private $t_quiz_ques_ans = "tutor_quiz_question_answers";
	private $t_quiz_attempt = "tutor_quiz_attempts";
	private $t_quiz_attempt_ans = "tutor_quiz_attempt_answers";

	public function quiz_with_settings(WP_REST_Request $request) {
		$this->post_parent = $request->get_param('id');

		global $wpdb;

		$table = $wpdb->prefix."posts";

		$quizs = $wpdb->get_results(
			$wpdb->prepare("SELECT ID, post_title, post_content, post_name FROM $table WHERE post_type = %s AND post_parent = %d", $this->post_type, $this->post_parent)
		);

		$data = [];

		if (count($quizs)>0) {
			foreach ($quizs as $quiz) {
				$quiz->quiz_settings = get_post_meta($quiz->ID,'tutor_quiz_option',false);

				array_push($data, $quiz);

				$response = array(
					'status_code'=> 'success',
					'message'=> __("Quiz retrieved successfully",'tutor'),
					'data'=> $data
				);
			}
			return self::send($response);
		}	
		$response = array(
			'status_code'=> 'not_found',
			'message'=> __("Quiz not found for given ID",'tutor'),
			'data'=> $data
		);
		return self::send($response);
	}

	public function quiz_question_ans(WP_REST_Request $request) {
		global $wpdb;

		$this->post_parent = $request->get_param('id');


		$q_t = $wpdb->prefix.$this->t_quiz_question;//question table

		$q_a_t = $wpdb->prefix.$this->t_quiz_ques_ans;//question answer table

		$quizs = $wpdb->get_results(
			$wpdb->prepare("SELECT question_id,question_title, question_description, question_type, question_mark, question_settings FROM $q_t WHERE quiz_id = %d", $this->post_parent)
		);			
		$data = [];

		if (count($quizs)>0) {

			//get question ans by question_id
			foreach ($quizs as $quiz) {
				//unserialized question settings
				$quiz->question_settings = maybe_unserialize($quiz->question_settings);

				//question options with correct ans
				$options = $wpdb->get_results(
					$wpdb->prepare("SELECT answer_title,is_correct FROM $q_a_t WHERE belongs_question_id = %d", $quiz->question_id)
				);

				//set question_answers as quiz property
				$quiz->question_answers = $options;

				array_push($data, $quiz);
			}

			$response = array(
				'status_code'=> 'success',
				'message'=> __('Question retrieved successfully','tutor'),
				'data'=> $data
			);

			return self::send($response);
		}

		$response = array(
			'status_code'=> 'not_found',
			'message'=> __('Question not found for given ID','tutor'),
			'data'=> []
		);

		return self::send($response);		
	}

	public function quiz_attempt_details(WP_REST_Request $request) {
		$quiz_id = $request->get_param('id');

		global $wpdb;
		$quiz_attempt = $wpdb->prefix.$this->t_quiz_attempt;

		$attempts = $wpdb->get_results(
			$wpdb->prepare("SELECT att.user_id,att.total_questions,att.total_answered_questions,att.total_marks,att.earned_marks,att.attempt_info,att.attempt_status,att.attempt_started_at,att.attempt_ended_at,att.is_manually_reviewed,att.manually_reviewed_at FROM $quiz_attempt att WHERE att.quiz_id = %d", $quiz_id)
		);
		
		if (count($attempts)>0) {
			//unserialize each attempt info
			foreach ($attempts as $key => $attempt) {
				$attempt->attempt_info = maybe_unserialize($attempt->attempt_info);
				//attach attempt ans
				$answers = $this->get_quiz_attemp_ans($quiz_id);
				
				if($answers !==false)
				{
					$attempt->attempts_answer = $answers;
				}
				else
				{
					$attempt->attempts_answer = [];
				}
				
			}

			$response = array(
				'status_code'=> 'success',
				'message'=> __('Quiz attempts retrieved successfully','tutor'),
				'data'=> $attempts
			);

			return self::send($response);						
		}
		$response = array(
			'status_code'=> 'not_found',
			'message'=> __('Quiz attempts not found for given ID','tutor'),
			'data'=> []
		);

		return self::send($response);
	}

	/*
	*required quiz_id
	*return attempts ans
	*/
	protected function get_quiz_attemp_ans($quiz_id) {
		global $wpdb;
		$quiz_attempt_ans = $wpdb->prefix.$this->t_quiz_attempt_ans;
		$quiz_question = $wpdb->prefix.$this->t_quiz_question;		
		//get attempt answers
		$answers = $wpdb->get_results(
			$wpdb->prepare("SELECT q.question_title,att_ans.given_answer,att_ans.question_mark,att_ans.achieved_mark,att_ans.minus_mark,att_ans.is_correct FROM $quiz_attempt_ans as att_ans JOIN $quiz_question q ON q.question_id = att_ans.question_id WHERE att_ans.quiz_id = %d",$quiz_id)
		);

		if (count($answers)>0) {
			//unserialize each given answer
			foreach ($answers as $key => $answer) {
				$answer->given_answer = maybe_unserialize($answer->given_answer);

				if(is_numeric($answer->given_answer) || is_array($answer->given_answer))
				{
					$ids = $answer->given_answer;
					$ans_title = $this->answer_titles_by_id($ids);
					$answer->given_answer = $ans_title;
				}
			}

			return $answers;			
		}
		return false;
	}
	
	/*
	*require ids (1,2,3)
	*return results containing answer title
	*/
	protected function answer_titles_by_id($id) {
		global $wpdb;
		$table = $wpdb->prefix.$this->t_quiz_ques_ans;

		if(is_array($id)) {
			$string = implode(',', $id);
			$array=array_map('intval', explode(',', $string));
			$array = implode("','",$array);

			$results = $wpdb->get_results(
				"SELECT answer_title FROM $table WHERE answer_id IN ('".$array."')"
			);			
		} else {
			$results = $wpdb->get_results(
				"SELECT answer_title FROM $table WHERE answer_id = {$id}"
			);
		}

		return $results;
	} 
}
