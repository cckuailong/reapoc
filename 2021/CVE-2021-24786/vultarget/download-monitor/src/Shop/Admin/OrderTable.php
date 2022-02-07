<?php

namespace Never5\DownloadMonitor\Shop\Admin;

use Never5\DownloadMonitor\Shop\Services\Services;
use Never5\DownloadMonitor\Shop\Util\PostType;

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * OrderTable class.
 *
 * @extends \WP_List_Table
 */
class OrderTable extends \WP_List_Table {

	private $filter_status = '';
	private $orders_per_page = 25;
	private $filter_month = '';
	private $filter_user = 0;

	/** @var bool $display_delete_message */
	private $display_delete_message = false;

	/**
	 * __construct function.
	 *
	 * @access public
	 */
	public function __construct() {
		global $status, $page, $wpdb;

		parent::__construct( array(
			'singular' => 'log',
			'plural'   => 'logs',
			'ajax'     => false
		) );

		// check if we need to empty the trash
		if ( isset( $_REQUEST['dlm_empty_trash'] ) ) {
			$this->empty_trash();
		}

		$this->filter_status   = isset( $_REQUEST['status'] ) ? sanitize_text_field( $_REQUEST['status'] ) : '';
		$this->orders_per_page = ! empty( $_REQUEST['orders_per_page'] ) ? intval( $_REQUEST['orders_per_page'] ) : 25;
		$this->filter_month    = ! empty( $_REQUEST['filter_month'] ) ? sanitize_text_field( $_REQUEST['filter_month'] ) : '';

		if ( $this->orders_per_page < 1 ) {
			$this->orders_per_page = 9999999999999;
		}
	}

	/**
	 * Empty trash
	 */
	private function empty_trash() {
		if ( Services::get()->service( 'order_repository' )->empty_trash() ) {
			?>
            <div id="message" class="updated notice notice-success">
                <p><?php _e( 'Trashed orders have been permanently deleted.', 'download-monitor' ); ?></p>
            </div>
			<?php
		}
	}

	/**
	 * Get the base URL for the order page
	 *
	 * @return string
	 */
	private function get_base_url() {
		return admin_url( sprintf( "edit.php?post_type=%s&page=download-monitor-orders", PostType::KEY ) );
	}

	/**
	 * The checkbox column
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return string
	 */
	public function column_cb( $order ) {
		return sprintf(
			'<input type="checkbox" name="order[]" value="%s" />', $order->get_id()
		);
	}

	/**
	 * Add bulk actions
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array();
		/** @todo add bulk actions in later version */
		$actions = array(
			'delete' => __( 'Delete', 'download-monitor' )
		);

		return $actions;
	}

	/**
	 * column_default function.
	 *
	 * @access public
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 * @param mixed $column_name
	 *
	 * @return string
	 */
	public function column_default( $order, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				$customer_name = "";
				if ( '' !== $order->get_customer()->get_first_name() ) {
					$customer_name .= " " . $order->get_customer()->get_first_name();
				}
				if ( '' !== $order->get_customer()->get_last_name() ) {
					$customer_name .= " " . $order->get_customer()->get_last_name();
				}

				return '<a href="' . add_query_arg( 'details', $order->get_id(), $this->get_base_url() ) . '">#' . esc_html( $order->get_id() . ' ' . $customer_name ) . '</a>';
				break;
			case 'status' :
				return '<span class="dlm-order-status dlm-order-status-' . $order->get_status()->get_key() . '" title="' . $order->get_status()->get_label() . '">' . esc_html( $order->get_status()->get_label() ) . '</span>';
				break;
			case 'date' :
				$time_str = date_i18n( get_option( 'date_format' ), $order->get_date_created()->format( 'U' ) );

				return '<time title="' . $time_str . '"">' . $time_str . '</time>';
				break;

			case 'total':
				return '<span class"dlm-order-total">' . dlm_format_money( $order->get_total(), array( 'currency' => $order->get_currency() ) ) . '</span>';
				break;
		}
	}

	/**
	 * get_columns function.
	 *
	 * @access public
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'     => '',
			'id'     => __( 'Order', 'download-monitor' ),
			'date'   => __( 'Date', 'download-monitor' ),
			'status' => __( 'Status', 'download-monitor' ),
			'total'  => __( 'Total', 'download-monitor' ),

		);

		return $columns;
	}

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'id'     => array( 'id', false ),
			'date'   => array( 'date', false ),
			'status' => array( 'status', false ),
			'total'  => array( 'total', false )
		);
	}

	/**
	 * Generate the table navigation above or below the table
	 */
	public function display_tablenav( $which ) {

		// output nonce
		if ( 'top' == $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}

		// display 'delete' success message
		if ( 'top' == $which && true === $this->display_delete_message ) {
			?>
            <div id="message" class="updated notice notice-success">
                <p><?php _e( 'Orders deleted', 'download-monitor' ); ?></p>
            </div>
			<?php
		}

		if ( 'top' == $which ) {
			$base_url = $this->get_base_url();
			?>
            <ul class="subsubsub">
                <li class="all"><a
                            href="<?php echo $base_url; ?>" <?php echo ( '' === $this->filter_status ) ? ' class="current"' : ''; ?>><?php _e( 'All', 'download-monitor' ); ?></a>
                </li>
				<?php

				/** @var \Never5\DownloadMonitor\Shop\Order\WordPressRepository $order_repo */
				$order_repo = Services::get()->service( 'order_repository' );
				$statuses   = Services::get()->service( 'order_status' )->get_available_statuses();
				/** @var \Never5\DownloadMonitor\Shop\Order\Status\OrderStatus $status */
				foreach ( $statuses as $status ) {

					$count = $order_repo->num_rows( array(
						array(
							'key'   => 'status',
							'value' => $status->get_key()
						)
					) );
					if ( $count > 0 ) {
						echo ' | <li class="' . $status->get_key() . '"><a ' . ( ( $status->get_key() === $this->filter_status ) ? ' class="current"' : '' ) . ' href="' . add_query_arg( 'status', $status->get_key(), $base_url ) . '">' . $status->get_label() . ' (' . $count . ')</a></li>' . PHP_EOL;
					}

				}
				?>
            </ul>
		<?php } ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <div class="alignleft actions bulkactions">
				<?php $this->bulk_actions( $which ); ?>
            </div>

			<?php if ( 'top' == $which ) { ?>

                <div class="alignleft actions">

					<?php
					global $wpdb, $wp_locale;

					$months = $wpdb->get_results( "
							SELECT DISTINCT YEAR( date_created ) AS year, MONTH( date_created ) AS month
							FROM {$wpdb->prefix}dlm_order
							ORDER BY date_created DESC
						"
					);

					$month_count = count( $months );

					if ( $month_count && ! ( 1 == $month_count && 0 == $months[0]->month ) ) {
						$m = ! empty( $this->filter_month ) ? $this->filter_month : 0;
						?>
                        <select name="filter_month">
                            <option <?php selected( $m, 0 ); ?> value='0'><?php _e( 'Show all dates' ); ?></option>
							<?php
							foreach ( $months as $arc_row ) {
								if ( 0 == $arc_row->year ) {
									continue;
								}

								$month = zeroise( $arc_row->month, 2 );
								$year  = $arc_row->year;

								printf( "<option %s value='%s'>%s</option>\n",
									selected( $m, $year . '-' . $month, false ),
									esc_attr( $year . '-' . $month ),

									sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
								);
							}
							?>
                        </select>
					<?php } ?>

                    <select name="orders_per_page">
                        <option value="25"><?php _e( '25 per page', 'download-monitor' ); ?></option>
                        <option
                                value="50" <?php selected( $this->orders_per_page, 50 ) ?>><?php _e( '50 per page', 'download-monitor' ); ?></option>
                        <option
                                value="100" <?php selected( $this->orders_per_page, 100 ) ?>><?php _e( '100 per page', 'download-monitor' ); ?></option>
                        <option
                                value="200" <?php selected( $this->orders_per_page, 200 ) ?>><?php _e( '200 per page', 'download-monitor' ); ?></option>
                        <option
                                value="-1" <?php selected( $this->orders_per_page, - 1 ) ?>><?php _e( 'Show All', 'download-monitor' ); ?></option>
                    </select>

                    <input type="hidden" name="post_type" value="<?php echo PostType::KEY; ?>"/>
                    <input type="hidden" name="page" value="download-monitor-orders"/>
                    <input type="submit" value="<?php _e( 'Filter', 'download-monitor' ); ?>" class="button"/>

					<?php

					if ( 'trash' === $this->filter_status ) {
						?><input type="submit" name="dlm_empty_trash" id="dlm_empty_trash" class="button apply"
                                 value="<?php _e( "Empty Trash", 'download-monitor' ); ?>"/>
						<?php
					}

					?>
                </div>
				<?php
			}
			?>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
            <br class="clear"/>
        </div>
		<?php
	}

	/**
	 * prepare_items function.
	 *
	 * @access public
	 * @return void
	 */
	public function prepare_items() {
		global $wpdb;

		// process bulk action
		$this->process_bulk_action();

		// Init headers
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$per_page     = absint( $this->orders_per_page );
		$current_page = absint( $this->get_pagenum() );

		// setup filters
		$filters = array();

		// filter status
		if ( $this->filter_status ) {
			$filters[] = array(
				'key'   => 'status',
				'value' => $this->filter_status
			);
		}

		// filter month
		if ( $this->filter_month ) {
			$filters[] = array(
				'key'      => 'date_created',
				'value'    => date( 'Y-m-01', strtotime( $this->filter_month ) ),
				'operator' => '>='
			);

			$filters[] = array(
				'key'      => 'date_created',
				'value'    => date( 'Y-m-t', strtotime( $this->filter_month ) ),
				'operator' => '<='
			);
		}

		// filter on user
		/*
		if ( $this->filter_user > 0 ) {
			$filters[] = array(
				'key'   => 'user_id',
				'value' => $this->filter_user
			);
		}
		*/

		// check for order
		$order_by = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';
		$order    = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'DESC';

		/** @var \Never5\DownloadMonitor\Shop\Order\WordPressRepository $order_repository */
		$order_repository = Services::get()->service( 'order_repository' );

		$total_items = $order_repository->num_rows( $filters );

		$this->items = $order_repository->retrieve( $filters, $per_page, ( ( $current_page - 1 ) * $per_page ), $order_by, $order );

		// Pagination
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => absint( $this->orders_per_page ),
			'total_pages' => ( ( $total_items > 0 ) ? ceil( $total_items / absint( $this->orders_per_page ) ) : 1 )
		) );
	}

	/**
	 * Process bulk actions
	 */
	public function process_bulk_action() {

		return;

		if ( 'delete' === $this->current_action() ) {

			// check nonce
			if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'bulk-' . $this->_args['plural'] ) ) {
				wp_die( 'process_bulk_action() nonce check failed' );
			}

			// check capability
			if ( ! current_user_can( 'dlm_manage_logs' ) ) {
				wp_die( "You're not allowed to delete orders!" );
			}

			// check
			if ( count( $_POST['log'] ) > 0 ) {

				// @todo: implement delete

				// display delete message
				$this->display_delete_message = true;

			}

		}

	}

}
