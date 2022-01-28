<?php
/**
 * Class WPCF7R_Leads_Manager - Container class that handles leads management
 */
defined( 'ABSPATH' ) || exit;

class WPCF7R_Leads_Manager {
	/**
	 * Save a reference to the last lead inserted to the DB
	 */
	public static $new_lead_id;

	/**
	 * Define the leads post type
	 *
	 * @var string $cf7_id - contact form id.
	 */
	public static $post_type = 'wpcf7r_leads';

	/**
	 * Main leads manager initializaition.
	 *
	 * @param [string] $cf7_id - contact form id.
	 */
	public function __construct( $cf7_id ) {
		$this->cf7_id = $cf7_id;

		$this->leads = array();

	}

	/**
	 * Admin init hook. 
	 *
	 * @return void
	 */
	public static function admin_init_scripts() {
		add_filter( 'manage_wpcf7r_leads_posts_columns', array( 'WPCF7R_Leads_Manager', 'set_custom_edit_wpcf7r_leads_columns' ) );
		add_action( 'manage_wpcf7r_leads_posts_custom_column', array( 'WPCF7R_Leads_Manager', 'custom_wpcf7r_leads_column' ), 10, 2 );
		add_action( 'manage_posts_extra_tablenav', array( 'WPCF7R_Leads_Manager', 'display_export_button' ), 10, 2 );
	}

	/**
	 * Display custom post type columns on edit list.
	 *
	 * @param [type] $column - the key of the column.
	 * @param [int]  $post_id - the lead id.
	 * @return void
	 */
	public static function custom_wpcf7r_leads_column( $column, $lead_id ) {
		$action_id = get_post_meta( $lead_id, 'cf7_action_id', true );

		$action = WPCF7R_Action::get_action( (int) $action_id );

		if ( $action ) {
			$action->display_action_column_content( $column, $lead_id );
		} else {
			switch ( $column ) {
				case 'data_preview':
					echo __( 'Preview is not available: action does not exist', 'wpcf7-redirect' );
					break;
				case 'form':
					$form_id = get_post_meta( $lead_id, 'cf7_form', true );
					echo WPCF7r_Form_Helper::get_cf7_link_html( $form_id );
					break;
			}
		}
	}

	/**
	 * Adds an export button on the edit post list.
	 *
	 * @param [type] $which
	 * @return void
	 */
	public static function display_export_button( $which ) {
		global $typenow;

		if ( self::get_post_type() === $typenow && 'top' === $which ) {
			?>
			<input type="submit" name="export_leads" class="button button-primary" value="<?php _e( 'Export' ); ?>" />
			<?php
		}
	}

	/**
	 * Export the current filtered list.
	 *
	 * @return void
	 */
	public static function export_current_filtered_view() {

		if ( isset( $_GET['export_leads'] ) ) {
			$meta_query = array();

			$args = array(
				'post_type'      => self::get_post_type(),
				'post_status'    => 'any',
				'posts_per_page' => -1,
			);

			if ( isset( $_GET['cf7_form'] ) && $_GET['cf7_form'] ) {
				$meta_query[] = array(
					'key'   => 'cf7_form',
					'value' => (int) $_GET['cf7_form'],
				);
			}

			if ( isset( $_GET['m'] ) && $_GET['m'] ) {

				$month = substr( $_GET['m'], 4, 2 );
				$year  = substr( $_GET['m'], 0, 4 );

				$args['date_query'] = array(
					array(
						'year'  => $year,
						'month' => $month,
					),
				);
			}

			if ( $meta_query ) {
				$args['meta_query'] = $meta_query;
			}

			$arr_post = get_posts( $args );

			$forms = array();

			/**
			 * Order leads by form.
			 * Because the forms are dynamic we create diffrent headers for each form.
			 */
			foreach ( $arr_post as $lead ) {
				$form_id = get_post_meta( $lead->ID, 'cf7_form', true );

				$custom_fields = get_post_custom( $lead->ID );

				foreach ( $custom_fields as $custom_field_key => $custom_field_value ) {
					$value = maybe_unserialize( reset( $custom_field_value ) );

					if ( ! is_array( $value ) && '_' !== substr( $custom_field_key, 0, 1 ) ) {
						$forms[ $form_id ]['leads'][ $lead->ID ][ $custom_field_key ] = $value;
						$forms[ $form_id ]['headers'][ $custom_field_key ]            = $custom_field_key;
					}
				}

				$forms[ $form_id ]['leads'][ $lead->ID ]['form_name']   = get_the_title( $form_id ) ? get_the_title( $form_id ) : __( 'Form does not exist', 'wpcf7-redirect' );
				$forms[ $form_id ]['leads'][ $lead->ID ]['form_id']     = $form_id;
				$forms[ $form_id ]['leads'][ $lead->ID ]['record_date'] = get_the_date( 'Y-m-d H:i', $lead->ID );
			}

			if ( $forms ) {

				header( 'Content-type: text/csv' );
				header( 'Content-Disposition: attachment; filename="wp-leads.csv"' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				$file = fopen( 'php://output', 'w' );

				// Print UTF8 encoding.
				fprintf( $file, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

				foreach ( $forms as $form_id => $form ) {
					// Add default headers.
					$form['headers']['form_name']   = 'form_name';
					$form['headers']['form_id']     = 'form_id';
					$form['headers']['record_date'] = 'record_date';

					// Print headers.
					fputcsv( $file, $form['headers'] );

					foreach ( $form['leads'] as $lead ) {
						$values_to_print = array();

						foreach ( $form['headers'] as $header_key ) {
							$values_to_print[ $header_key ] = isset( $lead[ $header_key ] ) ? $lead[ $header_key ] : '';
						}

						fputcsv( $file, $values_to_print );
					}

					fputcsv( $file, array() );
				}

				exit();
			}
		}
	}


	/**
	 * Undocumented function
	 *
	 * @param [array] $columns - list of columns.
	 * @return [array] - the key of the column.
	 */
	public static function set_custom_edit_wpcf7r_leads_columns( $columns ) {

		$columns['form']         = __( 'Form', 'wpcf7-redirect' );
		$columns['data_preview'] = __( 'Preview', 'wpcf7-redirect' );

		return $columns;
	}
	/**
	 * Get the leads post type
	 */
	public static function get_post_type() {
		return self::$post_type;
	}

	/**
	 * Add A select filter on edit.php screen to filter records by form
	 */
	public static function add_form_filter() {

		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';

		if ( $post_type && self::get_post_type() === $post_type ) {
			$values = array();

			$forms = get_posts(
				array(
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'post_type'      => 'wpcf7_contact_form',
				)
			);

			foreach ( $forms as $form ) :
				$values[ $form->post_title ] = $form->ID;
			endforeach;

			?>

			<select name="cf7_form">
				<option value=""><?php _e( 'Form', 'wpcf7-redirect' ); ?></option>
				<?php
					$current_v = isset( $_GET['cf7_form'] ) ? (int) $_GET['cf7_form'] : '';

				foreach ( $values as $label => $value ) {
					printf(
						'<option value="%s"%s>%s</option>',
						$value,
						$value === $current_v ? ' selected="selected"' : '',
						$label
					);
				}
				?>
			</select>
			<?php
		}
	}

	/**
	 * Search by filters
	 *
	 * @param [object] $query - WP_Query object.
	 * @return [object] - WP_Query.
	 */
	public static function filter_request_query( $query ) {
		// modify the query only if it admin and main query.
		if ( ! ( is_admin() && $query->is_main_query() ) ) {
			return $query;
		}

		// we want to modify the query for the targeted custom post and filter option.
		if ( ! isset( $query->query['post_type'] ) || ( ! ( self::get_post_type() === $query->query['post_type'] && isset( $_REQUEST['cf7_form'] ) ) ) ) {
			return $query;
		}

		// for the default value of our filter no modification is required.
		if ( 0 === (int) $_REQUEST['cf7_form'] ) {
			return $query;
		}

		// modify the query_vars.
		$posted_value = isset( $_REQUEST['cf7_form'] ) && (int) $_REQUEST['cf7_form'] ? (int) $_REQUEST['cf7_form'] : '';

		$meta_query = $query->get( 'meta_query' );

		if ( ! $meta_query ) {
			$meta_query = array();
		}

		$meta_query[] = array(
			array(
				'key'     => 'cf7_form',
				'value'   => $posted_value,
				'compare' => '=',
			),
		);

		$query->set( 'meta_query', $meta_query );

		return $query;
	}

	/**
	 * Initialize leads table tab
	 */
	public function init() {
		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'leads . php';
	}

	/**
	 * Get the url to the admin post type list
	 * Auto filter by selected action
	 *
	 * @param [int] $form_id - the contact form id.
	 * @return [string] - the new url.
	 */
	public static function get_admin_url( $form_id ) {
		$url = admin_url( 'edit.php?post_type=' . self::get_post_type() );

		return add_query_arg( 'cf7_form', $form_id, $url );
	}

	/**
	 * Get leads
	 */
	public function get_leads() {
		$args = array(
			'post_type'      => self::get_post_type(),
			'post_status'    => 'private',
			'posts_per_page' => 20,
			'meta_query'     => array(
				array(
					'key'   => 'cf7_form',
					'value' => $this->cf7_id,
				),
			),
		);

		$leads_posts = get_posts( $args );

		if ( $leads_posts ) {
			foreach ( $leads_posts as $leads_post ) {
				$lead = new WPCF7R_Lead( $leads_post );

				$this->leads[] = $lead;
			}
		}

		return $this->leads;
	}

	/**
	 * Insert new lead
	 */
	public static function insert_lead( $cf7_form_id, $args, $files = array(), $lead_type, $action_id ) {
		$args['cf7_form']      = $cf7_form_id;
		$args['cf7_action_id'] = $action_id;

		$contact_form_title = get_the_title( $cf7_form_id );

		$new_post = array(
			'post_type'   => self::get_post_type(),
			'post_status' => 'private',
			'post_title'  => __( 'Lead from contact form: ', 'wpcf7-redirect' ) . $contact_form_title,
		);

		self::$new_lead_id = wp_insert_post( $new_post );

		$lead = new WPCF7R_Lead( self::$new_lead_id );

		$lead->update_lead_data( $args );

		$lead->update_lead_files( $files );

		$lead->update_lead_type( $lead_type );

		return $lead;
	}

	/**
	 * Save the action to the db lead
	 *
	 * @param  $lead_id
	 * @param  $action_name
	 * @param  $details
	 */
	public static function save_action( $lead_id, $action_name, $details ) {
		add_post_meta( $lead_id, 'action - ' . $action_name, $details );
	}

	/**
	 * Get a single action row
	 */
	public function get_lead_row( $lead ) {
		ob_start();
		do_action( 'before_wpcf7r_lead_row', $this );
		?>

		<tr class="primary" data-postid="<?php echo $lead->get_id(); ?>">
			<td class="manage-column column-primary sortable desc edit column-id">
				<?php echo $lead->get_id(); ?>
				<div class="row-actions">
					<span class="edit">
						<a href="<?php echo get_edit_post_link( $lead->get_id() ); ?>" data-id="<?php echo $lead->get_id(); ?>" aria-label="<?php _e( 'View', 'wpcf7-redirect' ); ?>" target="_blank"><?php _e( 'View', 'wpcf7-redirect' ); ?></a> |
					</span>
					<span class="trash">
						<a href="#" class="submitdelete" data-id="<?php echo $lead->get_id(); ?>" aria-label="<?php _e( 'Move to trash', 'wpcf7-redirect' ); ?>"><?php _e( 'Move to trash', 'wpcf7-redirect' ); ?></a> |
					</span>
					<?php do_action( 'wpcf7r_after_lead_links', $lead ); ?>
				</div>
			</td>
			<td class="manage-column column-primary sortable desc edit column-date">
				<?php echo $lead->get_date(); ?>
			</td>
			<td class="manage-column column-primary sortable desc edit column-time"><?php echo $lead->get_time(); ?></td>
			<td class="manage-column column-primary sortable desc edit column-type"><?php echo $lead->get_lead_type(); ?></td>
			<td></td>
		</tr>

		<?php
		do_action( 'after_wpcf7r_lead_row', $this );

		return apply_filters( 'wpcf7r_get_lead_row', ob_get_clean(), $this );
	}
}
