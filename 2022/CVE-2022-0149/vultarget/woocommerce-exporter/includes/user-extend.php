<?php
// Adds custom User columns to the User fields list
function woo_ce_extend_user_fields( $fields = array() ) {

	// WooCommerce Hear About Us - https://wordpress.org/plugins/woocommerce-hear-about-us/
	if( class_exists( 'WooCommerce_HearAboutUs' ) ) {
		$fields[] = array(
			'name' => 'hear_about_us',
			'label' => __( 'Source', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Hear About Us', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce User fields
	if( class_exists( 'WC_Admin_Profile' ) ) {
		$admin_profile = new WC_Admin_Profile();
		if( method_exists( 'WC_Admin_Profile', 'get_customer_meta_fields' ) ) {
			$show_fields = $admin_profile->get_customer_meta_fields();
			foreach( $show_fields as $fieldset ) {
				foreach( $fieldset['fields'] as $key => $field ) {
					$fields[] = array(
						'name' => $key,
						'label' => sprintf( apply_filters( 'woo_ce_extend_user_fields_wc', '%s: %s' ), $fieldset['title'], esc_html( $field['label'] ) ),
						'disabled' => 1
					);
				}
			}
			unset( $show_fields, $fieldset, $field );
		}
	}

	// WC Vendors - http://wcvendors.com
	if( class_exists( 'WC_Vendors' ) ) {
		$fields[] = array(
			'name' => 'shop_name',
			'label' => __( 'Shop Name' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'shop_slug',
			'label' => __( 'Shop Slug' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'paypal_email',
			'label' => __( 'PayPal E-mail' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'commission_rate',
			'label' => __( 'Commission Rate (%)' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'seller_info',
			'label' => __( 'Seller Info' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'shop_description',
			'label' => __( 'Shop Description' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Subscriptions - http://www.woothemes.com/products/woocommerce-subscriptions/
	if( class_exists( 'WC_Subscriptions_Manager' ) ) {
		$fields[] = array(
			'name' => 'active_subscriber',
			'label' => __( 'Active Subscriber' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// Custom User meta
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			if( !empty( $custom_user ) ) {
				$fields[] = array(
					'name' => $custom_user,
					'label' => $custom_user,
					'disabled' => 1
				);
			}
		}
	}
	unset( $custom_users, $custom_user );

	return $fields;

}
add_filter( 'woo_ce_user_fields', 'woo_ce_extend_user_fields' );