<?php
/**
 * Quiz Review Frontend
 *
 * @since v.1.4.0
 *
 * @author Themeum
 * @url https://themeum.com
 * @package Tutor
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */
?>

<?php
$attempt_id = (int) sanitize_text_field($_GET['attempt_id']);
$attempt = tutor_utils()->get_attempt($attempt_id);

if ( ! $attempt){
	?>
    <h1><?php _e('Attempt not found', 'tutor'); ?></h1>
	<?php
	return;
}

$quiz_attempt_info = tutor_utils()->quiz_attempt_info($attempt->attempt_info);
$answers = tutor_utils()->get_quiz_answers_by_attempt_id($attempt->attempt_id);

$user_id = tutor_utils()->avalue_dot('user_id', $attempt);
$user = get_userdata($user_id);

function show_correct_answer( $answers= array() ){
    if(!empty($answers)){

		echo '<div class="correct-answer-wrap">';
        foreach ($answers as $key => $ans) {
            $type = isset($ans->answer_view_format) ? $ans->answer_view_format : 'text_image';
            if (isset($ans->answer_two_gap_match)) { echo '<div class="matching-type">'; }
            switch ($type) {
				case 'text_image':
					echo '<div class="text-image-type">';
                        if(isset($ans->image_id)){
                            $img_url = wp_get_attachment_image_url($ans->image_id);
                            if($img_url){
                                echo '<span class="image"><img src="'.$img_url.'" /></span>';
                            }
                        }
                        if(isset($ans->answer_title)) {
                            echo '<span class="caption">'.stripslashes($ans->answer_title).'</span>';
                        }
					echo '</div>';
                    break;
				case 'text':
					echo '<div class="text-type">';
                        if(isset($ans->answer_title)) {
                            echo '<span class="text-item-caption">'.stripslashes($ans->answer_title);
                        }
                    echo '</div>';
                    break;
				case 'image':
					echo '<div class="image-type">';
                        if(isset($ans->image_id)){
                            $img_url = wp_get_attachment_image_url($ans->image_id);
                            if($img_url){
                                echo '<span class="image"><img src="'.$img_url.'" />'.'</span>';
                            }
                        }
                    echo '</div>';
                    break;
                default:
                    break;
            }
            if (isset($ans->answer_two_gap_match)) {
                echo '<div class="matching-separator">&nbsp;-&nbsp;</div>';
                echo '<div class="image-match">'.stripslashes($ans->answer_two_gap_match).'</div>';
                echo '</div>';
			}
        }
		echo '</div>';
    }
}

$attempt_data = tutor_utils()->get_attempt($attempt_id);
?>

<div>
    <?php $attempts_page = tutor_utils()->get_tutor_dashboard_page_permalink('quiz-attempts'); ?>
    <a class="prev-btn" href="<?php echo $attempts_page; ?>"><span>&leftarrow;</span><?php _e('Back to Attempt List', 'tutor'); ?></a>
</div>


    <div class="tutor-quiz-attempt-review-wrap">
        <div class="attempt-answers-header">
            <div class="attempt-header-quiz"><?php echo __('Quiz:','tutor')." <a href='" .get_permalink($attempt_data->quiz_id)."'>".get_the_title($attempt_data->quiz_id)."</a>"; ?></div>
            <div class="attempt-header-course"><?php echo __('Course:','tutor')." <a href='" .get_permalink($attempt_data->course_id)."'>".get_the_title($attempt_data->course_id)."</a>"; ?></div>
        </div>
            
        <table class="wp-list-table">
            <tr>
                <th><?php _e('#', 'tutor'); ?></th>
                <th><?php _e('Attempts Date', 'tutor'); ?></th>
                <th><?php _e('Questions', 'tutor'); ?></th>
                <th><?php _e('Total Marks', 'tutor'); ?></th>
                <th><?php _e('Pass Marks', 'tutor'); ?></th>
                <th><?php _e('Correct', 'tutor'); ?></th>
                <th><?php _e('Incorrect', 'tutor'); ?></th>
                <th><?php _e('Earned Marks', 'tutor'); ?></th>
                <th><?php _e('Results', 'tutor'); ?></th>
            </tr>
            
            <tr>
                <td><?php echo $attempt_data->attempt_id; ?></td>
                <td>
                    <?php
                        echo date_i18n(get_option('date_format'), strtotime($attempt_data->attempt_started_at)).' '.date_i18n(get_option('time_format'), strtotime($attempt_data->attempt_started_at));
                    ?>
                </td>
                <td><?php echo $attempt_data->total_questions; ?></td>
                <td><?php echo $attempt_data->total_marks; ?></td>
                <td>
                    <?php 
                        $pass_mark_percent = tutor_utils()->get_quiz_option($attempt_data->quiz_id, 'passing_grade', 0);
                        echo $pass_mark_percent.'%';
                    ?>
                </td>
                <td>
                    <?php
                    $correct = 0;
                    $incorrect = 0;
                    if(is_array($answers) && count($answers) > 0) {
                        foreach ($answers as $answer){
                            if ( (bool) isset( $answer->is_correct ) ? $answer->is_correct : '' ) {
                                $correct++;
                            } else {
                                if ($answer->question_type === 'open_ended' || $answer->question_type === 'short_answer'){
                                } else {
                                    $incorrect++;
                                }
                            }
                        }
                    }
                    echo $correct;
                    ?>
                </td>
                <td><?php echo $incorrect; ?></td>
                <td>
                    <?php 
                        echo $attempt_data->earned_marks; 
                        $earned_percentage = $attempt_data->earned_marks > 0 ? ( number_format(($attempt_data->earned_marks * 100) / $attempt_data->total_marks)) : 0;
                        echo '('.$earned_percentage.'%)';
                    ?>
                </td>
                <td>
                    <?php 
                        if ($earned_percentage >= $pass_mark_percent){
                            echo '<span class="result-pass">'.__('Pass', 'tutor').'</span>';
                        }else{
                            echo '<span class="result-fail">'.__('Fail', 'tutor').'</span>';
                        }
                    ?>
                </td>
            </tr>
        </table>
    </div>


    <?php $feedback = get_post_meta($attempt_id ,'instructor_feedback', true); ?>
    <?php if($feedback){ ?>
        <div class="tutor-quiz-attempt-review-wrap">
            <div class="quiz-attempt-answers-wrap">
                <div class="attempt-answers-header">
                    <div class="attempt-header-quiz"><?php _e('Instructor Feedback', 'tutor'); ?></div>
                </div>
                <div class="instructor-feedback-content">
                    <p><?php echo $feedback; ?></p>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="attempt-review-notice-wrap">
		<?php
		if (is_array($answers) && count($answers)) {
			$question_no = 0;
			$required_review = array();

			foreach ($answers as $answer){
				$question_no++;
				if ($answer->question_type === 'open_ended' || $answer->question_type === 'short_answer'){
					$required_review[] = $question_no;
				}
			}
			if (count($required_review)){
				echo '<p class="attempt-review-notice"> <i class="tutor-icon-warning-2"></i> <strong>'.__('Reminder:', 'tutor').' </strong> '.sprintf(__('Please review answers for question no. %s', 'tutor'), implode(', ', $required_review)).'</p>';
			}
		}
		?>

	    <?php if ((bool) $attempt->is_manually_reviewed ){
		    ?>
            <p class="attempt-review-at">
                <span class="circle-arrow">&circlearrowright; </span>
                <strong>
	                <?php _e('Manually reviewed at: ', 'tutor'); ?>
                </strong>
		        <?php echo date_i18n(get_option('date_format'), strtotime($attempt->manually_reviewed_at)).' '.date_i18n(get_option('time_format'), strtotime($attempt->manually_reviewed_at)); ?>
            </p>
		    <?php
	    } ?>
    </div>

	<?php
	if (is_array($answers) && count($answers)){

		?>
        <div class="quiz-attempt-answers-wrap">

            <div class="attempt-answers-header">
                <div class="attempt-header-quiz"><?php _e('Quiz Overview', 'tutor'); ?></div>
            </div>

            <table class="wp-list-table">
                <tr>
                    <th><?php _e('No.', 'tutor'); ?></th>
                    <th><?php _e('Type', 'tutor'); ?></th>
                    <th><?php _e('Question', 'tutor'); ?></th>
                    <th><?php _e('Given Answers', 'tutor'); ?></th>
                    <th><?php _e('Correct Answers', 'tutor'); ?></th>
                    <th><?php _e('Correct/Incorrect', 'tutor'); ?></th>
                    <th><?php _e('Manual Review', 'tutor'); ?></th>
                </tr>
				<?php
				$answer_i = 0;
				foreach ($answers as $answer){
					$answer_i++;
					$question_type = tutor_utils()->get_question_types($answer->question_type);
					?>
                    <tr>
                        <td><?php echo $answer_i; ?></td>
                        <td><?php echo $question_type['icon']; ?></td>
                        <td><?php echo stripslashes($answer->question_title); ?></td>
                        <td>
							<?php
							if ($answer->question_type === 'true_false' || $answer->question_type === 'single_choice' ){
							    $get_answers = tutor_utils()->get_answer_by_id($answer->given_answer);
								$answer_titles = wp_list_pluck($get_answers, 'answer_title');
								$answer_titles = array_map('stripslashes', $answer_titles);
								echo '<p>'.implode('</p><p>', $answer_titles).'</p>';
							}elseif ($answer->question_type === 'multiple_choice'){
								$get_answers = tutor_utils()->get_answer_by_id(maybe_unserialize($answer->given_answer));
								$answer_titles = wp_list_pluck($get_answers, 'answer_title');
								$answer_titles = array_map('stripslashes', $answer_titles);
								echo '<p>'.implode('</p><p>', $answer_titles).'</p>';
							}elseif ($answer->question_type === 'fill_in_the_blank'){
								$answer_titles = maybe_unserialize($answer->given_answer);
								$get_db_answers_by_question = tutor_utils()->get_answers_by_quiz_question($answer->question_id);
								foreach ($get_db_answers_by_question as $db_answer);
								$count_dash_fields = substr_count($db_answer->answer_title, '{dash}');
								if ($count_dash_fields){
									$dash_string = array();
									$input_data = array();
									for($i=0; $i<$count_dash_fields; $i++){
										//$dash_string[] = '{dash}';
										$input_data[] =  isset($answer_titles[$i]) ? "<span class='filled_dash_unser'>{$answer_titles[$i]}</span>" : "______";
									}
									$answer_title = $db_answer->answer_title;
									foreach($input_data as $replace){
										$answer_title = preg_replace('/{dash}/i', $replace, $answer_title, 1);
									}
									echo str_replace('{dash}', '_____', stripslashes($answer_title));
								}

							}elseif ($answer->question_type === 'open_ended' || $answer->question_type === 'short_answer'){

								if ($answer->given_answer){
									echo wpautop(stripslashes($answer->given_answer));
								}

							}elseif ($answer->question_type === 'ordering'){

								$ordering_ids = maybe_unserialize($answer->given_answer);
								foreach ($ordering_ids as $ordering_id){
									$get_answers = tutor_utils()->get_answer_by_id($ordering_id);
									$answer_titles = wp_list_pluck($get_answers, 'answer_title');
									$answer_titles = array_map('stripslashes', $answer_titles);
									echo '<p>'.implode('</p><p>', $answer_titles).'</p>';
								}

							}elseif ($answer->question_type === 'matching'){

								$ordering_ids = maybe_unserialize($answer->given_answer);
								$original_saved_answers = tutor_utils()->get_answers_by_quiz_question($answer->question_id);

								foreach ($original_saved_answers as $key => $original_saved_answer){
									$provided_answer_order_id = isset($ordering_ids[$key]) ? $ordering_ids[$key] : 0;
									$provided_answer_order = tutor_utils()->get_answer_by_id($provided_answer_order_id);
									if(tutils()->count($provided_answer_order)){
                                        foreach ($provided_answer_order as $provided_answer_order);
                                        echo stripslashes($original_saved_answer->answer_title)  .' - '.stripslashes($provided_answer_order->answer_two_gap_match).' <br />';
                                    }
								}

							}elseif ($answer->question_type === 'image_matching'){

								$ordering_ids = maybe_unserialize($answer->given_answer);
								$original_saved_answers = tutor_utils()->get_answers_by_quiz_question($answer->question_id);

								echo '<div class="answer-image-matched-wrap">';
								foreach ($original_saved_answers as $key => $original_saved_answer){
									$provided_answer_order_id = isset($ordering_ids[$key]) ? $ordering_ids[$key] : 0;
									$provided_answer_order = tutor_utils()->get_answer_by_id($provided_answer_order_id);
									foreach ($provided_answer_order as $provided_answer_order);
									?>
                                    <div class="image-matching-item">
                                        <p class="dragged-img-rap"><img src="<?php echo wp_get_attachment_image_url( $original_saved_answer->image_id); ?>" /> </p>
                                        <p class="dragged-caption"><?php echo stripslashes($provided_answer_order->answer_title); ?></p>
                                    </div>
									<?php
								}
								echo '</div>';
							}elseif ($answer->question_type === 'image_answering'){

								$ordering_ids = maybe_unserialize($answer->given_answer);

								echo '<div class="answer-image-matched-wrap">';
								foreach ($ordering_ids as $answer_id => $image_answer){
									$db_answers = tutor_utils()->get_answer_by_id($answer_id);
									foreach ($db_answers as $db_answer);
									?>
                                    <div class="image-matching-item">
                                        <p class="dragged-img-rap"><img src="<?php echo wp_get_attachment_image_url( $db_answer->image_id); ?>" /> </p>
                                        <p class="dragged-caption"><?php echo $image_answer; ?></p>
                                    </div>
									<?php
								}
								echo '</div>';
							}

							?>
                        </td>

                        <td>
                            <?php
                            if (($answer->question_type != 'open_ended' && $answer->question_type != 'short_answer')) {
                                global $wpdb;
                                if ( $answer->question_type === 'true_false' ) {
                                    $correct_answer = $wpdb->get_var( $wpdb->prepare( "SELECT answer_title FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id = %d AND is_correct = 1" , $answer->question_id ) );
                                    echo $correct_answer;

                                } elseif ( $answer->question_type === 'single_choice' ) {
                                    $correct_answer = $wpdb->get_results( $wpdb->prepare( "SELECT answer_title, image_id, answer_view_format FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id = %d AND is_correct = 1", $answer->question_id ) );
                                    show_correct_answer($correct_answer);

                                } elseif ( $answer->question_type === 'multiple_choice' ) {
                                    $correct_answer = $wpdb->get_results( $wpdb->prepare( "SELECT answer_title, image_id, answer_view_format FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id = %d AND is_correct = 1 ;", $answer->question_id ) );
                                    show_correct_answer($correct_answer);

                                } elseif ( $answer->question_type === 'fill_in_the_blank' ) {
                                    $correct_answer = $wpdb->get_var( $wpdb->prepare( "SELECT answer_two_gap_match FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id = %d", $answer->question_id ) );
                                    if($correct_answer){
                                        echo implode(', ', explode('|', stripslashes($correct_answer)));
                                    }
                                    
                                } elseif ( $answer->question_type === 'ordering' ) {
                                    $correct_answer = $wpdb->get_results( $wpdb->prepare( "SELECT answer_title, image_id, answer_view_format FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id = %d ORDER BY answer_order ASC;", $answer->question_id ) );
                                    show_correct_answer($correct_answer);

                                } elseif( $answer->question_type === 'matching' ){
                                    $correct_answer = $wpdb->get_results( $wpdb->prepare( "SELECT answer_title, image_id, answer_two_gap_match, answer_view_format FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id = %d ORDER BY answer_order ASC;", $answer->question_id ) );
                                    show_correct_answer($correct_answer);

                                } elseif( $answer->question_type === 'image_matching' ) {
                                    $correct_answer = $wpdb->get_results( $wpdb->prepare( "SELECT answer_title, image_id, answer_two_gap_match FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id = %d ORDER BY answer_order ASC;", $answer->question_id ) );
                                    show_correct_answer($correct_answer);
                                }
                            }
                            ?>
                        </td>

                        <td>
							<?php
							if ( (bool) isset( $answer->is_correct ) ? $answer->is_correct : '' ) {
								echo '<span class="tutor-status-approved-context"><i class="tutor-icon-mark"></i> '.__('Correct', 'tutor').'</span>';
							} else {
								if ($answer->question_type === 'open_ended' || $answer->question_type === 'short_answer'){
                                    if ( (bool) $attempt->is_manually_reviewed && (!isset( $answer->is_correct ) || $answer->is_correct == 0 )) {
                                        echo '<span class="tutor-status-blocked-context"><i class="tutor-icon-line-cross"></i> '.__('Incorrect', 'tutor').'</span>';
                                    } else {
                                        echo '<p style="color: #878A8F;"><span style="color: #ff282a;">&ast;</span> '.__('Review Required', 'tutor').'</p>';
                                    }
								} else {
									echo '<span class="tutor-status-blocked-context"><i class="tutor-icon-line-cross"></i> '.__('Incorrect', 'tutor').'</span>';
								}
							}
							?>
                        </td>

                        <td style="white-space: nowrap">
							<a href="javascript:;" data-attempt-id="<?php echo $attempt_id; ?>" data-attempt-answer-id="<?php echo $answer->attempt_answer_id; ?>" data-mark-as="correct" title="<?php _e('Mark as correct', 'tutor'); ?>" class="quiz-manual-review-action"><i class="tutor-icon-mark"></i> </a>
                            <a href="javascript:;" data-attempt-id="<?php echo $attempt_id; ?>" data-attempt-answer-id="<?php echo $answer->attempt_answer_id; ?>" data-mark-as="incorrect" title="<?php _e('Mark as In correct', 'tutor'); ?>" class="quiz-manual-review-action"><i class="tutor-icon-line-cross"></i></a>
                        </td>
                    </tr>
					<?php
				}
				?>
            </table>
        </div>

		<?php
	}
	?>

<div class="quiz-attempt-answers-wrap">
    <div class="attempt-answers-header">
        <div class="attempt-header-quiz"><?php _e('Instructor Feedback', 'tutor'); ?></div>
    </div>
    <div class="tutor-instructor-feedback-wrap">
        <textarea class="tutor-instructor-feedback-content" style="width:100%; height: 100px;"><?php echo get_post_meta($attempt_id, 'instructor_feedback', true); ?></textarea>
        <a class="tutor-button tutor-button-primary tutor-instructor-feedback" data-attemptid="<?php echo $attempt_id; ?>" data-toast_success_message="<?php _e('Updated', 'tutor'); ?>"><?php _e('Update', 'tutor'); ?></a>
    </div>
</div>