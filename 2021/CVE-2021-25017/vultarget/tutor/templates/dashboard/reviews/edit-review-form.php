<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */
?>

<form method="post" id="tutor_update_review_form">
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
			<button type="submit" class="tutor-button tutor-button-primary"><?php _e('Update Review', 'tutor'); ?></button>
		</div>
	</div>
</form>