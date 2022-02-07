<?php

namespace Never5\DownloadMonitor\Shop\Email;

use Never5\DownloadMonitor\Shop\Services\Services;

class VarParser {

	/**
	 * Parse (find and replace) all email variables
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 * @param string $body
	 *
	 * @return string
	 */
	public function parse( $order, $body ) {

		$body = str_ireplace( "%FIRST_NAME%", $order->get_customer()->get_first_name(), $body );

		$body = str_ireplace( "%DOWNLOADS_TABLE%", $this->generate_download_table( $order ), $body );
		$body = str_ireplace( "%DOWNLOADS_TABLE_PLAIN%", $this->generate_download_table_plain( $order ), $body );

		$body = str_ireplace( "%ORDER_TABLE%", $this->generate_order_table( $order ), $body );
		$body = str_ireplace( "%ORDER_TABLE_PLAIN%", $this->generate_order_table_plain( $order ), $body );


		$body = str_ireplace( "%WEBSITE_NAME%", get_bloginfo( 'name' ), $body );

		$body = apply_filters( 'dlm_shop_email_var_parser', $body );

		return $body;
	}

	/**
	 * Build a simplified order items array, used in email template files
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return array
	 */
	private function build_simplified_order_items_array( $order ) {
		$order_items = $order->get_items();
		$html_items  = array();

		if ( count( $order_items ) > 0 ) {

			foreach ( $order_items as $order_item ) {

				try {
					/** @var \Never5\DownloadMonitor\Shop\Product\Product $product */
					$product = Services::get()->service( 'product_repository' )->retrieve_single( $order_item->get_product_id() );

					$html_item = array(
						'label'     => $product->get_title(),
						'downloads' => array()
					);

					$downloads = $product->get_downloads();
					if ( ! empty( $downloads ) ) {

						foreach ( $downloads as $download ) {

							$version_label        = "-";
							$download_button_html = __( 'Download is no longer available', 'download-monitor' );
							$download_url         = "";

							if ( $download->exists() ) {
								$version_label        = $download->get_version()->get_version();
								$download_button_html = "<a href='" . $product->get_secure_download_link( $order, $download ) . "' class='dlm-download-button'>" . __( 'Download File', 'download-monitor' ) . "</a>";
								$download_url         = $product->get_secure_download_link( $order, $download );
							}

							$html_item['downloads'][] = array(
								'label'        => $download->get_title(),
								'version'      => $version_label,
								'button'       => $download_button_html,
								'download_url' => $download_url
							);
						}
					}

					$html_items[] = $html_item;


				} catch ( \Exception $e ) {
				}

			}

		}

		return $html_items;
	}

	/**
	 * Build a simplified order data array, used in email template files
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return array
	 */
	private function build_simplified_order_data_array( $order ) {

		$processor    = "";
		$transactions = $order->get_transactions();
		if ( count( $transactions ) > 0 ) {
			/** @var \Never5\DownloadMonitor\Shop\Order\Transaction\OrderTransaction $transaction */
			foreach ( $transactions as $transaction ) {
				if ( 'success' === $transaction->get_status()->get_key() ) {
					$processor = $transaction->get_processor_nice_name();
				}
			}
		}

		$data = array(
			array(
				'key'   => __( 'Order ID' ),
				'value' => $order->get_id()
			),
			array(
				'key'   => __( 'Order Date' ),
				'value' => date_i18n( get_option( 'date_format' ), $order->get_date_created()->getTimestamp() )
			),
			array(
				'key'   => __( 'Order Total' ),
				'value' => dlm_format_money( $order->get_total() )
			)
		);

		if ( ! empty( $processor ) ) {
			$data[] = array(
				'key'   => __( 'Payment Gateway' ),
				'value' => $processor
			);
		}

		return $data;
	}

	/**
	 * Generate the table with downloads customer just purchased
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return string
	 */
	private function generate_download_table( $order ) {

		ob_start();
		download_monitor()->service( 'template_handler' )->get_template_part( 'shop/email/elements/downloads-table', '', '', array(
			'products' => $this->build_simplified_order_items_array( $order )
		) );
		$output = ob_get_clean();


		return $output;
	}

	/**
	 * Generate a plain text overview with downloads customer just purchased
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return string
	 */
	private function generate_download_table_plain( $order ) {
		ob_start();
		download_monitor()->service( 'template_handler' )->get_template_part( 'shop/email/elements/downloads-table-plain', '', '', array(
			'products' => $this->build_simplified_order_items_array( $order )
		) );
		$output = ob_get_clean();


		return $output;
	}

	/**
	 * Generate the table with downloads customer just purchased
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return string
	 */
	private function generate_order_table( $order ) {

		ob_start();
		download_monitor()->service( 'template_handler' )->get_template_part( 'shop/email/elements/order-table', '', '', array(
			'items' => $this->build_simplified_order_data_array( $order )
		) );
		$output = ob_get_clean();


		return $output;
	}

	/**
	 * Generate the plain text overview with downloads customer just purchased
	 *
	 * @param \Never5\DownloadMonitor\Shop\Order\Order $order
	 *
	 * @return string
	 */
	private function generate_order_table_plain( $order ) {

		ob_start();
		download_monitor()->service( 'template_handler' )->get_template_part( 'shop/email/elements/order-table-plain', '', '', array(
			'items' => $this->build_simplified_order_data_array( $order )
		) );
		$output = ob_get_clean();


		return $output;
	}

}

