<?php
/**
 * A base module for the following types of tags:
 * [text] and [text*]      # Single-line text
 * [email] and [email*]    # Email address
 * [url] and [url*]        # URL
 * [tel] and [tel*]        # Telephone number
 */

defined( 'ABSPATH' ) || exit;

/* form_tag handler */
add_action( 'wpcf7_init', 'wpcf7_add_form_tag_password', 10, 0 );

function wpcf7_add_form_tag_password() {
	if ( function_exists( 'wpcf7_add_form_tag' ) ) {
		wpcf7_add_form_tag(
			array( 'password', 'password*' ),
			'wpcf7_password_form_tag_handler',
			array( 'name-attr' => true )
		);
	}
}

function wpcf7_password_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );
	$class            = wpcf7_form_controls_class( $tag->type, 'wpcf7-password' );

	if ( in_array( $tag->basetype, array( 'password' ), true ) ) {
		$class .= ' wpcf7-validates-as-' . $tag->basetype;
	}

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts              = array();
	$atts['size']      = $tag->get_size_option( '40' );
	$atts['maxlength'] = $tag->get_maxlength_option();
	$atts['minlength'] = $tag->get_minlength_option();

	if ( $atts['maxlength'] && $atts['minlength'] ) {
		$atts['pattern'] = ".{{$atts['minlength']},{$atts['maxlength']}}";
	} elseif ( $atts['minlength'] ) {
		$atts['pattern'] = ".{{$atts['minlength']},}";
	}

	if ( $atts['maxlength'] and $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
		unset( $atts['maxlength'], $atts['minlength'] );
	}

	$atts['class']        = $tag->get_class_option( $class );
	$atts['id']           = $tag->get_id_option();
	$atts['tabindex']     = $tag->get_option( 'tabindex', 'signed_int', true );
	$atts['autocomplete'] = $tag->get_option(
		'autocomplete',
		'[-0-9a-zA-Z]+',
		true
	);

	if ( $tag->has_option( 'readonly' ) ) {
		$atts['readonly'] = 'readonly';
	}

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';
	$value                = (string) reset( $tag->values );

	if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
		$atts['placeholder'] = $value;
		$value               = '';
	}

	$value         = $tag->get_default_option( $value );
	$value         = wpcf7_get_hangover( $tag->name, $value );
	$atts['value'] = $value;

	if ( wpcf7_support_html5() ) {
		$atts['type'] = $tag->basetype;
	} else {
		$atts['type'] = 'password';
	}

	$atts['name'] = $tag->name;
	$atts         = wpcf7_format_atts( $atts );
	$html         = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><input %2$s autocomplete="false"/>%3$s</span>',
		sanitize_html_class( $tag->name ),
		$atts,
		$validation_error
	);

	return $html;
}

/* Validation filter */
add_filter( 'wpcf7_validate_password', 'wpcf7_password_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_password*', 'wpcf7_password_validation_filter', 10, 2 );

function wpcf7_password_validation_filter( $result, $tag ) {
	$name  = $tag->name;
	$value = isset( $_POST[ $name ] )
		? trim( wp_unslash( strtr( (string) $_POST[ $name ], "\n", ' ' ) ) )
		: '';
	if ( 'password' === $tag->basetype ) {
		if ( $tag->is_required() and '' === $value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}
		$atts['maxlength'] = $tag->get_maxlength_option();
		$atts['minlength'] = $tag->get_minlength_option();
		if ( $atts['maxlength'] && strlen( $value ) > $atts['maxlength'] ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_long' ) );
		}
		if ( $atts['minlength'] && strlen( $value ) < $atts['minlength'] ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_too_short' ) );
		}
	}
	return $result;
}

/* Messages */
add_filter( 'wpcf7_messages', 'wpcf7_password_messages', 10, 1 );

function wpcf7_password_messages( $messages ) {
	$messages = array_merge(
		$messages,
		array(
			'invalid_email' => array(
				'description' =>
					__( 'Email address that the sender entered is invalid', 'wpcf7-redirect' ),
				'default'     =>
					__( 'The e-mail address entered is invalid.', 'wpcf7-redirect' ),
			),
			'invalid_url'   => array(
				'description' =>
					__( 'URL that the sender entered is invalid', 'wpcf7-redirect' ),
				'default'     =>
					__( 'The URL is invalid.', 'wpcf7-redirect' ),
			),
			'invalid_tel'   => array(
				'description' =>
					__( 'Telephone number that the sender entered is invalid', 'wpcf7-redirect' ),
				'default'     =>
					__( 'The telephone number is invalid.', 'wpcf7-redirect' ),
			),
		)
	);
	return $messages;
}

/**
 * Tag generator
 */
add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_password', 15, 0 );

function wpcf7_add_tag_generator_password() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add(
		'password',
		__( 'password', 'wpcf7-redirect' ),
		'wpcf7_tag_generator_password'
	);
}

function wpcf7_tag_generator_password( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = $args['id'];

	if ( 'password' === $type ) {
		$description = __( 'Generate a form-tag for a single-line plain password input field.', 'wpcf7-redirect' );
	}

	?>
	<div class="control-box">
		<fieldset>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php echo esc_html( __( 'Field type', 'wpcf7-redirect' ) ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'wpcf7-redirect' ) ); ?></legend>
								<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'wpcf7-redirect' ) ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'wpcf7-redirect' ) ); ?></label></th>
						<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Default value', 'wpcf7-redirect' ) ); ?></label></th>
						<td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br />
						<label><input type="checkbox" name="placeholder" class="option" /> <?php echo esc_html( __( 'Use this text as the placeholder of the field', 'wpcf7-redirect' ) ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'wpcf7-redirect' ) ); ?></label></th>
						<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'wpcf7-redirect' ) ); ?></label></th>
						<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="insert-box">
		<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
		<div class="submitbox">
			<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'wpcf7-redirect' ) ); ?>" />
		</div>
		<br class="clear" />
		<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( 'To use the value input through this field in a mail field, you need to insert the corresponding mail-tag into the field on the Mail tab.', 'wpcf7-redirect' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
	</div>
	<?php
}
