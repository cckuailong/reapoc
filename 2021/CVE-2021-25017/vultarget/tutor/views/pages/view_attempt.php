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
?>


<div class="tutor-quiz-attempt-review-wrap">
    <div class="attempt-review-title"> <i class="tutor-icon-list"></i> <?php _e('View Attempts', 'tutor'); ?></div>

    <div class="tutor-quiz-attempt-info-row">
        <div class="attempt-view-top">
            <div class="attempt-info-col">
                <div class="attempt-user-details">
                    <div class="attempt-user-avatar">
                        <img src="<?php echo esc_url(get_avatar_url($user_id)) ?>" alt="<?php echo esc_attr($user->display_name); ?>">
                    </div>
                    <div class="attempt-info-content">
                        <h5><?php echo __('Student Name', 'tutor'); ?></h5>
                        <h4><?php echo $user->display_name; ?></h4>
                    </div>
                </div>
            </div>

            <div class="attempt-info-col">
                <div class="attempt-info-content">
                    <h5><?php echo __('Quiz', 'tutor'); ?></h5>
                    <h4>
                        <?php
                        echo "<a href='" .admin_url("post.php?post={$attempt->quiz_id}&action=edit")."'>".get_the_title($attempt->quiz_id)."</a>";
                        ?>
                    </h4>
                </div>
            </div>

            <div class="attempt-info-col">
                <div class="attempt-info-content">
                    <h5><?php echo __('Attempt Time', 'tutor'); ?></h5>
                    <h4>
                        <?php echo date_i18n(get_option('date_format'), strtotime($attempt->attempt_started_at)).' '.date_i18n(get_option('time_format'), strtotime($attempt->attempt_started_at)); ?>
                    </h4>
                </div>
            </div>

            <div class="attempt-info-col">
                <div class="attempt-info-content">
                    <h5><?php echo __('Status', 'tutor'); ?></h5>
                    <h4>
                        <?php
                        $status = ucwords(str_replace('quiz_', '', $attempt->attempt_status));
                            esc_html_e( $status, 'tutor' );
                        ?>
                    </h4>
                </div>
            </div>
        </div>

        <div class="attempt-view-bottom">
            <div class="attempt-info-col">
                <div class="attempt-info-content">
                    <h5><?php echo __('Course', 'tutor'); ?></h5>
                    <h4>
                        <?php
                        $quiz = tutor_utils()->get_course_by_quiz($attempt->quiz_id);
                        if ($quiz) {
                            echo "<a href='".admin_url( "post.php?post={$quiz->ID}&action=edit" ) . "'>". get_the_title( $quiz->ID )."</a>";
                        }
                        ?>
                    </h4>
                </div>
            </div>

            <div class="attempt-info-col">
                <div class="attempt-info-content">
                    <h5><?php echo __('Result', 'tutor'); ?></h5>
                    <h4>
                        <?php
                        $marks_earned_text = __( "Marks earned", 'tutor' );
                        if ($attempt->attempt_status === 'review_required'){
                            $output = '<span class="result-review-required">' . __('Under Review', 'tutor') . '</span>';
                        }else {

                            $pass_mark_percent = tutor_utils()->get_quiz_option($attempt->quiz_id, 'passing_grade', 0);
                            $earned_percentage = $attempt->earned_marks > 0 ? (number_format(($attempt->earned_marks * 100) / $attempt->total_marks)) : 0;
                            $output = '';
                            if ($earned_percentage >= $pass_mark_percent) {
                                $output .= '<span class="result-pass">' . __('Pass', 'tutor') . '</span>';
                            } else {
                                $output .= '<span class="result-fail">' . __('Fail', 'tutor') . '</span>';
                            }

                            $output .= __( $attempt->earned_marks." out of ".$attempt->total_marks, 'tutor' );
                            $output .= "<span>, $marks_earned_text ($earned_percentage%)</span>";
                        }
                        echo $output;
                        ?>
                    </h4>
                </div>
            </div>

            <div class="attempt-info-col">
                <div class="attempt-info-content">
                    <h5><?php echo __('Quiz Time', 'tutor'); ?></h5>
                    <h4>
                        <?php
                        $time_limit_seconds = tutor_utils()->avalue_dot('time_limit.time_limit_seconds', $quiz_attempt_info);
                        echo tutor_utils()->seconds_to_time_context($time_limit_seconds);
                        ?>
                    </h4>
                </div>
            </div>

            <div class="attempt-info-col">
                <div class="attempt-info-content">
                    <h5><?php echo __('Attempt Time', 'tutor'); ?></h5>
                    <h4>
                        <?php
                        $attempt_time_sec = strtotime($attempt->attempt_ended_at) - strtotime($attempt->attempt_started_at);
                        echo tutor_utils()->seconds_to_time_context($attempt_time_sec);
                        ?>
                    </h4>
                </div>

            </div>

        </div>

    </div>


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
                    <th><?php _e('Type', 'tutor'); ?></th>
                    <th><?php _e('No.', 'tutor'); ?></th>
                    <th><?php _e('Question', 'tutor'); ?></th>
                    <th><?php _e('Given Answers', 'tutor'); ?></th>
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
                        <td><?php echo $question_type['icon']; ?></td>
                        <td><?php echo $answer_i; ?></td>
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
                                        echo stripslashes($original_saved_answer->answer_title)  .' - '.stripslashes($provided_answer_order->answer_two_gap_match).'<br />';
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

    if ( $answer->is_correct ) {
        echo '<span class="quiz-correct-answer-text"><i class="tutor-icon-mark"></i> '.__('Correct', 'tutor').'</span>';
    } 
    else {
        if ($answer->question_type === 'open_ended' || $answer->question_type === 'short_answer')
        {

            //if ( (bool) $attempt->is_manually_reviewed && (!isset( $answer->is_correct ) || $answer->is_correct == 0 )) {
            if($answer->is_correct==NULL)
            {
                echo '<p style="color: #878A8F;"><span style="color: #ff282a;">&ast;</span> '.__('Review Required', 'tutor').'</p>';
            }
            else if ( $answer->is_correct == 0 ) {

                echo '<span class="tutor-status-blocked-context"><i class="tutor-icon-line-cross"></i> '.__('Incorrect', 'tutor').'</span>';
            } 
            else {
                echo '<span class="quiz-correct-answer-text"><i class="tutor-icon-mark"></i> '.__('Correct', 'tutor').'</span>';
            }
        } 
        else {
            echo '<span class="quiz-incorrect-answer-text"><i class="tutor-icon-line-cross"></i> '.__('Incorrect', 'tutor').'</span>';
        }
    }
    ?>
</td>

                        <td style="white-space: nowrap">
                            <?php 
                                $nonce_key = tutor()->nonce;
                                $nonce_value = wp_create_nonce(tutor()->nonce_action);
                            ?>
                            <a href="<?php echo admin_url("admin.php?{$nonce_key}={$nonce_value}&action=review_quiz_answer&attempt_id={$attempt_id}&attempt_answer_id={$answer->attempt_answer_id}&mark_as=correct"); ?>" title="<?php _e('Mark as correct', 'tutor'); ?>" class="attempt-mark-correct-btn quiz-manual-review-action"><i class="tutor-icon-mark"></i> </a>
                            <a href="<?php echo admin_url("admin.php?{$nonce_key}={$nonce_value}&action=review_quiz_answer&attempt_id={$attempt_id}&attempt_answer_id={$answer->attempt_answer_id}&mark_as=incorrect"); ?>" title="<?php _e('Mark as In correct', 'tutor'); ?>" class="attempt-mark-incorrect-btn quiz-manual-review-action"><i class="tutor-icon-line-cross"></i></a>
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
</div>



<div class="quiz-attempt-answers-wrap">
    <div class="attempt-answers-header">
        <div class="attempt-header-quiz"><?php _e('Instructor Feedback', 'tutor'); ?></div>
    </div>
    <div class="tutor-instructor-feedback-wrap">
        <textarea class="tutor-instructor-feedback-content" style="width:100%; height: 100px;"><?php echo get_post_meta($attempt_id, 'instructor_feedback', true); ?></textarea>
        <a class="tutor-button tutor-button-primary tutor-instructor-feedback" data-attemptid="<?php echo $attempt_id; ?>" data-toast_success_message="<?php _e('Updated', 'tutor'); ?>"><?php _e('Update', 'tutor'); ?></a>
    </div>
</div>
