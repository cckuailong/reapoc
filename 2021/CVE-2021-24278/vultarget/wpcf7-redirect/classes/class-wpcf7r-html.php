<?php
/**
 * Class WPCF7R_Html - Mainly static functions class to create html fregments
 */

defined( 'ABSPATH' ) || exit;

class WPCF7R_Html {
	public static $mail_tags;

	public static $active_conditional_logic;
	/**
	 * The main class constructor
	 *
	 * @param string $mail_tags
	 */

	public function __construct( $mail_tags = '' ) {
		self::$mail_tags = $mail_tags;
	}

	/**
	 * Display admin groupos
	 */
	public static function conditional_groups_display( $group_block, $prefix ) {
		foreach ( $group_block['groups'] as $group_key => $group ) {
			echo self::group_display( 'block_1', $group_key, $group, $prefix );
		}
	}

	/**
	 * Print a single group of seetings
	 */
	public static function group_display( $block_key = '', $group_key = '', $group = array(), $prefix ) {
		ob_start();
		echo self::get_conditional_row_group_start( $group_key, $group );
		echo self::get_conditional_row_group_rows_start( $group_key, $group );

		foreach ( $group as $group_row => $row_fields ) {
			echo self::get_conditional_row_template( $block_key, $group_key, $group_row, $row_fields, $prefix, false );
		}

		echo self::get_conditional_row_group_rows_end();
		echo self::get_conditional_row_group_end();
		return ob_get_clean();
	}

	/**
	 * Conditional group row start html
	 */
	public static function get_conditional_row_group_rows_start( $group_key, $group ) {
		return '<div class="conditional-group-block active" data-block-id="block_1">
            <table class="wp-list-table widefat fixed striped pages repeater-table leads-list">
                <thead>
                    <tr>
                        <th colspan="4"><h3>' . __( 'IF', 'wpcf7-redirect' ) . '</h3></th>
                    </tr>
                </thead>
                <tbody>';
	}

	/**
	 * Conditional group row end html
	 */
	public static function get_conditional_row_group_rows_end() {
		return '</tbody></table></div>';
	}

	/**
	 * Conditional row group start
	 */
	public static function get_conditional_row_group_start( $group_key, $group ) {
		return '<div class="wpcfr-rule-group group-' . $group_key . '" data-group-id="' . $group_key . '"><div class="group-title title-or"><h3>' . __( 'OR', 'wpcf7-redirect' ) . '</h3></div>';
	}

	/**
	 * Get Conditional Row Group End
	 */
	public static function get_conditional_row_group_end() {
		return '</div>';
	}

	/**
	 * Get the title html block
	 *
	 * @param  $group_block_key
	 * @param  $group_block
	 * @param  $active_tab_title
	 * @param  boolean          $echo
	 * @param  $prefix
	 */
	public static function get_block_title( $group_block_key, $group_block, $active_tab_title, $echo = true, $prefix ) {
		ob_start();
		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'block-title.php';
		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * Get an HTML template of a condition row
	 */
	public static function get_conditional_row_template( $block_key = '', $group_key = '', $group_row = '', $row_fields = array(), $prefix, $required = true ) {
		ob_start();
		$condition = $row_fields['condition'];
		$tags      = WPCF7R_Form::get_mail_tags();
		$required  = $required ? 'required' : '';
		?>
		<tr class="row-template">
			<td>
				<select class="wpcf7r-fields" name="wpcf7-redirect<?php echo $prefix; ?>[blocks][<?php echo $block_key; ?>][groups][<?php echo $group_key; ?>][<?php echo $group_row; ?>][if]" <?php echo $required; ?>>
					<option value="" <?php selected( $row_fields['if'], '' ); ?>><?php _e( 'Select' ); ?></option>
					<?php
					if ( $tags ) :
						foreach ( $tags as $mail_tag ) :
							?>
						<option value="<?php echo $mail_tag['name']; ?>" <?php selected( $mail_tag['name'], $row_fields['if'] ); ?>><?php echo $mail_tag['name']; ?></option>
							<?php
						endforeach;
					endif;
					?>
				</select>
			</td>
			<td>
				<select class="compare-options" name="wpcf7-redirect<?php echo $prefix; ?>[blocks][<?php echo $block_key; ?>][groups][<?php echo $group_key; ?>][<?php echo $group_row; ?>][condition]" <?php echo $required; ?>>
					<option value="" <?php selected( $condition, '' ); ?>><?php _e( 'Select', 'wpcf7-redirect' ); ?></option>
					<option value="equal" <?php selected( $condition, 'equal' ); ?> data-comparetype="select"><?php _e( 'Equal', 'wpcf7-redirect' ); ?></option>
					<option value="not-equal" <?php selected( $condition, 'not-equal' ); ?> data-comparetype="select"><?php _e( 'Non Equal', 'wpcf7-redirect' ); ?></option>
					<option value="contain" <?php selected( $condition, 'contain' ); ?> data-comparetype=""><?php _e( 'Contains', 'wpcf7-redirect' ); ?></option>
					<option value="not-contain" <?php selected( $condition, 'not-contain' ); ?> data-comparetype=""><?php _e( 'Does not Contain', 'wpcf7-redirect' ); ?></option>
					<option value="less_than" <?php selected( $condition, 'less_than' ); ?> data-comparetype=""><?php _e( 'Less than', 'wpcf7-redirect' ); ?></option>
					<option value="greater_than" <?php selected( $condition, 'greater_than' ); ?> data-comparetype=""><?php _e( 'Greater than', 'wpcf7-redirect' ); ?></option>
					<option value="is_null" <?php selected( $condition, 'is_null' ); ?> data-comparetype=""><?php _e( 'Is Empty', 'wpcf7-redirect' ); ?></option>
					<option value="is_not_null" <?php selected( $condition, 'is_not_null' ); ?> data-comparetype=""><?php _e( 'Is Not Empty', 'wpcf7-redirect' ); ?></option>
				</select>
			</td>
			<td>
				<?php
				$select_visible = false;
				$select_fields  = array(
					'select*',
					'radio*',
					'checkbox*',
					'select',
					'radio',
					'checkbox',
				);
				if ( $tags ) :
					foreach ( $tags as $mail_tag ) :
						?>
						<?php if ( in_array( $mail_tag->type, $select_fields, true ) ) : ?>
							<?php $select_visible = $row_fields['if'] === $mail_tag['name'] ? true : $select_visible; ?>
							<select class="group_row_value group_row_value_select" style="<?php echo $row_fields['if'] !== $mail_tag['name'] ? 'display:none;' : ''; ?>" data-rel="<?php echo $mail_tag['name']; ?>">
								<option value="" <?php selected( $row_fields['value'], '' ); ?>><?php _e( 'Select', 'wpcf7-redirect' ); ?></option>
								<?php
								foreach ( $mail_tag->raw_values as $orig_value ) :
									$orig_value = explode( '|', $orig_value );
									$label      = $orig_value[0];
									$value      = isset( $orig_value[1] ) && $orig_value[1] ? $orig_value[1] : $orig_value[0];
									?>
									<option value="<?php echo $value; ?>"
										<?php
										if ( isset( $row_fields['value'] ) ) :
											selected( $row_fields['value'], $value );
													endif;
										?>
												>
										<?php echo $label; ?>
									</option>
								<?php endforeach; ?>
							</select>
						<?php endif; ?>
						<?php
					endforeach;
				endif;
				?>
				<input type="text" class="group_row_value wpcf7-redirect-value" name="wpcf7-redirect<?php echo $prefix; ?>[blocks][<?php echo $block_key; ?>][groups][<?php echo $group_key; ?>][<?php echo $group_row; ?>][value]" value="<?php echo isset( $row_fields['value'] ) ? esc_html( $row_fields['value'] ) : ''; ?>" placeholder="<?php _e( 'Your value here' ); ?>" style="<?php echo $select_visible ? 'display:none;' : ''; ?>">
			</td>
			<td>
				<div class="qs-condition-actions">
					<div class="group-actions">
						<span class="dashicons dashicons-minus"></span>
						<a href="#" class="button add-condition"><?php _e( 'And', 'wpcf7-redirect' ); ?></a>
					</div>
				</div>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get the html of the rule block
	 *
	 * @param  $group_block_key
	 * @param  $group_block
	 * @param  $active_tab
	 * @param  boolean         $echo
	 * @param  $prefix
	 */
	public static function get_block_html( $group_block_key, $group_block, $active_tab, $echo = true, $prefix ) {
		ob_start();
		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'block-html.php';
		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}

	/**
	 * A function that returns the relevant field HTML
	 *
	 * @param  $field
	 * @param  $prefix
	 */
	public static function render_field( $field, $prefix ) {
		switch ( $field['type'] ) {
			case 'text':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-text.php';
				break;
			case 'download':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-download.php';
				break;
			case 'password':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-password.php';
				break;
			case 'url':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-url.php';
				break;
			case 'textarea':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-textarea.php';
				break;
			case 'blocks':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-rule-blocks.php';
				break;
			case 'checkbox':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-checkbox.php';
				break;
			case 'post_type_select':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-post-type-select.php';
				break;
			case 'page_select':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-page-select.php';
				break;
			case 'notice':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-notice.php';
				break;
			case 'select':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-select.php';
				break;
			case 'tags_map':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-tags-map.php';
				break;
			case 'leads_map':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-leads-mapping.php';
				break;
			case 'debug_log':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-debug-log.php';
				break;
			case 'json':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-json-editor.php';
				break;
			case 'repeater':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-repeater.php';
				break;
			case 'section':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'section.php';
				break;
			case 'button':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-button.php';
				break;
			case 'description':
			case 'editor':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-editor.php';
				break;
			case 'number':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-number.php';
				break;
			case 'taxonomy':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-taxonomy.php';
				break;
			case 'post_author_select':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-post-author-select.php';
				break;
			case 'media':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-media.php';
				break;
			case 'preview':
				$template = WPCF7_PRO_REDIRECT_FIELDS_PATH . 'field-preview.php';
				break;
		}

		$template = apply_filters( 'render_field', $template, $field, $prefix, WPCF7_PRO_REDIRECT_FIELDS_PATH );
		include $template;
	}
}
