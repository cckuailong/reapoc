<?php
/**
 * Template for displaying Assignments Review Form
 *
 * @since v.1.3.4
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

$assignment_id = (int) sanitize_text_field(tutor_utils()->array_get('assignment', $_GET));
$assignment_submitted_id = (int) sanitize_text_field(tutor_utils()->array_get('view_assignment', $_GET));
$submitted_url = tutor_utils()->get_tutor_dashboard_page_permalink('assignments/submitted');

if(!$assignment_submitted_id){
	echo _e("Sorry, but you are looking for something that isn't here." , 'tutor');
	return;
}

$submitted_assignment = tutor_utils()->get_assignment_submit_info($assignment_submitted_id);
if ( $submitted_assignment){

	$max_mark = tutor_utils()->get_assignment_option($submitted_assignment->comment_post_ID, 'total_mark');

	$given_mark = get_comment_meta($assignment_submitted_id, 'assignment_mark', true);
    $instructor_note = get_comment_meta($assignment_submitted_id, 'instructor_note', true);
    $comment_author = get_user_by('login', $submitted_assignment->comment_author)
	?>

    <div class="submitted-assignment-title">
        <a class="prev-btn" href="<?php echo esc_url($submitted_url . '?assignment=' . $assignment_id); ?>"><span>&leftarrow;</span><?php _e('Back', 'tutor'); ?></a>
    </div>

    <div class="tutor-assignment-review-header">
        <h3>
            <a href="<?php echo get_the_permalink($submitted_assignment->comment_post_ID); ?>" target="_blank">
				<?php echo get_the_title($submitted_assignment->comment_post_ID); ?>
            </a>
        </h3>
        <p>
			<?php _e('Course' , 'tutor'); ?>:
            <a href="<?php echo get_the_permalink($submitted_assignment->comment_parent); ?>" target="_blank">
				<?php echo get_the_title($submitted_assignment->comment_parent); ?>
            </a>
        </p>
        <p>
			<?php _e('Student' , 'tutor'); ?>:
            <span><?php echo $comment_author->display_name. ' ('.$comment_author->user_email.')'; ?></span>
        </p>
        <p>
			<?php _e('Submitted Date' , 'tutor'); ?>:
            <span><?php echo date('j M, Y, h:i a', strtotime($submitted_assignment->comment_date)); ?></span>
        </p>
    </div>

    <hr>

    <div class="tutor-dashboard-assignment-submitted-content">
        <h4><?php _e('Assignment Description:', 'tutor'); ?></h4>
        <p><?php echo nl2br(stripslashes($submitted_assignment->comment_content)); ?></p>

		<?php
		$attached_files = get_comment_meta($submitted_assignment->comment_ID, 'uploaded_attachments', true);
		if($attached_files){
			?>
            <h5><?php _e('Attach assignment file(s)', 'tutor'); ?></h5>
            <div class="tutor-dashboard-assignment-files">
				<?php
				$attached_files = json_decode($attached_files, true);
				if (tutor_utils()->count($attached_files)){
					$upload_dir = wp_get_upload_dir();
					$upload_baseurl = trailingslashit(tutor_utils()->array_get('baseurl', $upload_dir));
					foreach ($attached_files as $attached_file){
						?>
                        <div class="uploaded-files">
                            <a href="<?php echo $upload_baseurl.tutor_utils()->array_get('uploaded_path', $attached_file) ?>" target="_blank"> <i class="tutor-icon-upload-file"></i> <?php echo tutor_utils()->array_get('name', $attached_file); ?></a>
                        </div>
						<?php
					}
				}
				?>
            </div>
			<?php
		}
		?>
    </div>

    <div class="tutor-dashboard-assignment-review">
        <h3><?php _e('Evaluation', 'tutor'); ?></h3>
        <form action="" method="post" class="tutor-form-submit-through-ajax" data-toast_success_message="<?php _e('Assignment evaluated', 'tutor'); ?>">
			<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>
            <input type="hidden" value="tutor_evaluate_assignment_submission" name="tutor_action"/>
            <input type="hidden" value="<?php echo $assignment_submitted_id; ?>" name="assignment_submitted_id"/>
            <div class="tutor-assignment-evaluate-row">
                <div class="tutor-option-field-label">
                    <label for=""><?php _e('Your Points', 'tutor'); ?></label>
                </div>
                <div class="tutor-option-field input-mark">
                    <input type="number" name="evaluate_assignment[assignment_mark]" value="<?php echo $given_mark ? $given_mark : 0; ?>">
                    <p class="desc"><?php echo sprintf(__('Evaluate this assignment out of %s', 'tutor'), "<code>{$max_mark}</code>" ); ?></p>
                </div>
            </div>
            <div class="tutor-assignment-evaluate-row">
                <div class="tutor-option-field-label">
                    <label for=""><?php _e('Write a note', 'tutor'); ?></label>
                </div>
                <div class="tutor-option-field">
                    <textarea name="evaluate_assignment[instructor_note]"><?php echo $instructor_note; ?></textarea>
                    <p class="desc"><?php _e('Write a note to students about this submission', 'tutor'); ?></p>
                </div>
            </div>
            <div class="tutor-assignment-evaluate-row">
                <div class="tutor-option-field-label"></div>
                <div class="tutor-option-field">
                    <button type="submit" class="tutor-button tutor-button-primary"><?php _e('Evaluate this submission', 'tutor'); ?></button>
                </div>
            </div>
        </form>
    </div>

<?php }else{
	_e('Assignments submission not found or not completed', 'tutor');
} ?>