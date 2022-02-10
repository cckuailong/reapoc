<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

?>

<div class="tutor-dashboard-content-inner">

    <div class="tutor-dashboard-inline-links">
        <?php
            tutor_load_template('dashboard.settings.nav-bar', ['active_setting_nav'=>'reset_password']);
        ?>
    </div>

    <h3><?php _e('Reset Password', 'tutor') ?></h3>

    <div class="tutor-reset-password-form-wrap">

		<?php
		$success_msg = tutor_utils()->get_flash_msg('success');
		if ($success_msg){
			?>
            <div class="tutor-success-msg">
				<?php echo $success_msg; ?>
            </div>
			<?php
		}
		?>


        <form action="" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>
            <input type="hidden" value="tutor_reset_password" name="tutor_action" />

			<?php
			$errors = apply_filters('tutor_reset_password_validation_errors', array());
			if (is_array($errors) && count($errors)){
				echo '<div class="tutor-alert-warning tutor-mb-10"><ul class="tutor-required-fields">';
				foreach ($errors as $error_key => $error_value){
					echo "<li>{$error_value}</li>";
				}
				echo '</ul></div>';
			}
			?>

			<?php do_action('tutor_reset_password_input_before') ?>

            <div class="tutor-form-row">
                <div class="tutor-form-col-12">
                    <div class="tutor-form-group">
                        <label> <?php _e('Current Password', 'tutor'); ?> </label>
                        <input type="password" name="previous_password">
                    </div>
                </div>
            </div>
            <div class="tutor-form-row">
                <div class="tutor-form-col-6">
                    <div class="tutor-form-group">
                        <label><?php _e('New Password', 'tutor'); ?></label>
                        <input type="password" name="new_password">
                    </div>
                </div>
                <div class="tutor-form-col-6">
                    <div class="tutor-form-group">
                        <label><?php _e('Confirm New Password', 'tutor'); ?></label>
                        <input type="password" name="confirm_new_password">
                    </div>
                </div>
            </div>

            <div class="tutor-form-group">
                <label>&nbsp;</label>
                <button type="submit" class="tutor-button" name="tutor_password_reset_btn"><?php _e('Reset Password', 'tutor'); ?></button>
            </div>

			<?php do_action('tutor_reset_password_input_after') ?>

        </form>

    </div>



</div>




