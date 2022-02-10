<?php
/**
 * Don't change it, it's supporting modal in other place
 * if get_the_ID() empty, then it's means we are passing $post variable from another place
 */
if (get_the_ID())
	global $post;

?>
<div class="tutor-lesson-attachments-metabox">
	<div class="tutor-added-attachments-wrap tutor-course-builder-attachments is-lesson">
		<?php
		$attachments = tutor_utils()->get_attachments($post->ID);
		if ( is_array($attachments) && count($attachments)) {
			foreach ( $attachments as $attachment ) {
				?>
				<div class="tutor-added-attachment">
                    <i class="tutor-icon-archive"></i>
                    <a href="javascript:;" class="tutor-delete-attachment tutor-icon-line-cross"></a>
                    <a href="<?php echo $attachment->url; ?>"><?php echo $attachment->name; ?></a>
                    <input type="hidden" name="tutor_attachments[]" value="<?php echo $attachment->id; ?>">
				</div>
			<?php }
		}
		?>
	</div>

	<button type="button" class="tutor-btn tutorUploadAttachmentBtn bordered-btn"><?php _e('Upload Attachment', 'tutor'); ?></button>
</div>