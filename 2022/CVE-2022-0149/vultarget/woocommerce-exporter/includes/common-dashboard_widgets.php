<?php
/*

Filename: common-dashboard_widgets.php
Description: common-dashboard_widgets.php loads commonly access Dashboard widgets across the Visser Labs suite.
Version: 1.5

*/

/* Start of: WooCommerce News - by Visser Labs */

if( !function_exists( 'woo_vl_dashboard_setup' ) ) {

	function woo_vl_dashboard_setup() {

		// Limit the Dashboard widget to Users with the Manage Options capability
		$user_capability = 'manage_options';
		if( current_user_can( $user_capability ) ) {
			if( apply_filters( 'woo_vl_news_widget', true ) ) {
				$dashboard_widget_title = __( 'Plugin News - by Visser Labs', 'woocommerce-exporter' );
				wp_add_dashboard_widget( 'woo_vl_news_widget', $dashboard_widget_title, 'woo_vl_news_widget' );
			}
		}

	}
	add_action( 'wp_dashboard_setup', 'woo_vl_dashboard_setup' );

	function woo_vl_news_widget() {

		include_once( ABSPATH . WPINC . '/feed.php' );

		// Get the RSS feed for WooCommerce Plugins
		$rss = fetch_feed( 'http://www.visser.com.au/blog/category/woocommerce/feed/' );
		$output = '<div class="rss-widget">';
		if( !is_wp_error( $rss ) ) {
			$maxitems = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
			$output .= '<ul>';
			foreach ( $rss_items as $item ) :
				$output .= '<li>';
				$output .= '<a href="' . $item->get_permalink() . '" title="' . 'Posted ' . $item->get_date( 'j F Y | g:i a' ) . '" class="rsswidget">' . $item->get_title() . '</a>';
				$output .= '<span class="rss-date">' . $item->get_date( 'j F, Y' ) . '</span>';
				$output .= '<div class="rssSummary">' . $item->get_description() . '</div>';
				$output .= '</li>';
			endforeach;
			$output .= '</ul>';
		} else {
			$message = __( 'Connection failed. Please check your network settings.', 'woocommerce-exporter' );
			$output .= '<p>' . $message . '</p>';
		}
		$output .= '</div>';

		echo $output;

	}

}

/* End of: WooCommerce News - by Visser Labs */