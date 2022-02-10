<?php
/**
 * Manage event categories.
 *
 * @category Events
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update a single field of a category
 *
 * @param string $field field name.
 * @param mixed  $data Data to change.
 * @param int    $category Category ID.
 *
 * @return result
 */
function mc_update_category( $field, $data, $category ) {
	global $wpdb;
	$field  = sanitize_key( $field );
	$result = $wpdb->query( $wpdb->prepare( 'UPDATE ' . my_calendar_categories_table() . " SET $field = %d WHERE category_id=%d", $data, $category ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared

	return $result;
}

/**
 * List image files in a directory.
 *
 * @param string $directory Path to directory.
 *
 * @return array images in directory.
 */
function mc_directory_list( $directory ) {
	if ( ! file_exists( $directory ) ) {
		return array();
	}
	$results = array();
	$handler = opendir( $directory );
	// keep going until all files in directory have been read.
	while ( false !== ( $file = readdir( $handler ) ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
		// if $file isn't this directory or its parent add it to the results array.
		if ( filesize( $directory . '/' . $file ) > 11 ) {
			if ( '.' !== $file && '..' !== $file && ! is_dir( $directory . '/' . $file ) && (
					exif_imagetype( $directory . '/' . $file ) === IMAGETYPE_GIF ||
					exif_imagetype( $directory . '/' . $file ) === IMAGETYPE_PNG ||
					exif_imagetype( $directory . '/' . $file ) === IMAGETYPE_JPEG )
			) {
				$results[] = $file;
			}
		}
	}
	closedir( $handler );
	sort( $results, SORT_STRING );

	return $results;
}

/**
 * Return SQL to select only categories *not* marked as private
 *
 * @return string partial SQL statement
 */
function mc_private_categories() {
	$cats = '';
	if ( ! is_user_logged_in() ) {
		$categories = mc_get_private_categories();
		$cats       = implode( ',', $categories );
		if ( '' !== $cats ) {
			$cats = " AND c.category_id NOT IN ($cats)";
		}
	}

	return $cats;
}

/**
 * Fetch array of private categories.
 *
 * @uses filter mc_private_categories
 *
 * @return array private categories
 */
function mc_get_private_categories() {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$table      = my_calendar_categories_table();
	$query      = 'SELECT category_id FROM `' . $table . '` WHERE category_private = 1';
	$results    = $mcdb->get_results( $query );
	$categories = array();
	foreach ( $results as $result ) {
		$categories[] = $result->category_id;
	}

	return apply_filters( 'mc_private_categories', $categories );
}

/**
 * Check whether a given icon is a custom or stock icon
 *
 * @return boolean
 */
function mc_is_custom_icon() {
	$dir  = plugin_dir_path( __FILE__ );
	$base = basename( $dir );
	if ( file_exists( str_replace( $base, '', $dir ) . 'my-calendar-custom' ) ) {
		$results = mc_directory_list( str_replace( $base, '', $dir ) . 'my-calendar-custom' );
		if ( empty( $results ) ) {
			return false;
		} else {
			return true;
		}
	}

	return false;
}

/**
 * Generate form to manage categories
 */
function my_calendar_manage_categories() {
	global $wpdb;
	?>
	<div class="wrap my-calendar-admin">
		<?php
		my_calendar_check_db();
		$append = '';
		// We do some checking to see what we're doing.
		if ( ! empty( $_POST ) ) {
			$nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $nonce, 'my-calendar-nonce' ) ) {
				die( 'Security check failed' );
			}
		}

		if ( isset( $_POST['mode'] ) && 'add' === $_POST['mode'] ) {
			$cat_id = mc_create_category( $_POST );

			if ( isset( $_POST['mc_default_category'] ) ) {
				update_option( 'mc_default_category', $cat_id );
				$append .= __( 'Default category changed.', 'my-calendar' );
			}

			if ( isset( $_POST['mc_skip_holidays_category'] ) ) {
				update_option( 'mc_skip_holidays_category', $cat_id );
				$append .= __( 'Holiday category changed.', 'my-calendar' );
			}

			if ( $cat_id ) {
				mc_show_notice( __( 'Category added successfully', 'my-calendar' ) . ". $append" );
			} else {
				mc_show_error( __( 'Category addition failed.', 'my-calendar' ) );
			}
		} elseif ( isset( $_GET['mode'] ) && isset( $_GET['category_id'] ) && 'delete' === $_GET['mode'] ) {
			$cat_id  = (int) $_GET['category_id'];
			$results = $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . my_calendar_categories_table() . ' WHERE category_id=%d', $cat_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			// Also delete relationships for this category.
			$rel_results = $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . my_calendar_category_relationships_table() . ' WHERE category_id = %d', $cat_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			if ( $results ) {
				$default_category = get_option( 'mc_default_category' );
				$default_category = ( is_numeric( $default_category ) ) ? absint( $default_category ) : 1;
				$cal_results      = $wpdb->query( $wpdb->prepare( 'UPDATE `' . my_calendar_table() . '` SET event_category=%d WHERE event_category=%d', $default_category, $cat_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			} else {
				$cal_results = false;
			}
			if ( get_option( 'mc_default_category' ) === (string) $cat_id ) {
				update_option( 'mc_default_category', 1 );
			}
			if ( $results && ( $cal_results || $rel_results ) ) {
				mc_show_notice( __( 'Category deleted successfully. Categories in calendar updated.', 'my-calendar' ) );
			} elseif ( $results && ! $cal_results ) {
				mc_show_notice( __( 'Category deleted successfully. Category was not in use; categories in calendar not updated.', 'my-calendar' ) );
			} elseif ( ! $results && $cal_results ) {
				mc_show_error( __( 'Category not deleted. Categories in calendar updated.', 'my-calendar' ) );
			}
		} elseif ( isset( $_GET['mode'] ) && isset( $_GET['category_id'] ) && 'edit' === $_GET['mode'] && ! isset( $_POST['mode'] ) ) {
			$cur_cat = (int) $_GET['category_id'];
			mc_edit_category_form( 'edit', $cur_cat );
		} elseif ( isset( $_POST['mode'] ) && isset( $_POST['category_id'] ) && isset( $_POST['category_name'] ) && isset( $_POST['category_color'] ) && 'edit' === $_POST['mode'] ) {
			$append = '';
			if ( isset( $_POST['mc_default_category'] ) ) {
				update_option( 'mc_default_category', (int) $_POST['category_id'] );
				$append .= __( 'Default category changed.', 'my-calendar' );
			} else {
				if ( get_option( 'mc_default_category' ) === (string) $_POST['category_id'] ) {
					delete_option( 'mc_default_category' );
				}
			}
			if ( isset( $_POST['mc_skip_holidays_category'] ) ) {
				update_option( 'mc_skip_holidays_category', (int) $_POST['category_id'] );
				$append .= __( 'Holiday category changed.', 'my-calendar' );
			} else {
				if ( get_option( 'mc_skip_holidays_category' ) === (string) $_POST['category_id'] ) {
					delete_option( 'mc_skip_holidays_category' );
				}
			}

			$update  = array(
				'category_name'    => $_POST['category_name'],
				'category_color'   => $_POST['category_color'],
				'category_icon'    => $_POST['category_icon'],
				'category_private' => ( ( isset( $_POST['category_private'] ) ) ? 1 : 0 ),
			);
			$results = mc_update_cat( $update );
			if ( $results ) {
				mc_show_notice( __( 'Category edited successfully.', 'my-calendar' ) . " $append" );
			} else {
				mc_show_error( __( 'Category was not changed.', 'my-calendar' ) . " $append" );
			}
			$cur_cat = (int) $_POST['category_id'];
			mc_edit_category_form( 'edit', $cur_cat );
		}

		if ( isset( $_GET['mode'] ) && 'edit' !== $_GET['mode'] || isset( $_POST['mode'] ) && 'edit' !== $_POST['mode'] || ! isset( $_GET['mode'] ) && ! isset( $_POST['mode'] ) ) {
			mc_edit_category_form( 'add' );
		}
		?>
		</div>
	<?php
}

/**
 * Update a category.
 *
 * @param array $category Array of params to update.
 *
 * @return mixed boolean/int query result
 */
function mc_update_cat( $category ) {
	global $wpdb;
	$formats     = array( '%s', '%s', '%s', '%d', '%d', '%d' );
	$where       = array(
		'category_id' => (int) $_POST['category_id'],
	);
	$cat_name    = strip_tags( $category['category_name'] );
	$term_exists = term_exists( $cat_name, 'mc-event-category' );
	if ( ! $term_exists ) {
		$term = wp_insert_term( $cat_name, 'mc-event-category' );
		if ( ! is_wp_error( $term ) ) {
			$term = $term['term_id'];
		} else {
			$term = false;
		}
	} else {
		$term = get_term_by( 'name', $cat_name, 'mc-event-category' );
		$term = $term->term_id;
	}
	$category['category_term'] = $term;

	$result = $wpdb->update( my_calendar_categories_table(), $category, $where, $formats, '%d' );

	return $result;
}

/**
 * Create a category.
 *
 * @param array $category Array of params to update.
 *
 * @return mixed boolean/int query result
 */
function mc_create_category( $category ) {
	global $wpdb;

	$formats     = array( '%s', '%s', '%s', '%d', '%d' );
	$cat_name    = strip_tags( $category['category_name'] );
	$term_exists = term_exists( $cat_name, 'mc-event-category' );
	if ( ! $term_exists ) {
		$term = wp_insert_term( $cat_name, 'mc-event-category' );
		if ( ! is_wp_error( $term ) ) {
			$term = $term['term_id'];
		} else {
			$term = false;
		}
	} else {
		$term = get_term_by( 'name', $cat_name, 'mc-event-category' );
		$term = $term->term_id;
	}
	$add = array(
		'category_name'    => $category['category_name'],
		'category_color'   => $category['category_color'],
		'category_icon'    => $category['category_icon'],
		'category_private' => ( ( isset( $category['category_private'] ) && ( 'on' === $category['category_private'] || '1' === (string) $category['category_private'] ) ) ? 1 : 0 ),
		'category_term'    => $term,
	);

	$add     = array_map( 'mc_kses_post', $add );
	$add     = apply_filters( 'mc_pre_add_category', $add, $category );
	$results = $wpdb->insert( my_calendar_categories_table(), $add, $formats );
	$cat_id  = $wpdb->insert_id;
	do_action( 'mc_post_add_category', $add, $cat_id, $category );

	return $cat_id;
}

/**
 * Form to edit a category
 *
 * @param string $view Edit or create.
 * @param id     $cat_id Category ID.
 */
function mc_edit_category_form( $view = 'edit', $cat_id = '' ) {
	global $wpdb;
	$dir     = plugin_dir_path( __FILE__ );
	$url     = plugin_dir_url( __FILE__ );
	$cur_cat = false;
	if ( $cat_id ) {
		$cat_id  = (int) $cat_id;
		$cur_cat = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . my_calendar_categories_table() . ' WHERE category_id=%d', $cat_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	} else {
		// If no category ID, change view.
		$view = 'add';
	}
	if ( mc_is_custom_icon() ) {
		$directory = str_replace( '/my-calendar', '', $dir ) . '/my-calendar-custom/';
		$path      = '/my-calendar-custom';
		$iconlist  = mc_directory_list( $directory );
	} else {
		$directory = dirname( __FILE__ ) . '/images/icons/';
		$path      = '/' . dirname( plugin_basename( __FILE__ ) ) . '/images/icons';
		$iconlist  = mc_directory_list( $directory );
	}
	if ( 'add' === $view ) {
		?>
		<h1><?php _e( 'Add Category', 'my-calendar' ); ?></h1>
		<?php
	} else {
		?>
		<h1 class="wp-heading-inline"><?php _e( 'Edit Category', 'my-calendar' ); ?></h1>
		<a href="<?php echo admin_url( 'admin.php?page=my-calendar-categories' ); ?>" class="page-title-action"><?php _e( 'Add New', 'my-calendar' ); ?></a>
		<hr class="wp-header-end">
		<?php
	}
	?>
	<div class="postbox-container jcd-wide">
		<div class="metabox-holder">

			<div class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h2><?php _e( 'Category Editor', 'my-calendar' ); ?></h2>

					<div class="inside">
						<form id="my-calendar" method="post" action="<?php echo admin_url( 'admin.php?page=my-calendar-categories' ); ?>">
							<div><input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>"/></div>
							<?php
							if ( 'add' === $view ) {
								?>
								<div>
									<input type="hidden" name="mode" value="add"/>
									<input type="hidden" name="category_id" value=""/>
								</div>
								<?php
							} else {
								?>
								<div>
									<input type="hidden" name="mode" value="edit"/>
									<input type="hidden" name="category_id" value="<?php echo ( is_object( $cur_cat ) ) ? absint( $cur_cat->category_id ) : ''; ?>" />
								</div>
								<?php
							}
							if ( ! empty( $cur_cat ) && is_object( $cur_cat ) ) {
								$color  = ( strpos( $cur_cat->category_color, '#' ) !== 0 ) ? '#' : '';
								$color .= $cur_cat->category_color;
							} else {
								$color = '';
							}
							$color = strip_tags( $color );
							if ( ! empty( $cur_cat ) && is_object( $cur_cat ) ) {
								$cat_name = stripslashes( $cur_cat->category_name );
							} else {
								$cat_name = '';
							}
							?>
							<ul>
							<li>
								<label for="cat_name"><?php _e( 'Category Name', 'my-calendar' ); ?></label>
								<input type="text" id="cat_name" name="category_name" class="input" size="30" value="<?php echo esc_attr( $cat_name ); ?>"/>
								<label for="cat_color"><?php _e( 'Color', 'my-calendar' ); ?></label>
								<input type="text" id="cat_color" name="category_color" class="mc-color-input" size="10" maxlength="7" value="<?php echo ( '#' !== $color ) ? esc_attr( $color ) : ''; ?>"/>
							</li>
							<?php
							if ( 'true' === get_option( 'mc_hide_icons' ) ) {
								echo "<input type='hidden' name='category_icon' value='' />";
							} else {
								?>
							<li>
							<label for="cat_icon"><?php _e( 'Category Icon', 'my-calendar' ); ?></label>
							<select name="category_icon" id="cat_icon">
								<option value=''><?php _e( 'None', 'my-calendar' ); ?></option>
								<?php
								foreach ( $iconlist as $value ) {
									$selected = ( ( ! empty( $cur_cat ) && is_object( $cur_cat ) ) && $cur_cat->category_icon === $value ) ? ' selected="selected"' : '';
									echo "<option value='" . esc_attr( $value ) . "'$selected style='background: url(" . esc_url( str_replace( 'my-calendar/', '', $url ) . "$path/$value" ) . ") left 50% no-repeat;'>$value</option>";
								}
								?>
							</select>
							</li>
								<?php
							}
							?>
							<li>
								<?php
								if ( 'add' === $view ) {
									$private_checked = '';
								} else {
									if ( ! empty( $cur_cat ) && is_object( $cur_cat ) && '1' === (string) $cur_cat->category_private ) {
										$private_checked = ' checked="checked"';
									} else {
										$private_checked = '';
									}
								}
								$checked         = ( 'add' === $view ) ? '' : checked( get_option( 'mc_default_category' ), $cur_cat->category_id, false );
								$holiday_checked = ( 'add' === $view ) ? '' : checked( get_option( 'mc_skip_holidays_category' ), $cur_cat->category_id, false );
								?>
								<ul class='checkboxes'>
								<li>
									<input type="checkbox" value="on" name="category_private" id="cat_private"<?php echo $private_checked; ?> /> <label for="cat_private"><?php _e( 'Private category (logged-in users only)', 'my-calendar' ); ?></label>
								</li>
								<li>
									<input type="checkbox" value="on" name="mc_default_category" id="mc_default_category"<?php echo $checked; ?> /> <label for="mc_default_category"><?php _e( 'Default category', 'my-calendar' ); ?></label>
								</li>
								<li>
									<input type="checkbox" value="on" name="mc_skip_holidays_category" id="mc_shc"<?php echo $holiday_checked; ?> /> <label for="mc_shc"><?php _e( 'Holiday Category', 'my-calendar' ); ?></label>
								</li>
								</ul>
							</li>
							<?php echo apply_filters( 'mc_category_fields', '', $cur_cat ); ?>
						</ul>
						<?php
						if ( 'add' === $view ) {
							$save_text = __( 'Add Category', 'my-calendar' );
						} else {
							$save_text = __( 'Save Changes', 'my-calendar' );
						}
						?>
							<p>
								<input type="submit" name="save" class="button-primary" value="<?php echo esc_attr( $save_text ); ?> &raquo;"/>
							</p>
							<?php do_action( 'mc_post_category_form', $cur_cat, $view ); ?>
						</form>
					</div>
				</div>
			</div>
			<?php if ( 'edit' === $view ) { ?>
				<p>
					<a href="<?php echo admin_url( 'admin.php?page=my-calendar-categories' ); ?>"><?php _e( 'Add a New Category', 'my-calendar' ); ?> &raquo;</a>
				</p>
			<?php } ?>
			<div class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h2><?php _e( 'Category List', 'my-calendar' ); ?></h2>

					<div class="inside">
						<?php mc_manage_categories(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
		$category_settings = mc_category_settings();
		mc_show_sidebar( '', $category_settings );
}

/**
 * Update category settings.
 *
 * @return Update message.
 */
function mc_category_settings_update() {
	$message = '';
	$nonce   = ( isset( $_POST['_wpnonce'] ) ) ? $_POST['_wpnonce'] : false;
	if ( isset( $_POST['mc_category_settings'] ) && wp_verify_nonce( $nonce, 'my-calendar-nonce' ) ) {
		update_option( 'mc_hide_icons', ( ! empty( $_POST['mc_hide_icons'] ) && 'on' === $_POST['mc_hide_icons'] ) ? 'true' : 'false' );
		update_option( 'mc_apply_color', $_POST['mc_apply_color'] );
		update_option( 'mc_multiple_categories', ( ! empty( $_POST['mc_multiple_categories'] ) && 'on' === $_POST['mc_multiple_categories'] ) ? 'true' : 'false' );

		$message = mc_show_notice( __( 'My Calendar Category Configuration Updated', 'my-calendar' ), false );
	}

	return $message;
}

/**
 * Generate category settings form.
 *
 * @return string HTML form.
 */
function mc_category_settings() {
	if ( current_user_can( 'mc_edit_settings' ) ) {
		$response = mc_category_settings_update();
		$settings = $response . '
		<form method="post" action="' . admin_url( 'admin.php?page=my-calendar-categories' ) . '">
		<div>
			<input type="hidden" name="_wpnonce" value="' . wp_create_nonce( 'my-calendar-nonce' ) . '" />
		</div>

			<fieldset>
			<legend>' . __( 'Category Colors', 'my-calendar' ) . '</legend>
				<ul>' .
				mc_settings_field(
					'mc_apply_color',
					array(
						'default'    => __( 'Ignore colors', 'my-calendar' ),
						'font'       => __( 'Titles are in colors.', 'my-calendar' ),
						'background' => __( 'Titles use colors as background.', 'my-calendar' ),
					),
					'default',
					'',
					array(),
					'radio',
					false
				) . '
				</ul>
			</fieldset>
			<ul>
				<li>' . mc_settings_field( 'mc_hide_icons', __( 'Hide Category icons', 'my-calendar' ), '', '', array(), 'checkbox-single', false ) . '</li>
				<li>' . mc_settings_field( 'mc_multiple_categories', __( 'Use multiple categories on events', 'my-calendar' ), '', '', array(), 'checkbox-single', false ) . '</li>
			</ul>
			<p>
				<input type="submit" name="mc_category_settings" class="button-primary" value="' . __( 'Save Settings', 'my-calendar' ) . '" />
			</p>
		</form>';

		return array( __( 'Category Settings', 'my-calendar' ) => $settings );
	}
}

/**
 * Get single field about a category.
 *
 * @param int    $cat_id Category ID.
 * @param string $field Field name to get.
 *
 * @return mixed string/int Query result.
 */
function mc_get_category_detail( $cat_id, $field = 'category_name' ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}

	$category = $mcdb->get_row( $mcdb->prepare( 'SELECT * FROM ' . my_calendar_categories_table() . ' WHERE category_id=%d', $cat_id ) );

	if ( $category ) {
		if ( ! $field ) {
			return $category;
		}

		return (string) $category->$field;
	}
}

/**
 * Fetch the category ID for categories passed by name
 *
 * @param string $string Name of category.
 *
 * @return int $cat_id or false.
 */
function mc_category_by_name( $string ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$cat_id = false;
	$sql    = 'SELECT * FROM ' . my_calendar_categories_table() . ' WHERE category_name = %s';
	$cat    = $mcdb->get_row( $mcdb->prepare( $sql, $string ) );

	if ( is_object( $cat ) ) {
		$cat_id = $cat->category_id;
	}

	return $cat_id;
}

/**
 * Get or create a category if no default set.
 *
 * @param bool $single False for all categories; true for individual category.
 *
 * @return int
 */
function mc_no_category_default( $single = false ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}

	$cats   = $mcdb->get_results( 'SELECT * FROM ' . my_calendar_categories_table() . ' ORDER BY category_name ASC' );
	$cat_id = $cats[0]->category_id;
	if ( empty( $cats ) ) {
		// need to have categories. Try to create again.
		$cat_id = mc_create_category(
			array(
				'category_name'  => 'General',
				'category_color' => '#ffffcc',
				'category_icon'  => 'event.png',
			)
		);

		$cats = $mcdb->get_results( 'SELECT * FROM ' . my_calendar_categories_table() . ' ORDER BY category_name ASC' );
	}
	if ( $single ) {
		return $cat_id;
	}

	return $cats;
}

/**
 * Fetch category object by ID or name.
 *
 * @param int|string $category Category name/id.
 *
 * @return object
 */
function mc_get_category( $category ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	if ( is_int( $category ) ) {
		$sql = 'SELECT * FROM ' . my_calendar_categories_table() . ' WHERE category_id = %d';
		$cat = $mcdb->get_row( $mcdb->prepare( $sql, $category ) );
	} else {
		$cat = mc_category_by_name( $category );
	}

	return $cat;
}

/**
 * Generate list of categories to edit.
 */
function mc_manage_categories() {
	global $wpdb;
	?>
	<h1>
	<?php
	_e( 'Manage Categories', 'my-calendar' );
	?>
	</h1>
	<?php
	$co = ( ! isset( $_GET['co'] ) ) ? '1' : (int) $_GET['co'];
	switch ( $co ) {
		case 1:
			$cat_order = 'category_id';
			break;
		case 2:
			$cat_order = 'category_name';
			break;
		default:
			$cat_order = 'category_id';
	}
	// We pull the categories from the database.
	$categories = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . my_calendar_categories_table() . ' ORDER BY %s ASC', $cat_order ) );  // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	if ( ! empty( $categories ) ) {
		?>
		<table class="widefat page fixed mc-categories" id="my-calendar-admin-table">
		<thead>
		<tr>
			<th scope="col">
				<?php
				echo ( '2' === $co ) ? '<a href="' . admin_url( 'admin.php?page=my-calendar-categories&amp;co=1' ) . '">' : '';
				_e( 'ID', 'my-calendar' );
				echo ( '2' === $co ) ? '</a>' : '';
				?>
			</th>
			<th scope="col">
				<?php
					echo ( '1' === $co ) ? '<a href="' . admin_url( 'admin.php?page=my-calendar-categories&amp;co=2' ) . '">' : '';
					_e( 'Category Name', 'my-calendar' );
					echo ( '1' === $co ) ? '</a>' : '';
				?>
			</th>
			<th scope="col"><?php _e( 'Color/Icon', 'my-calendar' ); ?></th>
			<th scope="col"><?php _e( 'Private', 'my-calendar' ); ?></th>
			<th scope="col"><?php _e( 'Edit', 'my-calendar' ); ?></th>
			<th scope="col"><?php _e( 'Delete', 'my-calendar' ); ?></th>
		</tr>
		</thead>
		<?php
		$class = '';
		foreach ( $categories as $cat ) {
			$class = ( 'alternate' === $class ) ? '' : 'alternate';
			if ( ! $cat->category_icon && 'true' !== get_option( 'mc_hide_icons' ) ) {
				$icon_src = ( mc_file_exists( $cat->category_icon ) ) ? mc_get_file( $cat->category_icon, 'url' ) : plugins_url( 'my-calendar/images/icons/' . $cat->category_icon );
			} else {
				$icon_src = false;
			}
			$background = ( 0 !== strpos( $cat->category_color, '#' ) ) ? '#' : '' . $cat->category_color;
			$foreground = mc_inverse_color( $background );
			$cat_name   = stripslashes( strip_tags( $cat->category_name, mc_strip_tags() ) );
			?>
		<tr class="<?php echo $class; ?>">
			<th scope="row"><?php echo $cat->category_id; ?></th>
			<td>
			<?php
			echo $cat_name;
			if ( get_option( 'mc_default_category' ) === (string) $cat->category_id ) {
				echo ' <strong>' . __( '(Default)' ) . '</strong>';
			}
			if ( get_option( 'mc_skip_holidays_category' ) === (string) $cat->category_id ) {
				echo ' <strong>' . __( '(Holiday)' ) . '</strong>';
			}
			?>
			</td>
			<td style="background-color:<?php echo $background; ?>;color: <?php echo $foreground; ?>"><?php echo ( $icon_src ) ? "<img src='$icon_src' alt='' />" : ''; ?> <?php echo ( '#' !== $background ) ? $background : ''; ?></td>
			<td><?php echo ( '1' === (string) $cat->category_private ) ? __( 'Yes', 'my-calendar' ) : __( 'No', 'my-calendar' ); ?></td>
			<td>
				<a href="<?php echo admin_url( "admin.php?page=my-calendar-categories&amp;mode=edit&amp;category_id=$cat->category_id" ); ?>"
				class='edit'>
				<?php
				// Translators: Name of category being edited.
				printf( __( 'Edit %s', 'my-calendar' ), '<span class="screen-reader-text">' . $cat_name . '</span>' );
				?>
				</a>
			</td>
			<?php
			if ( '1' === (string) $cat->category_id ) {
				echo '<td>' . __( 'N/A', 'my-calendar' ) . '</td>';
			} else {
				?>
			<td>
				<a href="<?php echo admin_url( "admin.php?page=my-calendar-categories&amp;mode=delete&amp;category_id=$cat->category_id" ); ?>" class="delete" onclick="return confirm('<?php _e( 'Are you sure you want to delete this category?', 'my-calendar' ); ?>')">
				<?php
				// Translators: Category name.
				printf( __( 'Delete %s', 'my-calendar' ), '<span class="screen-reader-text">' . $cat_name . '</span>' );
				?>
				</a>
			</td>
				<?php
			}
			?>
		</tr>
			<?php
		}
		?>
	</table>
		<?php
	} else {
		echo '<p>' . __( 'There are no categories in the database - or something has gone wrong!', 'my-calendar' ) . '</p>';
	}
}

add_action( 'show_user_profile', 'mc_profile' );
add_action( 'edit_user_profile', 'mc_profile' );
add_action( 'profile_update', 'mc_save_profile' );
/**
 * Show user profile data on Edit User pages.
 */
function mc_profile() {
	global $user_ID;
	$current_user = wp_get_current_user();
	$user_edit    = ( isset( $_GET['user_id'] ) ) ? (int) $_GET['user_id'] : $user_ID;

	if ( user_can( $user_edit, 'mc_manage_events' ) && current_user_can( 'manage_options' ) ) {
		$permissions = get_user_meta( $user_edit, 'mc_user_permissions', true );
		$selected    = ( empty( $permissions ) || in_array( 'all', $permissions, true ) || user_can( $user_edit, 'manage_options' ) ) ? ' checked="checked"' : '';
		?>
		<h3><?php _e( 'My Calendar Editor Permissions', 'my-calendar' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="mc_user_permissions"><?php _e( 'Allowed Categories', 'my-calendar' ); ?></label>
				</th>
				<td>
					<ul class='checkboxes'>
						<li><input type="checkbox" name="mc_user_permissions[]" value="all" id="mc_edit_all" <?php echo $selected; ?>> <label for="mc_edit_all"><?php _e( 'Edit All Categories', 'my-calendar' ); ?></li>
						<?php echo mc_category_select( $permissions, true, true, 'mc_user_permissions[]' ); ?>
					</ul>
				</td>
			</tr>
			<?php echo apply_filters( 'mc_user_fields', '', $user_edit ); ?>
		</table>
		<?php
	}
}

/**
 * Save user profile data
 */
function mc_save_profile() {
	global $user_ID;
	$current_user = wp_get_current_user();
	if ( isset( $_POST['user_id'] ) ) {
		$edit_id = (int) $_POST['user_id'];
	} else {
		$edit_id = $user_ID;
	}
	if ( current_user_can( 'manage_options' ) ) {
		if ( isset( $_POST['mc_user_permissions'] ) ) {
			$mc_user_permission = $_POST['mc_user_permissions'];
			update_user_meta( $edit_id, 'mc_user_permissions', $mc_user_permission );
		} else {
			delete_user_meta( $edit_id, 'mc_user_permissions' );
		}
	}

	apply_filters( 'mc_save_user', $edit_id, $_POST );
}


/**
 * Generate fields to select event categories.
 *
 * @param object               $data object with event_category value.
 * @param boolean              $option Type of form.
 * @param boolean              $multiple Allow multiple categories to be entered.
 * @param mixed boolean/string $name Field name for input.
 *
 * @return string HTML fields.
 */
function mc_category_select( $data = false, $option = true, $multiple = false, $name = false ) {
	if ( ! $name ) {
		$name = 'event_category[]';
	}
	// Grab all the categories and list them.
	$list    = '';
	$default = '';
	$cats    = mc_no_category_default();
	if ( ! empty( $cats ) ) {
		$cats = apply_filters( 'mc_category_list', $cats, $data, $option, $name );
		foreach ( $cats as $cat ) {
			$selected = '';
			// if category is private, don't show if user is not logged in.
			if ( '1' === (string) $cat->category_private && ! is_user_logged_in() ) {
				continue;
			}
			if ( ! empty( $data ) ) {
				if ( ! is_object( $data ) ) {
					$category = $data;
				} elseif ( is_array( $data ) && $multiple && 'mc_user_permissions[]' === $name ) {
					$category = $data;
				} else {
					if ( $multiple ) {
						$category = ( property_exists( $data, 'user_error' ) ) ? $data->event_categories : mc_get_categories( $data );
					} else {
						$category = $data->event_category;
					}
				}
				if ( $multiple ) {
					if ( is_array( $category ) && in_array( $cat->category_id, $category, true ) ) {
						$selected = ' checked="checked"';
					} elseif ( is_numeric( $category ) && ( (int) $category === (int) $cat->category_id ) ) {
						$selected = ' checked="checked"';
					} elseif ( ! $category ) {
						$selected = ( get_option( 'mc_default_category' ) === (string) $cat->category_id ) ? ' checked="checked"' : '';
					}
				} else {
					if ( (int) $category === (int) $cat->category_id ) {
						$selected = ' selected="selected"';
					}
				}
			} else {
				if ( get_option( 'mc_default_category' ) === (string) $cat->category_id ) {
					$selected = ' checked="checked"';
				}
			}
			$category_name = strip_tags( stripslashes( trim( $cat->category_name ) ) );
			$category_name = ( '' === $category_name ) ? '(' . __( 'Untitled category', 'my-calendar' ) . ')' : $category_name;
			if ( $multiple ) {
				$c = '<li class="mc_cat_' . $cat->category_id . '"><input type="checkbox"' . $selected . ' name="' . esc_attr( $name ) . '" id="mc_cat_' . $cat->category_id . '" value="' . $cat->category_id . '" ' . $selected . ' /> <label for="mc_cat_' . $cat->category_id . '">' . $category_name . '</label></li>';
			} else {
				$c = '<option value="' . $cat->category_id . '" ' . $selected . '>' . $category_name . '</option>';
			}
			if ( get_option( 'mc_default_category' ) !== (string) $cat->category_id ) {
				$list .= $c;
			} else {
				$default = $c;
			}
		}
	} else {
		$category_url = admin_url( 'admin.php?page=my-calendar-categories' );
		// Translators: URL to add a new category.
		mc_show_error( sprintf( __( 'You do not have any categories created. Please <a href="%s">create at least one category!</a>', 'my-calendar' ), $category_url ) );
	}
	if ( ! $option ) {
		$default = ( get_option( 'mc_default_category' ) ) ? get_option( 'mc_default_category' ) : 1;

		return ( is_object( $data ) ) ? $data->event_category : $default;
	}

	return $default . $list;
}

/**
 * Get all categories for given event
 *
 * @param object  $event Event object.
 * @param boolean $ids Return objects or ids.
 *
 * @return array of values
 */
function mc_get_categories( $event, $ids = true ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}

	$return  = array();
	$results = false;
	if ( is_object( $event ) ) {
		$event_id = absint( $event->event_id );
		$primary  = $event->event_category;
		if ( property_exists( $event, 'categories' ) ) {
			$results = $event->categories;
		}
	} elseif ( is_numeric( $event ) ) {
		$event_id = absint( $event );
		$primary  = mc_get_data( 'event_category', $event_id );
	} else {

		return ( 'html' === $ids ) ? '' : array();
	}

	if ( ! $results ) {
		$relate  = my_calendar_category_relationships_table();
		$catego  = my_calendar_categories_table();
		$results = $mcdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $relate . ' as r JOIN ' . $catego . ' as c ON c.category_id = r.category_id WHERE event_id = %d', $event_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}
	if ( true === $ids ) {
		if ( $results ) {
			foreach ( $results as $result ) {
				$return[] = $result->category_id;
			}
		} else {
			$return[] = $primary;
		}
	} elseif ( 'html' === $ids ) {
		$return = mc_categories_html( $results, $primary );
	} elseif ( 'testing' === $ids ) {
		if ( $results ) {
			foreach ( $results as $result ) {
				$return[] = $result->category_id;
			}
		}
	} else {
		$return = ( is_array( $results ) ) ? $results : array( $event->event_category );
	}

	return $return;
}

/**
 * Return HTML representing categories.
 *
 * @param array $results array of categories.
 * @param int   $primary Primary selected category for event.
 *
 * @return String
 */
function mc_categories_html( $results, $primary ) {
	if ( $results ) {
		foreach ( $results as $result ) {
			if ( ! is_object( $result ) ) {
				$result = (object) $result;
			}
			$icon     = mc_category_icon( $result );
			$return[] = $icon . $result->category_name;
		}
	} else {
		$return[] = $primary;
	}
	$return = implode( ', ', $return );

	return $return;
}
