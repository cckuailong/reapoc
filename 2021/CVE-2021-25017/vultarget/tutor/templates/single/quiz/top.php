<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

global $post;
$currentPost = $post;

$course = tutor_utils()->get_course_by_quiz(get_the_ID());
$previous_attempts = tutor_utils()->quiz_attempts();
$attempted_count = is_array($previous_attempts) ? count($previous_attempts) : 0;

$attempts_allowed = tutor_utils()->get_quiz_option(get_the_ID(), 'attempts_allowed', 0);
$passing_grade = tutor_utils()->get_quiz_option(get_the_ID(), 'passing_grade', 0);

$attempt_remaining = $attempts_allowed - $attempted_count;

do_action('tutor_quiz/single/before/top');

?>

<div class="tutor-quiz-header">
    <span class="tutor-quiz-badge"><?php _e('Quiz', 'tutor'); ?></span>
    <h2><?php echo get_the_title(); ?></h2>
    <h5>
		<?php _e('Course', 'tutor'); ?> :
        <a href="<?php echo get_the_permalink($course->ID); ?>"><?php echo get_the_title($course->ID); ?></a>
    </h5>
    <ul class="tutor-quiz-meta">

		<?php
		$total_questions = tutor_utils()->total_questions_for_student_by_quiz(get_the_ID());

		if($total_questions){
			?>
            <li>
                <strong><?php _e('Questions', 'tutor'); ?> :</strong>
				<?php echo $total_questions; ?>
            </li>
			<?php
		}

			$time_limit = tutor_utils()->get_quiz_option(get_the_ID(), 'time_limit.time_value');
			if ($time_limit){
				$time_type 	= tutor_utils()->get_quiz_option(get_the_ID(), 'time_limit.time_type');

				$available_time_type = array(
					'seconds'	=> __( 'seconds', 'tutor' ),
					'minutes'	=> __( 'minutes', 'tutor' ),
					'hours'		=> __( 'hours', 'tutor' ),
					'days'		=> __( 'days', 'tutor' ),
					'weeks'		=> __( 'weeks', 'tutor' ),
				);
			?>
            <li>
                <strong><?php _e('Time', 'tutor'); ?> :</strong>
				<?php echo $time_limit.' '.sprintf( __( '%s', 'tutor' ), isset( $available_time_type[$time_type] ) ? $available_time_type[$time_type] : $time_type ); ?>
            </li>
			<?php
		}

		?>
        <li>
            <strong><?php _e('Attempts Allowed', 'tutor'); ?> :</strong>
	        <?php echo $attempts_allowed == 0 ? __('No limit', 'tutor') : $attempts_allowed; ?>
        </li>
	    <?php

		if($attempted_count){
			?>
            <li>
                <strong><?php _e('Attempted', 'tutor'); ?> :</strong>
				<?php echo $attempted_count; ?>
            </li>
			<?php
		}
		?>
        <li>
            <strong><?php _e('Attempts Remaining', 'tutor'); ?> :</strong>
			<?php echo $attempts_allowed == 0 ? __('No limit', 'tutor') : $attempt_remaining; ?>
        </li>
		<?php
		if($passing_grade){
			?>
            <li>
                <strong><?php _e('Passing Grade', 'tutor'); ?> :</strong>
				<?php echo $passing_grade . '%'; ?>

            </li>
			<?php
		}
		?>
    </ul>
</div>

<?php do_action('tutor_quiz/single/after/top'); ?>
