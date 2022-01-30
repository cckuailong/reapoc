<?php
/**
** A base module for [file] and [file*]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_file', 10, 0 );

function wpcf7_add_form_tag_file() {
	wpcf7_add_form_tag( array( 'file', 'file*' ),
		'wpcf7_file_form_tag_handler',
		array(
			'name-attr' => true,
		)
	);
}

function wpcf7_file_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['size'] = $tag->get_size_option( '40' );
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	$atts['accept'] = wpcf7_acceptable_filetypes(
		$tag->get_option( 'filetypes' ), 'attr' );

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	if ( $validation_error ) {
		$atts['aria-invalid'] = 'true';
		$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	} else {
		$atts['aria-invalid'] = 'false';
	}

	$atts['type'] = 'file';
	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error
	);

	return $html;
}


/* Encode type filter */

add_filter( 'wpcf7_form_enctype', 'wpcf7_file_form_enctype_filter', 10, 1 );

function wpcf7_file_form_enctype_filter( $enctype ) {
	$multipart = (bool) wpcf7_scan_form_tags(
		array( 'type' => array( 'file', 'file*' ) )
	);

	if ( $multipart ) {
		$enctype = 'multipart/form-data';
	}

	return $enctype;
}


/* Validation + upload handling filter */

add_filter( 'wpcf7_validate_file', 'wpcf7_file_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_file*', 'wpcf7_file_validation_filter', 10, 2 );

function wpcf7_file_validation_filter( $result, $tag ) {
	$name = $tag->name;
	$id = $tag->get_id_option();

	$file = isset( $_FILES[$name] ) ? $_FILES[$name] : null;

	if ( ! empty( $file['error'] ) and UPLOAD_ERR_NO_FILE !== $file['error'] ) {
		$result->invalidate( $tag, wpcf7_get_message( 'upload_failed_php_error' ) );
		return $result;
	}

	if ( empty( $file['tmp_name'] ) and $tag->is_required() ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		return $result;
	}

	if ( empty( $file['tmp_name'] )
	or ! is_uploaded_file( $file['tmp_name'] ) ) {
		return $result;
	}

	/* File type validation */

	$file_type_pattern = wpcf7_acceptable_filetypes(
		$tag->get_option( 'filetypes' ), 'regex'
	);

	$file_type_pattern = '/\.(' . $file_type_pattern . ')$/i';

	if ( empty( $file['name'] )
	or ! preg_match( $file_type_pattern, $file['name'] ) ) {
		$result->invalidate( $tag,
			wpcf7_get_message( 'upload_file_type_invalid' )
		);

		return $result;
	}

	/* File size validation */

	$allowed_size = $tag->get_limit_option();

	if ( ! empty( $file['size'] ) and $allowed_size < $file['size'] ) {
		$result->invalidate( $tag, wpcf7_get_message( 'upload_file_too_large' ) );
		return $result;
	}

	wpcf7_init_uploads(); // Confirm upload dir
	$uploads_dir = wpcf7_upload_tmp_dir();
	$uploads_dir = wpcf7_maybe_add_random_dir( $uploads_dir );

	$filename = $file['name'];
	$filename = wpcf7_canonicalize( $filename, 'as-is' );
	$filename = wpcf7_antiscript_file_name( $filename );

	$filename = apply_filters( 'wpcf7_upload_file_name', $filename,
		$file['name'], $tag
	);

	$filename = wp_unique_filename( $uploads_dir, $filename );
	$new_file = path_join( $uploads_dir, $filename );

	if ( false === @move_uploaded_file( $file['tmp_name'], $new_file ) ) {
		$result->invalidate( $tag, wpcf7_get_message( 'upload_failed' ) );
		return $result;
	}

	// Make sure the uploaded file is only readable for the owner process
	chmod( $new_file, 0400 );

	if ( $submission = WPCF7_Submission::get_instance() ) {
		$submission->add_uploaded_file( $name, $new_file );
	}

	return $result;
}

add_filter( 'wpcf7_mail_tag_replaced_file', 'wpcf7_file_mail_tag', 10, 4 );
add_filter( 'wpcf7_mail_tag_replaced_file*', 'wpcf7_file_mail_tag', 10, 4 );

function wpcf7_file_mail_tag( $replaced, $submitted, $html, $mail_tag ) {
	$submission = WPCF7_Submission::get_instance();
	$uploaded_files = $submission->uploaded_files();
	$name = $mail_tag->field_name();

	if ( ! empty( $uploaded_files[$name] ) ) {
		$replaced = wp_basename( $uploaded_files[$name] );
	}

	return $replaced;
}


/* Messages */

add_filter( 'wpcf7_messages', 'wpcf7_file_messages', 10, 1 );

function wpcf7_file_messages( $messages ) {
	return array_merge( $messages, array(
		'upload_failed' => array(
			'description' => __( "Uploading a file fails for any reason", 'contact-form-7' ),
			'default' => __( "There was an unknown error uploading the file.", 'contact-form-7' )
		),

		'upload_file_type_invalid' => array(
			'description' => __( "Uploaded file is not allowed for file type", 'contact-form-7' ),
			'default' => __( "You are not allowed to upload files of this type.", 'contact-form-7' )
		),

		'upload_file_too_large' => array(
			'description' => __( "Uploaded file is too large", 'contact-form-7' ),
			'default' => __( "The file is too big.", 'contact-form-7' )
		),

		'upload_failed_php_error' => array(
			'description' => __( "Uploading a file fails for PHP error", 'contact-form-7' ),
			'default' => __( "There was an error uploading the file.", 'contact-form-7' )
		)
	) );
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_file', 50, 0 );

function wpcf7_add_tag_generator_file() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'file', __( 'file', 'contact-form-7' ),
		'wpcf7_tag_generator_file' );
}

function wpcf7_tag_generator_file( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'file';

	$description = __( "Generate a form-tag for a file uploading field. For more details, see %s.", 'contact-form-7' );

	$desc_link = wpcf7_link( __( 'https://contactform7.com/file-uploading-and-attachment/', 'contact-form-7' ), __( 'File uploading and attachment', 'contact-form-7' ) );

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-limit' ); ?>"><?php echo esc_html( __( "File size limit (bytes)", 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="limit" class="filesize oneline option" id="<?php echo esc_attr( $args['content'] . '-limit' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>"><?php echo esc_html( __( 'Acceptable file types', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="filetypes" class="filetype oneline option" id="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>

</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>

	<br class="clear" />

	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To attach the file uploaded through this field to mail, you need to insert the corresponding mail-tag (%s) into the File Attachments field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}


/* Warning message */

add_action( 'wpcf7_admin_warnings',
	'wpcf7_file_display_warning_message', 10, 3 );

function wpcf7_file_display_warning_message( $page, $action, $object ) {
	if ( $object instanceof WPCF7_ContactForm ) {
		$contact_form = $object;
	} else {
		return;
	}

	$has_tags = (bool) $contact_form->scan_form_tags(
		array( 'type' => array( 'file', 'file*' ) ) );

	if ( ! $has_tags ) {
		return;
	}

	$uploads_dir = wpcf7_upload_tmp_dir();
	wpcf7_init_uploads();

	if ( ! is_dir( $uploads_dir )
	or ! wp_is_writable( $uploads_dir ) ) {
		$message = sprintf( __( 'This contact form contains file uploading fields, but the temporary folder for the files (%s) does not exist or is not writable. You can create the folder or change its permission manually.', 'contact-form-7' ), $uploads_dir );

		echo sprintf( '<div class="notice notice-warning"><p>%s</p></div>',
			esc_html( $message ) );
	}
}


/* File uploading functions */

function wpcf7_acceptable_filetypes( $types = 'default', $format = 'regex' ) {
	if ( 'default' === $types
	or empty( $types ) ) {
		$types = array(
			'jpg',
			'jpeg',
			'png',
			'gif',
			'pdf',
			'doc',
			'docx',
			'ppt',
			'pptx',
			'odt',
			'avi',
			'ogg',
			'm4a',
			'mov',
			'mp3',
			'mp4',
			'mpg',
			'wav',
			'wmv',
		);
	} else {
		$types_tmp = (array) $types;
		$types = array();

		foreach ( $types_tmp as $val ) {
			if ( is_string( $val ) ) {
				$val = preg_split( '/[\s|,]+/', $val );
			}

			$types = array_merge( $types, (array) $val );
		}
	}

	$types = array_unique( array_filter( $types ) );

	$output = '';

	foreach ( $types as $type ) {
		$type = trim( $type, ' ,.|' );
		$type = str_replace(
			array( '.', '+', '*', '?' ),
			array( '\.', '\+', '\*', '\?' ),
			$type );

		if ( '' === $type ) {
			continue;
		}

		if ( 'attr' === $format
		or 'attribute' === $format ) {
			$output .= sprintf( '.%s', $type );
			$output .= ',';
		} else {
			$output .= $type;
			$output .= '|';
		}
	}

	return trim( $output, ' ,|' );
}

function wpcf7_init_uploads() {
	$dir = wpcf7_upload_tmp_dir();
	wp_mkdir_p( $dir );

	$htaccess_file = path_join( $dir, '.htaccess' );

	if ( file_exists( $htaccess_file ) ) {
		return;
	}

	if ( $handle = fopen( $htaccess_file, 'w' ) ) {
		fwrite( $handle, "Deny from all\n" );
		fclose( $handle );
	}
}

function wpcf7_maybe_add_random_dir( $dir ) {
	do {
		$rand_max = mt_getrandmax();
		$rand = zeroise( mt_rand( 0, $rand_max ), strlen( $rand_max ) );
		$dir_new = path_join( $dir, $rand );
	} while ( file_exists( $dir_new ) );

	if ( wp_mkdir_p( $dir_new ) ) {
		return $dir_new;
	}

	return $dir;
}

function wpcf7_upload_tmp_dir() {
	if ( defined( 'WPCF7_UPLOADS_TMP_DIR' ) ) {
		return WPCF7_UPLOADS_TMP_DIR;
	} else {
		return path_join( wpcf7_upload_dir( 'dir' ), 'wpcf7_uploads' );
	}
}

add_action( 'template_redirect', 'wpcf7_cleanup_upload_files', 20, 0 );

function wpcf7_cleanup_upload_files( $seconds = 60, $max = 100 ) {
	if ( is_admin()
	or 'GET' != $_SERVER['REQUEST_METHOD']
	or is_robots()
	or is_feed()
	or is_trackback() ) {
		return;
	}

	$dir = trailingslashit( wpcf7_upload_tmp_dir() );

	if ( ! is_dir( $dir )
	or ! is_readable( $dir )
	or ! wp_is_writable( $dir ) ) {
		return;
	}

	$seconds = absint( $seconds );
	$max = absint( $max );
	$count = 0;

	if ( $handle = opendir( $dir ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( '.' == $file
			or '..' == $file
			or '.htaccess' == $file ) {
				continue;
			}

			$mtime = @filemtime( path_join( $dir, $file ) );

			if ( $mtime and time() < $mtime + $seconds ) { // less than $seconds old
				continue;
			}

			wpcf7_rmdir_p( path_join( $dir, $file ) );
			$count += 1;

			if ( $max <= $count ) {
				break;
			}
		}

		closedir( $handle );
	}
}
