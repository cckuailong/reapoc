<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */
?>

<form method="post" enctype="multipart/form-data" id="tutor-instructor-application-form">
	<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>
	<input type="hidden" value="tutor_apply_instructor" name="tutor_action"/>

	<div class="tutor-form-row">
		<div class="tutor-form-col-12">
			<div class="tutor-form-group">
				<button type="submit" name="tutor_register_instructor_btn" value="apply">
                    <?php _e('Apply to become an instructor', 'tutor'); ?>
                </button>
			</div>
		</div>
	</div>

</form>