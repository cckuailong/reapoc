<?php
/**
 * Quiz Attempts, I attempted to courses
 *
 * @since v.1.1.2
 *
 * @author Themeum
 * @url https://themeum.com
 *
 *
 * @package TutorLMS/Templates
 * @version 1.6.4
 */


if(isset($_GET['view_quiz_attempt_id']) && get_tutor_option('tutor_quiz_student_attempt_view_in_profile')) {
    $_GET['attempt_id'] = $_GET['view_quiz_attempt_id'];
    echo tutor_get_template_html('dashboard.my-quiz-attempts.attempts-details');
    return;
}

$previous_attempts = tutor_utils()->get_all_quiz_attempts_by_user();
$attempted_count = is_array($previous_attempts) ? count($previous_attempts) : 0;
?>

<h3><?php _e('My Quiz Attempts', 'tutor'); ?></h3>
<?php
if ($attempted_count){
    ?>
    <div class="tutor-dashboard-content tutor-quiz-attempt-history ">
        <table class="tutor-table">
            <tr>
                <th><?php _e('Course Info', 'tutor'); ?></th>
                <th><?php _e('Correct Answer', 'tutor'); ?></th>
                <th><?php _e('Incorrect Answer', 'tutor'); ?></th>
                <th><?php _e('Earned Marks', 'tutor'); ?></th>
                <th><?php _e('Result', 'tutor'); ?></th>
                <?php do_action('tutor_quiz/my_attempts/table/thead/col'); ?>
            </tr>
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
                    <?php do_action('tutor_quiz/my_attempts/table/tbody/col', $attempt); ?>
                </tr>
                <?php
            }
            ?>

        </table>
    </div>

<?php } else {
    echo __('You have not attempted any quiz yet', 'tutor');
} ?>