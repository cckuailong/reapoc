<?php
/**
 * Manage Nukes within WooCommerce - Store Toolkit.
 *
 * @package woo_st
 * @subpackage commands/community
 * @maintainer Visser Labs
 */
class Store_Toolkit_Command extends WP_CLI_Command {

  /**
   * List supported Nuke Types.
   *
   * ## EXAMPLES
   *
   *     wp store-toolkit list
   *
   * @subcommand list
   */
	function _list( $args, $assoc_args ) {

		$datasets = woo_st_get_dataset_types();


		if( !empty( $datasets ) ) {
			$format = 'table';
			$items = array();
			foreach( $datasets as $dataset ) {
				$items[] = array(
					'type' => $dataset,
					'count' => woo_st_return_count( $dataset )
				);
			}
			$fields = array( 'type', 'count' );
			WP_CLI\Utils\format_items( $format, $items, $fields );
			exit();
		} else {
			WP_CLI::error( 'No supported Nuke Types were found...' );
			exit();
		}

	}

  /**
   * Trigger a Nuke.
   *
   * ## OPTIONS
   *
   * [--type=<type>]
   * : Accepted values: product, category, tag, order, coupon, shipping_class, woocommerce_log. Default: empty
   *
   * ## EXAMPLES
   *
   *     wp store-export nuke
   *     wp store-export nuke --type=woocommerce_log
   *
   * @subcommand nuke
   */
	function nuke( $args, $assoc_args ) {

		$type = ( !empty( $assoc_args['type'] ) ? $assoc_args['type'] : false );
		// Check if a Type has been provided
		if( empty( $type ) ) {
			WP_CLI::error( 'No Type was provided...' );
			exit();
		}

		// Check if the Type is valid
		$datasets = woo_st_get_dataset_types();
		if( !in_array( $type, $datasets ) ) {
			WP_CLI::error( 'A valid Nuke Type was not provided...' );
			exit();
		}

		WP_CLI::line( sprintf( 'Running Nuke on Type: %s...', $type ) );

		$start_time = time();

		woo_st_clear_dataset( $type );

		$end_time = time();
		$time_taken = ( $end_time - $start_time );

		WP_CLI::success( sprintf( 'Nuke has completed. Time taken: %s second(s)', $time_taken ) );
		exit();

	}

}
WP_CLI::add_command( 'store-toolkit', 'Store_Toolkit_Command' );
?>