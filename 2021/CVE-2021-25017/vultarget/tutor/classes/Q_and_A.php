<?php

namespace TUTOR;

if (!defined('ABSPATH'))
	exit;

class Q_and_A {

	public function __construct() {
		add_action('admin_post_tutor_place_answer', array($this, 'place_answer'));

		/**
		 * Delete question [frontend dashboard]
		 * @since  v.1.6.4
		 */
		add_action('wp_ajax_tutor_delete_dashboard_question', array($this, 'tutor_delete_dashboard_question'));
	}

	public function place_answer() {
		tutor_utils()->checking_nonce();

		global $wpdb;

		$answer = wp_kses_post($_POST['answer']);
		if (!empty($answer)) {
			$question_id = (int) sanitize_text_field($_POST['question_id']);
			$question = tutor_utils()->get_qa_question($question_id);

			$user_id = get_current_user_id();
			$user = get_userdata($user_id);
			$date = date("Y-m-d H:i:s", tutor_time());

			do_action('tutor_before_answer_to_question');

			$data = apply_filters('tutor_answer_to_question_data', array(
				'comment_post_ID'   => $question->comment_post_ID,
				'comment_author'    => $user->user_login,
				'comment_date'      => $date,
				'comment_date_gmt'  => get_gmt_from_date($date),
				'comment_content'   => $answer,
				'comment_approved'  => 'approved',
				'comment_agent'     => 'TutorLMSPlugin',
				'comment_type'      => 'tutor_q_and_a',
				'comment_parent'    => $question_id,
				'user_id'           => $user_id,
			));

			$wpdb->insert($wpdb->comments, $data);
			$answer_id = (int) $wpdb->insert_id;

			if ($answer_id) {
				$wpdb->update($wpdb->comments, array('comment_approved' => 'answered'), array('comment_ID' => $question_id));
				do_action('tutor_after_answer_to_question', $answer_id);
			}
		}
		wp_redirect(wp_get_referer());
	}

	/**
	 * Delete question [frontend dashboard]
	 * @since  v.1.6.4
	 */
	public function tutor_delete_dashboard_question() {
		tutils()->checking_nonce();

		global $wpdb;
		$question_id = intval(sanitize_text_field($_POST['question_id']));
		
		if( !$question_id || !tutils()->can_user_manage('question', $question_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		//Deleting question (comment), child question and question meta (comment meta)
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->comments} WHERE {$wpdb->comments}.comment_ID = %d", $question_id));
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->comments} WHERE {$wpdb->comments}.comment_parent = %d", $question_id));
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->commentmeta} WHERE {$wpdb->commentmeta}.comment_id = %d", $question_id));
		
		wp_send_json_success(['element'=>'question']);
	}
}
