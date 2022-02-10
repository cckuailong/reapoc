<?php
/**
 * Manage My Calendar styles.
 *
 * @category Core
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate stylesheet editor
 */
function my_calendar_style_edit() {
	$edit_files = true;
	if ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT === true ) {
		$edit_files = false;
		mc_show_error( __( 'File editing is disallowed in your WordPress installation. Edit your stylesheets offline.', 'my-calendar' ) );
	}
	if ( isset( $_POST['mc_edit_style'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'my-calendar-nonce' ) ) {
			die( 'Security check failed' );
		}
		$my_calendar_style = ( isset( $_POST['style'] ) ) ? stripcslashes( $_POST['style'] ) : false;
		$mc_css_file       = stripcslashes( $_POST['mc_css_file'] );

		if ( $edit_files ) {
			$stylefile    = mc_get_style_path( $mc_css_file );
			$wrote_styles = ( false !== $my_calendar_style ) ? mc_write_styles( $stylefile, $my_calendar_style ) : 'disabled';
		} else {
			$wrote_styles = false;
		}

		if ( 'disabled' === $wrote_styles ) {
			$message = '<p>' . __( 'Styles are disabled, and were not edited.', 'my-calendar' ) . '</p>';
		} else {
			$message = ( true === $wrote_styles ) ? '<p>' . __( 'The stylesheet has been updated.', 'my-calendar' ) . '</p>' : '<p><strong>' . __( 'Write Error! Please verify write permissions on the style file.', 'my-calendar' ) . '</strong></p>';
		}

		$mc_show_css = ( empty( $_POST['mc_show_css'] ) ) ? '' : stripcslashes( $_POST['mc_show_css'] );
		update_option( 'mc_show_css', $mc_show_css );
		$use_styles = ( empty( $_POST['use_styles'] ) ) ? '' : 'true';
		update_option( 'mc_use_styles', $use_styles );

		if ( ! empty( $_POST['reset_styles'] ) ) {
			$stylefile        = mc_get_style_path();
			$styles           = mc_default_style();
			$wrote_old_styles = mc_write_styles( $stylefile, $styles );
			if ( $wrote_old_styles ) {
				$message .= '<p>' . __( 'Stylesheet reset to default.', 'my-calendar' ) . '</p>';
			}
		}

		if ( ! empty( $_POST['style_vars'] ) ) {
			$styles = get_option( 'mc_style_vars' );
			if ( isset( $_POST['new_style_var'] ) ) {
				$key = $_POST['new_style_var']['key'];
				$val = $_POST['new_style_var']['val'];
				if ( $key && $val ) {
					if ( 0 !== strpos( $key, '--' ) ) {
						$key = '--' . $key;
					}
					$styles[ $key ] = $val;
				}
			}
			foreach ( $_POST['style_vars'] as $key => $value ) {
				if ( '' !== trim( $value ) ) {
					$styles[ $key ] = $value;
				}
			}
			if ( isset( $_POST['delete_var'] ) ) {
				$delete = $_POST['delete_var'];
				unset( $styles[ $delete ] );
			}
			update_option( 'mc_style_vars', $styles );
		}

		$message .= '<p><strong>' . __( 'Style Settings Saved', 'my-calendar' ) . '.</strong></p>';
		echo "<div id='message' class='updated fade'>$message</div>";
	}
	if ( isset( $_POST['mc_choose_style'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'my-calendar-nonce' ) ) {
			die( 'Security check failed' );
		}
		$mc_css_file = stripcslashes( $_POST['mc_css_file'] );

		update_option( 'mc_css_file', $mc_css_file );
		$message = '<p><strong>' . __( 'New theme selected.', 'my-calendar' ) . '</strong></p>';
		echo "<div id='message' class='updated fade'>$message</div>";
	}

	$mc_show_css = get_option( 'mc_show_css' );
	$stylefile   = mc_get_style_path();
	if ( $stylefile ) {
		$f                 = fopen( $stylefile, 'r' );
		$size              = ( 0 === filesize( $stylefile ) ) ? 1 : filesize( $stylefile );
		$file              = fread( $f, $size );
		$my_calendar_style = $file;
		fclose( $f );
		$mc_current_style = mc_default_style();
	} else {
		$mc_current_style  = '';
		$my_calendar_style = __( 'Sorry. The file you are looking for doesn\'t appear to exist. Please check your file name and location!', 'my-calendar' );
	}
	?>
	<div class="wrap my-calendar-admin">
	<h1><?php _e( 'My Calendar Styles', 'my-calendar' ); ?></h1>
	<div class="postbox-container jcd-wide">
		<div class="metabox-holder">
			<div class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h2><?php _e( 'Calendar Style Settings', 'my-calendar' ); ?></h2>

					<div class="inside">
						<form method="post" action="<?php echo admin_url( 'admin.php?page=my-calendar-styles' ); ?>">
							<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>"/>
							<input type="hidden" value="true" name="mc_edit_style"/>
							<input type="hidden" name="mc_css_file" value="<?php echo esc_attr( get_option( 'mc_css_file' ) ); ?>"/>
							<fieldset style="position:relative;">
								<legend><?php _e( 'CSS Style Options', 'my-calendar' ); ?></legend>
								<p>
									<label for="mc_show_css"><?php _e( 'Apply CSS on these pages (comma separated IDs)', 'my-calendar' ); ?></label>
									<input type="text" id="mc_show_css" name="mc_show_css" value="<?php echo esc_attr( $mc_show_css ); ?>" />
								</p>
								<p>
									<input type="checkbox" id="reset_styles" name="reset_styles" <?php echo ( mc_is_custom_style( get_option( 'mc_css_file' ) ) ) ? "disabled='disabled'" : ''; ?> /> <label for="reset_styles"><?php _e( 'Reset to default', 'my-calendar' ); ?></label>
									<input type="checkbox" id="use_styles" name="use_styles" <?php mc_is_checked( 'mc_use_styles', 'true' ); ?> />
									<label for="use_styles"><?php _e( 'Disable My Calendar Stylesheet', 'my-calendar' ); ?></label>
								</p>
								<p>
								<?php
								if ( mc_is_custom_style( get_option( 'mc_css_file' ) ) ) {
									_e( 'The editor is not available for custom CSS files. Edit your custom CSS locally, then upload your changes.', 'my-calendar' );
								} else {
									$disabled = ( $edit_files || get_option( 'mc_use_styles' ) === 'true' ) ? '' : ' disabled="disabled"';
									?>
									<label for="style"><?php _e( 'Edit the stylesheet for My Calendar', 'my-calendar' ); ?></label><br/><textarea <?php echo $disabled; ?> class="style-editor" id="style" name="style" rows="30" cols="80"><?php echo $my_calendar_style; ?></textarea>
									<?php
								}
								?>
								</p>
								<fieldset>
									<legend><?php _e( 'CSS Variables', 'my-calendar' ); ?></legend>
								<?php
								$output = '';
								$styles = get_option( 'mc_style_vars' );
								foreach ( $styles as $var => $style ) {
									$var_id = 'mc' . sanitize_key( $var );
									if ( ! in_array( $var, array( '--primary-dark', '--primary-light', '--secondary-light', '--secondary-dark', '--highlight-dark', '--highlight-light' ), true ) ) {
										$delete = " <input type='checkbox' id='delete_var_$var_id' name='delete_var' value='" . esc_attr( $var ) . "' /><label for='delete_var_$var_id'>" . __( 'Delete', 'my-calendar' ) . '</label>';
									} else {
										$delete = '';
									}
									$output .= "<li><label for='$var_id'>" . esc_html( $var ) . "</label> <input type='text' id='$var_id' name='style_vars[$var]' value='" . esc_attr( $style ) . "' /><span aria-hidden='true' class='variable-color' style='background-color: " . esc_attr( $style ) . "'></span>$delete</li>";
								}
								if ( $output ) {
									echo "<ul class='checkboxes'>$output</ul>";
								}
								?>
									<p>
										<label for='new_style_var_key'><?php _e( 'New variable:', 'my-calendar' ); ?></label>
										<input type='text' name='new_style_var[key]' id='new_style_var_key' /> <label for='new_style_var_val'><?php _e( 'Value:', 'my-calendar' ); ?></label>
										<input type='text' name='new_style_var[val]' id='new_style_var_val' />
									</p>
								</fieldset>
								<p>
									<input type="submit" name="save" class="button-primary button-adjust" value="<?php _e( 'Save Changes', 'my-calendar' ); ?>" />
								</p>
							</fieldset>
						</form>
						<?php
						$left_string  = normalize_whitespace( $my_calendar_style );
						$right_string = normalize_whitespace( $mc_current_style );
						if ( $right_string ) { // If right string is blank, there is no default.
							if ( isset( $_GET['diff'] ) ) {
								echo '<div class="wrap my-calendar-admin" id="diff">';
								echo mc_text_diff(
									$left_string,
									$right_string,
									array(
										'title'       => __( 'Comparing Your Style with latest installed version of My Calendar', 'my-calendar' ),
										'title_right' => __( 'Latest (from plugin)', 'my-calendar' ),
										'title_left'  => __( 'Current (in use)', 'my-calendar' ),
									)
								);
								echo '</div>';
							} elseif ( trim( $left_string ) !== trim( $right_string ) ) {
								echo '<div class="wrap my-calendar-admin">';
								mc_show_notice( __( 'There have been updates to the stylesheet.', 'my-calendar' ) . ' <a href="' . admin_url( 'admin.php?page=my-calendar-styles&amp;diff#diff' ) . '">' . __( 'Compare Your Stylesheet with latest installed version of My Calendar.', 'my-calendar' ) . '</a>' );
								echo '</div>';
							} else {
								echo '
						<div class="wrap my-calendar-admin">
							<p>' . __( 'Your stylesheet matches that included with My Calendar.', 'my-calendar' ) . '</p>
						</div>';
							}
						}
						?>
					</div>
				</div>
				<p><?php _e( 'Resetting your stylesheet will set your stylesheet to the version currently distributed with the plug-in.', 'my-calendar' ); ?></p>
			</div>
		</div>
	</div>
	<?php
		$selector = mc_stylesheet_selector();
		mc_show_sidebar( '', $selector );
	?>
	</div>
	<?php
}

/**
 * Display stylesheet selector as added component in sidebar.
 *
 * @return string
 */
function mc_stylesheet_selector() {
	$dir              = plugin_dir_path( __FILE__ );
	$options          = '';
	$return           = '
	<form method="post" action="' . admin_url( 'admin.php?page=my-calendar-styles' ) . '">
		<div><input type="hidden" name="_wpnonce" value="' . wp_create_nonce( 'my-calendar-nonce' ) . '"/></div>
		<div><input type="hidden" value="true" name="mc_choose_style"/></div>';
	$custom_directory = str_replace( '/my-calendar/', '', $dir ) . '/my-calendar-custom/styles/';
	$directory        = dirname( __FILE__ ) . '/styles/';
	$files            = mc_css_list( $custom_directory );
	if ( ! empty( $files ) ) {
		$options .= '<optgroup label="' . __( 'Your Custom Stylesheets', 'my-calendar' ) . '">';
		foreach ( $files as $value ) {
			$test     = 'mc_custom_' . $value;
			$filepath = mc_get_style_path( $test );
			$path     = pathinfo( $filepath );
			if ( 'css' === $path['extension'] ) {
				$selected = ( get_option( 'mc_css_file' ) === $test ) ? ' selected="selected"' : '';
				$options .= "<option value='mc_custom_$value'$selected>$value</option>\n";
			}
		}
		$options .= '</optgroup>';
	}
	$files    = mc_css_list( $directory );
	$options .= '<optgroup label="' . __( 'Installed Stylesheets', 'my-calendar' ) . '">';
	foreach ( $files as $value ) {
		$filepath = mc_get_style_path( $value );
		$path     = pathinfo( $filepath );
		if ( 'css' === $path['extension'] ) {
			$selected = ( get_option( 'mc_css_file' ) === $value ) ? ' selected="selected"' : '';
			$options .= "<option value='$value'$selected>$value</option>\n";
		}
	}
	$options .= '</optgroup>';
	$return  .= '
		<fieldset>
			<p>
				<label for="mc_css_file">' . __( 'Select My Calendar Theme', 'my-calendar' ) . '</label>
				<select name="mc_css_file" id="mc_css_file">' . $options . '</select>
			</p>
			<p>
				<input type="submit" name="save" class="button-primary" value="' . __( 'Choose Style', 'my-calendar' ) . '"/>
			</p>
		</fieldset>
	</form>';

	return array( __( 'Select Stylesheet', 'my-calendar' ) => $return );
}

/**
 * Get path for given filename or current selected stylesheet.
 *
 * @param string $filename File name.
 * @param string $type path or url.
 *
 * @return mixed string/boolean
 */
function mc_get_style_path( $filename = false, $type = 'path' ) {
	$url = plugin_dir_url( __FILE__ );
	$dir = plugin_dir_path( __FILE__ );
	if ( ! $filename ) {
		$filename = get_option( 'mc_css_file' );
	}
	if ( ! $filename ) {
		// If no value is saved, return default.
		$filename = 'twentytwenty.css';
	}
	if ( 0 === strpos( $filename, 'mc_custom_' ) ) {
		$filename  = str_replace( 'mc_custom_', '', $filename );
		$stylefile = ( 'path' === $type ) ? str_replace( '/my-calendar/', '', $dir ) . '/my-calendar-custom/styles/' . $filename : str_replace( '/my-calendar/', '', $url ) . '/my-calendar-custom/styles/' . $filename;
	} else {
		$stylefile = ( 'path' === $type ) ? dirname( __FILE__ ) . '/styles/' . $filename : plugins_url( 'styles', __FILE__ ) . '/' . $filename;
	}
	if ( 'path' === $type ) {
		if ( is_file( $stylefile ) ) {
			return $stylefile;
		} else {
			return false;
		}
	} else {
		return $stylefile;
	}
}

/**
 * Identify whether a given file is a custom style or a core style
 *
 * @param string $filename File name..
 *
 * @return boolean
 */
function mc_is_custom_style( $filename ) {
	if ( 0 === strpos( $filename, 'mc_custom_' ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Fetch the styles for the current selected style
 *
 * @param string $filename File name.
 * @param string $return content or filename.
 *
 * @return string
 */
function mc_default_style( $filename = false, $return = 'content' ) {
	if ( ! $filename ) {
		$mc_css_file = get_option( 'mc_css_file' );
	} else {
		$mc_css_file = $filename;
	}
	$mc_current_file = dirname( __FILE__ ) . '/templates/' . $mc_css_file;
	if ( file_exists( $mc_current_file ) ) {
		$f                = fopen( $mc_current_file, 'r' );
		$file             = fread( $f, filesize( $mc_current_file ) );
		$mc_current_style = $file;
		fclose( $f );
		switch ( $return ) {
			case 'content':
				return $mc_current_style;
				break;
			case 'path':
				return $mc_current_file;
				break;
			case 'both':
				return array( $mc_current_file, $mc_current_style );
				break;
		}
	}

	return '';
}

/**
 * List CSS files in a directory
 *
 * @param string $directory File directory.
 *
 * @return array list of CSS files
 */
function mc_css_list( $directory ) {
	if ( ! file_exists( $directory ) ) {
		return array();
	}
	$results = array();
	$handler = opendir( $directory );
	// Keep going until all files in directory have been read.
	while ( false !== ( $file = readdir( $handler ) ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
		// If $file isn't this directory or parent, add it to the results array.
		if ( '.' !== $file && '..' !== $file ) {
			$results[] = $file;
		}
	}
	closedir( $handler );
	sort( $results, SORT_STRING );

	return $results;
}

/**
 * Write updated styles to file
 *
 * @param string $file File to write to.
 * @param string $style New styles to write.
 *
 * @return boolean;
 */
function mc_write_styles( $file, $style ) {
	if ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT === true ) {
		return false;
	}

	$standard = dirname( __FILE__ ) . '/styles/';
	$files    = mc_css_list( $standard );
	foreach ( $files as $f ) {
		$filepath = mc_get_style_path( $f );
		$path     = pathinfo( $filepath );
		if ( 'css' === $path['extension'] ) {
			$styles_whitelist[] = $filepath;
		}
	}

	if ( in_array( $file, $styles_whitelist, true ) ) {
		if ( function_exists( 'wp_is_writable' ) ) {
			$is_writable = wp_is_writable( $file );
		} else {
			$is_writable = is_writeable( $file );
		}
		if ( $is_writable ) {
			$f = fopen( $file, 'w+' );
			fwrite( $f, $style ); // number of bytes to write, max.
			fclose( $f );

			return true;
		} else {
			return false;
		}
	}
	return false;
}

/**
 * Check diff between current styles and shipped styles
 *
 * @param string           $left_string Currently installed.
 * @param string           $right_string Shipped.
 * @param mixed array/null $args Function table rendered arguments.
 *
 * @return string
 */
function mc_text_diff( $left_string, $right_string, $args = null ) {
	$defaults = array(
		'title'       => '',
		'title_left'  => '',
		'title_right' => '',
	);
	$args     = wp_parse_args( $args, $defaults );

	if ( ! class_exists( 'WP_Text_Diff_Renderer_Table' ) ) {
		require( ABSPATH . WPINC . '/wp-diff.php' );
	}
	$left_string  = normalize_whitespace( $left_string );
	$right_string = normalize_whitespace( $right_string );

	$left_lines  = explode( "\n", $left_string );
	$right_lines = explode( "\n", $right_string );
	$text_diff   = new Text_Diff( $left_lines, $right_lines );
	$renderer    = new WP_Text_Diff_Renderer_Table( $args );
	$diff        = $renderer->render( $text_diff );
	$r           = '';

	if ( ! $diff ) {
		return '';
	}
	if ( $args['title'] ) {
		$r .= "<h2>$args[title]</h2>\n";
	}

	$r .= "<table class='diff'>\n";
	$r .= "<col class='content diffsplit left' /><col class='content diffsplit middle' /><col class='content diffsplit right' />";

	if ( $args['title'] || $args['title_left'] || $args['title_right'] ) {
		$r .= '<thead>';
	}

	if ( $args['title_left'] || $args['title_right'] ) {
		$r .= "<tr class='diff-sub-title'>\n";
		$r .= "\t<th scope='col'>$args[title_left]</th><td></td>\n";
		$r .= "\t<th scope='col'>$args[title_right]</th>\n";
		$r .= "</tr>\n";
	}
	if ( $args['title'] || $args['title_left'] || $args['title_right'] ) {
		$r .= "</thead>\n";
	}

	$r .= "<tbody>\n$diff\n</tbody>\n";
	$r .= '</table>';

	return $r;
}

add_action(
	'admin_enqueue_scripts',
	function() {
		if ( ! function_exists( 'wp_enqueue_code_editor' ) ) {
			return;
		}

		if ( sanitize_title( __( 'My Calendar', 'my-calendar' ) ) . '_page_my-calendar-styles' !== get_current_screen()->id ) {
			return;
		}

		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

		// Bail if user disabled CodeMirror.
		if ( false === $settings ) {
			return;
		}

		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "style", %s ); } );',
				wp_json_encode( $settings )
			)
		);
	}
);
