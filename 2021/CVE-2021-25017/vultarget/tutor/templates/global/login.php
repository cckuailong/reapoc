<?php

/**
 * Display global login
 *
 * @since v.1.0.0
 * @author themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
*/


if ( ! defined( 'ABSPATH' ) )
	exit;
?>

<div class="tutor-login-form-wrap">
	<?php do_action("tutor_before_login_form");?>

    <?php
    $current_url = tutils()->get_current_url();
    $register_page = tutor_utils()->student_register_url();
	$query_args = array(
		'redirect_to' => $current_url
	);
	/**
	 * Get current course to make user auto enroll
	 * 
	 * @since 1.9.8
	 */
	global $post;
	$course_id = '';
	if ( isset( $post->post_type ) && $post->post_type == tutor()->course_post_type ) {
		$course_id = sanitize_text_field( $post->ID );
		$query_args[ 'id' ] = $course_id;
	}
	$register_url = add_query_arg ( $query_args, $register_page );
	//redirect_to
    $args = array(
	    'echo'                      => true,
	    // Default 'redirect' value takes the user back to the request URI.
	    'redirect'                  => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
	    'form_id'                   => 'loginform',
	    'label_username'            => __( 'Username or Email Address', 'tutor' ),
	    'label_password'            => __( 'Password', 'tutor' ),
	    'label_remember'            => __( 'Remember Me', 'tutor' ),
	    'label_log_in'              => __( 'Log In', 'tutor' ),
	    'label_create_new_account'  => __( 'Create a new account', 'tutor' ),
	    'id_username'               => 'user_login',
	    'id_password'               => 'user_pass',
	    'id_remember'               => 'rememberme',
	    'id_submit'                 => 'wp-submit',
	    'remember'                  => true,
	    'value_username'            => tutils()->input_old('log'),
	    // Set 'value_remember' to true to default the "Remember me" checkbox to checked.
	    'value_remember'            => false,
	    'wp_lostpassword_url'       => apply_filters('tutor_lostpassword_url', wp_lostpassword_url()),
	    'wp_lostpassword_label'     => __('Forgot Password?', 'tutor'),
    );

    //action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '"

	// tutor_alert(null, 'warning');

	ob_start();
	tutor_nonce_field();
	$nonce_field = ob_get_clean();
	?>

	<form name="<?php echo $args['form_id']?>" id="<?php echo $args['form_id']?>" method="post">
		<input type="hidden" name="tutor_course_enroll_attempt" value="<?php echo esc_html( $course_id );?>">
	<?php do_action("tutor_login_form_start");?>

	<?php echo $nonce_field;?>
	
	<input type="hidden" name="tutor_action" value="tutor_user_login" />
		<p class="login-username">
			<input type="text" placeholder="<?php echo esc_html( $args['label_username'] )?>" name="log" id="<?php echo esc_attr( $args['id_username'] )?>" class="input" value="<?php echo esc_attr( $args['value_username'] )?>" size="20" />
		</p>

		<p class="login-password">
			<input type="password" placeholder="<?php echo esc_html( $args['label_password'] )?>" name="pwd" id="<?php echo esc_attr( $args['id_password'] )?>" class="input" value="" size="20"/>
		</p>
		
		<?php 
			do_action("tutor_login_form_middle");
			do_action("login_form");
			apply_filters("login_form_middle",'','');
		?>


		<div class="tutor-login-rememeber-wrap">
			<?php  if($args['remember']):?>
			<p class="login-remember">
				<label>
					<input name="rememberme" type="checkbox" id="<?php echo esc_attr( $args['id_remember'] )?>" 
					value="forever"
					 <?php $args['value_remember'] ? 'checked' : '';?>
					>
					<?php echo esc_html($args['label_remember']);?>
				</label>
			</p>
			<?php endif;?>
		    <a href="<?php echo esc_url($args['wp_lostpassword_url'])?>">
		    	<?php echo esc_html($args['wp_lostpassword_label']);?>
		    </a>
		</div>
		
		<?php do_action("tutor_login_form_end");?>

		<p class="login-submit">
			<input type="submit" name="wp-submit" id="<?php echo esc_attr( $args['id_submit'] )?>" class="tutor-button" value="<?php echo esc_attr( $args['label_log_in'] )?>" />
			<input type="hidden" name="redirect_to" value="<?php echo esc_url( $args['redirect'] )?>" />
		</p>
		
		<?php 
			if(get_option( 'users_can_register', false )) {
				?>
				<p class="tutor-form-register-wrap">
					<a href="<?php echo esc_url( $register_url );?>">
						<?php echo esc_html($args['label_create_new_account']);?>
					</a>
				</p>
				<?php
			}
		?>
	</form>

    <?php
    	//#@TODO: student_register_url() return false, it must be an valid url.
     	do_action("tutor_after_login_form");
    ?>
</div>
