<?php
/**
 * @package TutorLMS/Templates
 * @version 1.6.4
 */

$passing_grade = tutor_utils()->get_quiz_option($quiz_id, 'passing_grade', 0);

?>

<div class="tutor-quiz-attempt-history single-quiz-page">
    <div class="tutor-quiz-attempt-history-title"><?php _e('Previous attempts', 'tutor'); ?></div>
    <table class="tutor-table">
        <thead>
        <tr>
            <th><?php _e('Course Info', 'tutor'); ?></th>
            <th><?php _e('Correct Answer', 'tutor'); ?></th>
            <th><?php _e('Incorrect Answer', 'tutor'); ?></th>
            <th><?php _e('Earned Marks', 'tutor'); ?></th>
            <th><?php _e('Result', 'tutor'); ?></th>
            <th></th>
			<?php do_action('tutor_quiz/previous_attempts/table/thead/col'); ?>
        </tr>
        </thead>

        <tbody>
		<?php
		foreach ( $previous_attempts as $attempt){
            $attempt_action = tutor_utils()->get_tutor_dashboard_page_permalink('my-quiz-attempts/attempts-details/?attempt_id='.$attempt->attempt_id);
            $earned_percentage = $attempt->earned_marks > 0 ? ( number_format(($attempt->earned_marks * 100) / $attempt->total_marks)) : 0;
            $passing_grade = (int) tutor_utils()->get_quiz_option($attempt->quiz_id, 'passing_grade', 0);
            $answers = tutor_utils()->get_quiz_answers_by_attempt_id($attempt->attempt_id); 
            ?>
            <tr>
                <td>
                    <div class="course">
                        <a href="<?php echo get_the_permalink($attempt->course_id); ?>" target="_blank"><?php echo get_the_title($attempt->course_id); ?></a>
                    </div>
                    <div class="course-meta">
                        <span><?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($attempt->attempt_ended_at)); ?></span>
                        <span><?php _e('Question: ','tutor'); ?><strong><?php echo count($answers); ?></strong></span>
                        <span><?php _e('Total Marks: ','tutor'); ?><strong><?php echo $attempt->total_marks; ?></strong></span>
                    </div>
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
                <td>
                    <?php echo $incorrect; ?>
                </td>
                <td>
                    <?php echo $attempt->earned_marks.' ('.$earned_percentage.'%)'; ?>
                </td>
                <td>
                    <?php
                        if ($attempt->attempt_status === 'review_required'){
                            echo '<span class="result-review-required">' . __('Under Review', 'tutor') . '</span>';
                        }else{
                            echo $earned_percentage >= $passing_grade ? '<span class="result-pass">'.__('Pass', 'tutor').'</span>' : '<span class="result-fail">'.__('Fail', 'tutor').'</span>';
                        }
                    ?>
                </td>
                <td><a href="<?php echo $attempt_action; ?>"><?php _e('Details', 'tutor'); ?></a></td>
                <?php do_action('tutor_quiz/previous_attempts/table/tbody/col', $attempt); ?>
            </tr>
			<?php
		}
		?>
        </tbody>

    </table>
</div>
