<h2><?php _e( 'Tutor Fields', 'tutor' ); ?></h2>

<?php
/**
 * Enqueue Media Scripts
 */
wp_enqueue_media();
?>

<table class="form-table">
	<?php do_action('tutor_backend_profile_fields_before'); ?>
	<tr class="user-description-wrap">
		<th><label for="description"><?php _e('Job Title'); ?></label></th>
		<td>
			<input type="text" name="_tutor_profile_job_title" id="_tutor_profile_job_title" value="<?php echo get_user_meta($user->ID, '_tutor_profile_job_title', true); ?>" class="regular-text" />
		</td>
	</tr>

	<tr class="user-description-wrap">
		<th><label for="description"><?php _e('Profile Bio'); ?></label></th>
		<td>
			<?php
			$settings = array(
				'teeny' => true,
				'media_buttons' => false,
				'quicktags' => false,
				'editor_height' => 200,
			);
			wp_editor(get_user_meta($user->ID, '_tutor_profile_bio', true), '_tutor_profile_bio', $settings);
			?>

			<p class="description"><?php _e('Write a little bit more about you, it will show publicly.'); ?></p>
		</td>
	</tr>

	<tr class="user-description-wrap">
		<th><label for="description"><?php _e('Profile Photo'); ?></label></th>
		<td>
			<div class="tutor-video-poster-wrap">
				<p class="video-poster-img">
					<?php
					$user_profile_photo = get_user_meta($user->ID, '_tutor_profile_photo', true);
					if ($user_profile_photo){
						echo '<img src="'.wp_get_attachment_image_url($user_profile_photo).'" alt="" /> ';
					}
					?>
				</p>
				<input type="hidden" name="_tutor_profile_photo" value="<?php echo $user_profile_photo; ?>">
				<button type="button" class="tutor_video_poster_upload_btn button button-primary"><?php _e('Upload', 'tutor'); ?></button>
			</div>

			<input type="hidden" name="tutor_action" value="tutor_profile_update_by_wp">
		</td>
	</tr>
	<?php do_action('tutor_backend_profile_fields_after'); ?>
</table>

