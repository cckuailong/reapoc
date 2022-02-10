<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

$isLoggedIn = is_user_logged_in();
$rating = $isLoggedIn ? tutor_utils()->get_course_rating_by_user() : '';
?>

<div class="tutor-course-enrolled-review-wrap" id>
    <a href="javascript:;" class="write-course-review-link-btn">
		<?php
		if($isLoggedIn && (!empty($rating->rating) || !empty($rating->review))){
			_e('Edit review', 'tutor');
		}else{
			_e('Write a review', 'tutor');
		}
		?>
    </a>
    <div class="tutor-write-review-form" style="display: none;">
		<?php
		if($isLoggedIn) {
			?>
            <form method="post">
                <input type="hidden" name="tutor_course_id" value="<?php echo get_the_ID(); ?>">
                <div class="tutor-write-review-box tutor-star-rating-container">
                    <div class="tutor-form-group">
						<?php
						tutor_utils()->star_rating_generator(tutor_utils()->get_rating_value($rating->rating));
						?>
                    </div>
                    <div class="tutor-form-group">
                        <textarea name="review" placeholder="<?php _e('write a review', 'tutor'); ?>"><?php echo stripslashes($rating->review); ?></textarea>
                    </div>
                    <div class="tutor-form-group">
                        <button type="submit" class="tutor_submit_review_btn tutor-button tutor-button-primary"><?php _e('Submit Review', 'tutor'); ?></button>
                    </div>
                </div>
            </form>
			<?php
		}else{
			ob_start();
			tutor_load_template( 'single.course.login' );
			$output = apply_filters( 'tutor_course/global/login', ob_get_clean());
			echo $output;
		}
		?>
    </div>
</div>