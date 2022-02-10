<?php
// Adds custom Order columns to the Order fields list
function woo_ce_extend_order_fields( $fields = array() ) {

	// Product Add-ons - http://www.woothemes.com/
	if( woo_ce_detect_export_plugin( 'product_addons' ) ) {
		$fields[] = array(
			'name' => 'order_items_product_addons_summary',
			'label' => __( 'Order Items: Product Add-ons', 'woocommerce-exporter' ),
			'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_product_addons_summary', '%s' ), __( 'Product Add-ons', 'woocommerce-exporter' ) )
		);
		$product_addons = woo_ce_get_product_addons();
		if( !empty( $product_addons ) ) {
			foreach( $product_addons as $product_addon ) {
				if( !empty( $product_addon ) ) {
					$fields[] = array(
						'name' => sprintf( 'order_items_product_addon_%s', sanitize_key( $product_addon->post_name ) ),
						'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), ucfirst( $product_addon->post_title ) ),
						'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_product_addons', '%s: %s' ), __( 'Product Add-ons', 'woocommerce-exporter' ), $product_addon->form_title )
					);
				}
			}
		}
		unset( $product_addons, $product_addon );
	}

	// WooCommerce Sequential Order Numbers - http://www.skyverge.com/blog/woocommerce-sequential-order-numbers/
	// Sequential Order Numbers Pro - http://www.woothemes.com/products/sequential-order-numbers-pro/
	if( woo_ce_detect_export_plugin( 'seq' ) || woo_ce_detect_export_plugin( 'seq_pro' ) ) {
		$fields[] = array(
			'name' => 'order_number',
			'label' => __( 'Order Number', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Print Invoice & Delivery Note - https://wordpress.org/plugins/woocommerce-delivery-notes/
	if( woo_ce_detect_export_plugin( 'print_invoice_delivery_note' ) ) {
		$fields[] = array(
			'name' => 'invoice_number',
			'label' => __( 'Invoice Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Print Invoice & Delivery Note', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'invoice_date',
			'label' => __( 'Invoice Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Print Invoice & Delivery Note', 'woocommerce-exporter' )
		);
	}

	// WooCommerce PDF Invoices & Packing Slips - http://www.wpovernight.com
	if( woo_ce_detect_export_plugin( 'pdf_invoices_packing_slips' ) ) {
		$fields[] = array(
			'name' => 'pdf_invoice_number',
			'label' => __( 'PDF Invoice Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce PDF Invoices & Packing Slips', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'pdf_invoice_date',
			'label' => __( 'PDF Invoice Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce PDF Invoices & Packing Slips', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Germanized Pro - https://www.vendidero.de/woocommerce-germanized
	if( woo_ce_detect_export_plugin( 'wc_germanized_pro' ) ) {
		$fields[] = array(
			'name' => 'invoice_number',
			'label' => __( 'Invoice Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'invoice_number_formatted',
			'label' => __( 'Invoice Number (Formatted)', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'invoice_status',
			'label' => __( 'Invoice Status', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Hear About Us - https://wordpress.org/plugins/woocommerce-hear-about-us/
	if( woo_ce_detect_export_plugin( 'hear_about_us' ) ) {
		$fields[] = array(
			'name' => 'hear_about_us',
			'label' => __( 'Source', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Hear About Us', 'woocommerce-exporter' )
		);
	}

	// Order Delivery Date for WooCommerce - https://wordpress.org/plugins/order-delivery-date-for-woocommerce/
	// Order Delivery Date Pro for WooCommerce - https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/
	if( woo_ce_detect_export_plugin( 'orddd_free' ) || woo_ce_detect_export_plugin( 'orddd' ) ) {
		$fields[] = array(
			'name' => 'delivery_date',
			'label' => __( 'Delivery Date', 'woocommerce-exporter' ),
			'hover' => ( woo_ce_detect_export_plugin( 'orddd' ) ? __( 'Order Delivery Date Pro for WooCommerce', 'woocommerce-exporter' ) : __( 'Order Delivery Date for WooCommerce', 'woocommerce-exporter' ) )
		);
	}

	// WooCommerce Memberships - http://www.woothemes.com/products/woocommerce-memberships/
	if( woo_ce_detect_export_plugin( 'wc_memberships' ) ) {
		$fields[] = array(
			'name' => 'active_memberships',
			'label' => __( 'Active Memberships', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Memberships', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Uploads - https://wpfortune.com/shop/plugins/woocommerce-uploads/
	if( woo_ce_detect_export_plugin( 'wc_uploads' ) ) {
		$fields[] = array(
			'name' => 'uploaded_files',
			'label' => __( 'Uploaded Files', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Uploads', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'uploaded_files_thumbnail',
			'label' => __( 'Uploaded Files (Thumbnail)', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Uploads', 'woocommerce-exporter' )
		);
	}

	// WPML - https://wpml.org/
	// WooCommerce Multilingual - https://wordpress.org/plugins/woocommerce-multilingual/
	if( woo_ce_detect_wpml() && woo_ce_detect_export_plugin( 'wpml_wc' ) ) {
		$fields[] = array(
			'name' => 'language',
			'label' => __( 'Language', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Multilingual', 'woocommerce-exporter' )
		);
	}

	// WooCommerce EAN Payment Gateway - http://plugins.yanco.dk/woocommerce-ean-payment-gateway
	if( woo_ce_detect_export_plugin( 'wc_ean' ) ) {
		$fields[] = array(
			'name' => 'ean_number',
			'label' => __( 'EAN Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EAN Payment Gateway', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Checkout Manager - http://wordpress.org/plugins/woocommerce-checkout-manager/
	// WooCommerce Checkout Manager Pro - http://wordpress.org/plugins/woocommerce-checkout-manager/
	if( woo_ce_detect_export_plugin( 'checkout_manager' ) ) {

		// Checkout Manager stores its settings in mulitple suffixed wccs_settings WordPress Options

		// Load generic settings
		$options = get_option( 'wccs_settings' );
		if( isset( $options['buttons'] ) ) {
			$buttons = $options['buttons'];
			if( !empty( $buttons ) ) {
				$header = ( $buttons[0]['type'] == 'heading' ? $buttons[0]['label'] : __( 'Additional', 'woocommerce-exporter' ) );
				foreach( $buttons as $button ) {
					// Skip headings
					if( $button['type'] == 'heading' )
						continue;
					$label = ( !empty( $button['label'] ) ? $button['label'] : $button['cow'] );
					$fields[] = array(
						'name' => sprintf( 'additional_%s', $button['cow'] ),
						'label' => ( !empty( $header ) ? sprintf( apply_filters( 'woo_ce_extend_order_fields_wccs', '%s: %s' ), ucfirst( $header ), ucfirst( $label ) ) : ucfirst( $label ) ),
						'hover' => __( 'WooCommerce Checkout Manager', 'woocommerce-exporter' )
					);
				}
				unset( $buttons, $button, $header, $label );
			}
		}
		unset( $options );

		// Load Shipping settings
		$options = get_option( 'wccs_settings2' );
		if( isset( $options['shipping_buttons'] ) ) {
			$buttons = $options['shipping_buttons'];
			if( !empty( $buttons ) ) {
				$header = ( $buttons[0]['type'] == 'heading' ? $buttons[0]['label'] : __( 'Shipping', 'woocommerce-exporter' ) );
				foreach( $buttons as $button ) {
					// Skip headings
					if( $button['type'] == 'heading' )
						continue;
					$wccs_field_duplicate = false;
					// Check if this isn't a duplicate Checkout Manager Pro field
					foreach( $fields as $field ) {
						if( isset( $field['name'] ) && $field['name'] == sprintf( 'shipping_%s', $button['cow'] ) ) {
							// Duplicate exists
							$wccs_field_duplicate = true;
							break;
						}
					}
					// If it's not a duplicate go ahead and add it to the list
					if( $wccs_field_duplicate !== true ) {
						$label = ( !empty( $button['label'] ) ? $button['label'] : $button['cow'] );
						$fields[] = array(
							'name' => sprintf( 'shipping_%s', $button['cow'] ),
							'label' => ( !empty( $header ) ? sprintf( apply_filters( 'woo_ce_extend_order_fields_wccs', '%s: %s' ), ucfirst( $header ), ucfirst( $label ) ) : ucfirst( $label ) ),
							'hover' => __( 'WooCommerce Checkout Manager', 'woocommerce-exporter' )
						);
					}
					unset( $wccs_field_duplicate );
				}
				unset( $buttons, $button, $header, $label );
			}
		}
		unset( $options );

		// Load Billing settings
		$options = get_option( 'wccs_settings3' );
		if( isset( $options['billing_buttons'] ) ) {
			$buttons = $options['billing_buttons'];
			if( !empty( $buttons ) ) {
				$header = ( $buttons[0]['type'] == 'heading' ? $buttons[0]['label'] : __( 'Billing', 'woocommerce-exporter' ) );
				foreach( $buttons as $button ) {
					// Skip headings
					if( $button['type'] == 'heading' )
						continue;
					$wccs_field_duplicate = false;
					// Check if this isn't a duplicate Checkout Manager Pro field
					foreach( $fields as $field ) {
						if( isset( $field['name'] ) && $field['name'] == sprintf( 'billing_%s', $button['cow'] ) ) {
							// Duplicate exists
							$wccs_field_duplicate = true;
							break;
						}
					}
					// If it's not a duplicate go ahead and add it to the list
					if( $wccs_field_duplicate !== true ) {
						$label = ( !empty( $button['label'] ) ? $button['label'] : $button['cow'] );
						$fields[] = array(
							'name' => sprintf( 'billing_%s', $button['cow'] ),
							'label' => ( !empty( $header ) ? sprintf( apply_filters( 'woo_ce_extend_order_fields_wccs', '%s: %s' ), ucfirst( $header ), ucfirst( $label ) ) : ucfirst( $label ) ),
							'hover' => __( 'WooCommerce Checkout Manager', 'woocommerce-exporter' )
						);
					}
					unset( $wccs_field_duplicate );
				}
				unset( $buttons, $button, $header, $label );
			}
		}
		unset( $options );

	}

	// Poor Guys Swiss Knife - http://wordpress.org/plugins/woocommerce-poor-guys-swiss-knife/
	if( woo_ce_detect_export_plugin( 'wc_pgsk' ) ) {
		$options = get_option( 'wcpgsk_settings' );
		$billing_fields = ( isset( $options['woofields']['billing'] ) ? $options['woofields']['billing'] : array() );
		$shipping_fields = ( isset( $options['woofields']['shipping'] ) ? $options['woofields']['shipping'] : array() );

		// Custom billing fields
		if( !empty( $billing_fields ) ) {
			foreach( $billing_fields as $key => $billing_field ) {
				$fields[] = array(
					'name' => $key,
					'label' => $options['woofields'][sprintf( 'label_%s', $key )],
					'hover' => __( 'Poor Guys Swiss Knife', 'woocommerce-exporter' )
				);
			}
			unset( $billing_fields, $billing_field );
		}

		// Custom shipping fields
		if( !empty( $shipping_fields ) ) {
			foreach( $shipping_fields as $key => $shipping_field ) {
				$fields[] = array(
					'name' => $key,
					'label' => $options['woofields'][sprintf( 'label_%s', $key )],
					'hover' => __( 'Poor Guys Swiss Knife', 'woocommerce-exporter' )
				);
			}
			unset( $shipping_fields, $shipping_field );
		}

		unset( $options );
	}

	// Checkout Field Editor - http://woothemes.com/woocommerce/
	if( woo_ce_detect_export_plugin( 'checkout_field_editor' ) ) {
		$billing_fields = get_option( 'wc_fields_billing', array() );
		$shipping_fields = get_option( 'wc_fields_shipping', array() );
		$additional_fields = get_option( 'wc_fields_additional', array() );

		// Custom billing fields
		if( !empty( $billing_fields ) ) {
			foreach( $billing_fields as $key => $billing_field ) {
				// Only add non-default Checkout fields to export columns list
				if( isset( $billing_field['custom'] ) && $billing_field['custom'] == 1 ) {
					$fields[] = array(
						'name' => sprintf( 'wc_billing_%s', $key ),
						'label' => sprintf( __( 'Billing: %s', 'woocommerce-exporter' ), ucfirst( $billing_field['label'] ) ),
						'hover' => __( 'Checkout Field Editor', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $billing_fields, $billing_field );

		// Custom shipping fields
		if( !empty( $shipping_fields ) ) {
			foreach( $shipping_fields as $key => $shipping_field ) {
				// Only add non-default Checkout fields to export columns list
				if( isset( $shipping_field['custom'] ) && $shipping_field['custom'] == 1 ) {
					$fields[] = array(
						'name' => sprintf( 'wc_shipping_%s', $key ),
						'label' => sprintf( __( 'Shipping: %s', 'woocommerce-exporter' ), ucfirst( $shipping_field['label'] ) ),
						'hover' => __( 'Checkout Field Editor', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $shipping_fields, $shipping_field );

		// Additional fields
		if( !empty( $additional_fields ) ) {
			foreach( $additional_fields as $key => $additional_field ) {
				// Only add non-default Checkout fields to export columns list
				if( isset( $additional_field['custom'] ) && $additional_field['custom'] == 1 ) {
					$fields[] = array(
						'name' => sprintf( 'wc_additional_%s', $key ),
						'label' => sprintf( __( 'Additional: %s', 'woocommerce-exporter' ), ucfirst( $additional_field['label'] ) ),
						'hover' => __( 'Checkout Field Editor', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $additional_fields, $additional_field );
	}

	// Checkout Field Manager - http://61extensions.com
	if( woo_ce_detect_export_plugin( 'checkout_field_manager' ) ) {
		$billing_fields = get_option( 'woocommerce_checkout_billing_fields', array() );
		$shipping_fields = get_option( 'woocommerce_checkout_shipping_fields', array() );
		$custom_fields = get_option( 'woocommerce_checkout_additional_fields', array() );

		// Custom billing fields
		if( !empty( $billing_fields ) ) {
			foreach( $billing_fields as $key => $billing_field ) {
				// Only add non-default Checkout fields to export columns list
				if( strtolower( $billing_field['default_field'] ) != 'on' ) {
					$fields[] = array(
						'name' => sprintf( 'sod_billing_%s', $billing_field['name'] ),
						'label' => sprintf( __( 'Billing: %s', 'woocommerce-exporter' ), ucfirst( $billing_field['label'] ) ),
						'hover' => __( 'Checkout Field Manager', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $billing_fields, $billing_field );

		// Custom shipping fields
		if( !empty( $shipping_fields ) ) {
			foreach( $shipping_fields as $key => $shipping_field ) {
				// Only add non-default Checkout fields to export columns list
				if( strtolower( $shipping_field['default_field'] ) != 'on' ) {
					$fields[] = array(
						'name' => sprintf( 'sod_shipping_%s', $shipping_field['name'] ),
						'label' => sprintf( __( 'Shipping: %s', 'woocommerce-exporter' ), ucfirst( $shipping_field['label'] ) ),
						'hover' => __( 'Checkout Field Manager', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $shipping_fields, $shipping_field );

		// Custom fields
		if( !empty( $custom_fields ) ) {
			foreach( $custom_fields as $key => $custom_field ) {
				// Only add non-default Checkout fields to export columns list
				if( strtolower( $custom_field['default_field'] ) != 'on' ) {
					$fields[] = array(
						'name' => sprintf( 'sod_additional_%s', $custom_field['name'] ),
						'label' => sprintf( __( 'Additional: %s', 'woocommerce-exporter' ), ucfirst( $custom_field['label'] ) ),
						'hover' => __( 'Checkout Field Manager', 'woocommerce-exporter' )
					);
				}
			}
		}
		unset( $custom_fields, $custom_field );
	}

	// WooCommerce Extra Checkout Fields for Brazil - https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/
	if( woo_ce_detect_export_plugin( 'wc_extra_checkout_fields_brazil' ) ) {
		$fields[] = array(
			'name' => 'billing_cpf',
			'label' => __( 'Billing: CPF', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_rg',
			'label' => __( 'Billing: RG', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_cnpj',
			'label' => __( 'Billing: CNPJ', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_ie',
			'label' => __( 'Billing: IE', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_birthdate',
			'label' => __( 'Billing: Birth Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_sex',
			'label' => __( 'Billing: Sex', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_number',
			'label' => __( 'Billing: Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_neighborhood',
			'label' => __( 'Billing: Neighborhood', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'billing_cellphone',
			'label' => __( 'Billing: Cell Phone', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'shipping_number',
			'label' => __( 'Shipping: Number', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'shipping_neighborhood',
			'label' => __( 'Shipping: Neighborhood', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-exporter' )
		);
	}

	// YITH WooCommerce Checkout Manager - https://yithemes.com/themes/plugins/yith-woocommerce-checkout-manager/
	if( woo_ce_detect_export_plugin( 'yith_cm' ) ) {
		// YITH WooCommerce Checkout Manager stores its settings in separate Options
		$billing_options = get_option( 'ywccp_fields_billing_options' );
		$shipping_options = get_option( 'ywccp_fields_shipping_options' );
		$additional_options = get_option( 'ywccp_fields_additional_options' );

		// Custom billing fields
		if( !empty( $billing_options ) ) {
			// Only add non-default Checkout fields to export columns list
			$default_keys = ywccp_get_default_fields_key( 'billing' );
			$fields_keys = array_keys( $billing_options );
			$billing_fields = array_diff( $fields_keys, $default_keys );
			if( !empty( $billing_fields ) ) {
				foreach( $billing_fields as $billing_field ) {
					// Check that the custom Billing field exists
					if( isset( $billing_options[$billing_field] ) ) {
						// Skip headings
						if( $billing_options[$billing_field]['type'] == 'heading' )
							continue;
						$fields[] = array(
							'name' => sprintf( 'ywccp_%s', sanitize_key( $billing_field ) ),
							'label' => sprintf( __( 'Billing: %s', 'woocommerce-exporter' ), ( !empty( $billing_options[$billing_field]['label'] ) ? $billing_options[$billing_field]['label'] : str_replace( 'billing_', '', $billing_field ) ) ),
							'hover' => __( 'YITH WooCommerce Checkout Manager', 'woocommerce-exporter' )
						);
					}
				}
			}
			unset( $fields_keys, $default_keys, $billing_fields, $billing_field );
		}
		unset( $billing_options );

		// Custom shipping fields
		if( !empty( $shipping_options ) ) {
			// Only add non-default Checkout fields to export columns list
			$default_keys = ywccp_get_default_fields_key( 'shipping' );
			$fields_keys = array_keys( $shipping_options );
			$shipping_fields = array_diff( $fields_keys, $default_keys );
			if( !empty( $shipping_fields ) ) {
				foreach( $shipping_fields as $shipping_field ) {
					// Check that the custom Shipping field exists
					if( isset( $shipping_options[$shipping_field] ) ) {
						// Skip headings
						if( $shipping_options[$shipping_field]['type'] == 'heading' )
							continue;
						$fields[] = array(
							'name' => sprintf( 'ywccp_%s', sanitize_key( $shipping_field ) ),
							'label' => sprintf( __( 'Shipping: %s', 'woocommerce-exporter' ), ( !empty( $shipping_options[$shipping_field]['label'] ) ? $shipping_options[$shipping_field]['label'] : str_replace( 'shipping_', '', $shipping_field ) ) ),
							'hover' => __( 'YITH WooCommerce Checkout Manager', 'woocommerce-exporter' )
						);
					}
				}
			}
			unset( $fields_keys, $default_keys, $shipping_fields, $shipping_field );
		}
		unset( $shipping_options );

		// Custom additional fields
		if( !empty( $additional_options ) ) {
			// Only add non-default Checkout fields to export columns list
			$default_keys = ywccp_get_default_fields_key( 'additional' );
			$fields_keys = array_keys( $additional_options );
			$additional_fields = array_diff( $fields_keys, $default_keys );
			if( !empty( $additional_fields ) ) {
				foreach( $additional_fields as $additional_field ) {
					// Check that the custom Additional field exists
					if( isset( $additional_options[$additional_field] ) ) {
						// Skip headings
						if( $additional_options[$additional_field]['type'] == 'heading' )
							continue;
						$fields[] = array(
							'name' => sprintf( 'ywccp_%s', sanitize_key( $additional_field ) ),
							'label' => sprintf( __( 'Additional: %s', 'woocommerce-exporter' ), ( !empty( $additional_options[$additional_field]['label'] ) ? $additional_options[$additional_field]['label'] : str_replace( 'additional_', '', $additional_field ) ) ),
							'hover' => __( 'YITH WooCommerce Checkout Manager', 'woocommerce-exporter' )
						);
					}
				}
			}
			unset( $fields_keys, $default_keys, $additional_fields, $additional_field );
		}
		unset( $additional_options );

	}

	// WooCommerce Subscriptions - http://www.woothemes.com/products/woocommerce-subscriptions/
	if( woo_ce_detect_export_plugin( 'subscriptions' ) ) {
		$fields[] = array(
			'name' => 'order_type',
			'label' => __( 'Subscription Relationship', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_renewal',
			'label' => __( 'Subscription Renewal', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_resubscribe',
			'label' => __( 'Subscription Resubscribe', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_switch',
			'label' => __( 'Subscription Switch', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Quick Donation - http://wordpress.org/plugins/woocommerce-quick-donation/
	if( woo_ce_detect_export_plugin( 'wc_quickdonation' ) ) {
		$fields[] = array(
			'name' => 'project_id',
			'label' => __( 'Project ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Quick Donation', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'project_name',
			'label' => __( 'Project Name', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Quick Donation', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Easy Checkout Fields Editor - http://codecanyon.net/item/woocommerce-easy-checkout-field-editor/9799777
	if( woo_ce_detect_export_plugin( 'wc_easycheckout' ) ) {
		$custom_fields = get_option( 'pcfme_additional_settings' );
		if( !empty( $custom_fields ) ) {
			foreach( $custom_fields as $key => $custom_field ) {
				$fields[] = array(
					'name' => $key,
					'label' => sprintf( __( 'Additional: %s', 'woocommerce-exporter' ), ucfirst( $custom_field['label'] ) ),
					'hover' => __( 'WooCommerce Easy Checkout Fields Editor', 'woocommerce-exporter' )
				);
			}
			unset( $custom_fields, $custom_field );
		}
	}

	// WooCommerce Events - http://www.woocommerceevents.com/
	if( woo_ce_detect_export_plugin( 'wc_events' ) ) {
		$fields[] = array(
			'name' => 'tickets_purchased',
			'label' => __( 'Tickets Purchased', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Currency Switcher - http://dev.pathtoenlightenment.net/shop
	if( woo_ce_detect_export_plugin( 'currency_switcher' ) ) {
		$fields[] = array(
			'name' => 'order_currency',
			'label' => __( 'Order Currency', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Currency Switcher', 'woocommerce-exporter' )
		);
	}

	// WooCommerce EU VAT Number - https://www.woothemes.com/products/eu-vat-number/
	if( woo_ce_detect_export_plugin( 'eu_vat' ) ) {
		$fields[] = array(
			'name' => 'eu_vat',
			'label' => __( 'VAT ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Number', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_validated',
			'label' => __( 'VAT ID Validated', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Number', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_b2b',
			'label' => __( 'VAT B2B Transaction', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Number', 'woocommerce-exporter' )
		);
	}

	// WooCommerce EU VAT Assistant - https://wordpress.org/plugins/woocommerce-eu-vat-assistant/
	if( woo_ce_detect_export_plugin( 'aelia_eu_vat' ) ) {
		$fields[] = array(
			'name' => 'eu_vat',
			'label' => __( 'VAT ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Assistant', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_country',
			'label' => __( 'VAT ID Country', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Assistant', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_validated',
			'label' => __( 'VAT ID Validated', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Assistant', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_b2b',
			'label' => __( 'VAT B2B Transaction', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Assistant', 'woocommerce-exporter' )
		);
	}

	// WooCommerce EU VAT Compliance - https://wordpress.org/plugins/woocommerce-eu-vat-compliance/
	// WooCommerce EU VAT Compliance (Premium) - https://www.simbahosting.co.uk/s3/product/woocommerce-eu-vat-compliance/
	if( woo_ce_detect_export_plugin( 'wc_eu_vat_compliance' ) || woo_ce_detect_export_plugin( 'wc_eu_vat_compliance_pro' ) ) {
		if( woo_ce_detect_export_plugin( 'wc_eu_vat_compliance_pro' ) ) {
			$fields[] = array(
				'name' => 'eu_vat',
				'label' => __( 'VAT ID', 'woocommerce-exporter' ),
				'hover' => __( 'WooCommerce EU VAT Compliance (Premium)', 'woocommerce-exporter' )
			);
			$fields[] = array(
				'name' => 'eu_vat_validated',
				'label' => __( 'VAT ID Validated', 'woocommerce-exporter' ),
				'hover' => __( 'WooCommerce EU VAT Compliance (Premium)', 'woocommerce-exporter' )
			);
			$fields[] = array(
				'name' => 'eu_vat_valid_id',
				'label' => __( 'Valid VAT ID', 'woocommerce-exporter' ),
				'hover' => __( 'WooCommerce EU VAT Compliance (Premium)', 'woocommerce-exporter' )
			);
		}
		$fields[] = array(
			'name' => 'eu_vat_country',
			'label' => __( 'VAT ID Country', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Compliance', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'eu_vat_country_source',
			'label' => __( 'VAT Country Source', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce EU VAT Compliance', 'woocommerce-exporter' )
		);
		if( woo_ce_detect_export_plugin( 'wc_eu_vat_compliance_pro' ) ) {
			$fields[] = array(
				'name' => 'eu_vat_b2b',
				'label' => __( 'VAT B2B Transaction', 'woocommerce-exporter' ),
				'hover' => __( 'WooCommerce EU VAT Compliance (Premium)', 'woocommerce-exporter' )
			);
		}
	}

	// WooCommerce Jetpack - https://wordpress.org/plugins/woocommerce-jetpack/
	// WooCommerce Jetpack Plus - http://woojetpack.com/shop/wordpress-woocommerce-jetpack-plus/
	if( woo_ce_detect_export_plugin( 'woocommerce_jetpack' ) || woo_ce_detect_export_plugin( 'woocommerce_jetpack_plus' ) ) {
		$fields[] = array(
			'name' => 'eu_vat',
			'label' => __( 'EU VAT Number', 'woocommerce-exporter' ),
			'hover' => __( 'Booster for WooCommerce', 'woocommerce-exporter' )
		);
	}

	// AweBooking - https://codecanyon.net/item/awebooking-online-hotel-booking-for-wordpress/12323878
	if( woo_ce_detect_export_plugin( 'awebooking' ) ) {
		$fields[] = array(
			'name' => 'arrival_date',
			'label' => __( 'Arrival Date', 'woocommerce-exporter' ),
			'hover' => __( 'AweBooking', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'departure_date',
			'label' => __( 'Departure Date', 'woocommerce-exporter' ),
			'hover' => __( 'AweBooking', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'adults',
			'label' => __( 'Adults', 'woocommerce-exporter' ),
			'hover' => __( 'AweBooking', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'children',
			'label' => __( 'Children', 'woocommerce-exporter' ),
			'hover' => __( 'AweBooking', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'room_type_id',
			'label' => __( 'Room Type ID', 'woocommerce-exporter' ),
			'hover' => __( 'AweBooking', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'room_type_name',
			'label' => __( 'Room Type Name', 'woocommerce-exporter' ),
			'hover' => __( 'AweBooking', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Custom Admin Order Fields - http://www.woothemes.com/products/woocommerce-admin-custom-order-fields/
	if( woo_ce_detect_export_plugin( 'admin_custom_order_fields' ) ) {
		$ac_fields = get_option( 'wc_admin_custom_order_fields' );
		if( !empty( $ac_fields ) ) {
			foreach( $ac_fields as $ac_key => $ac_field ) {
				$fields[] = array(
					'name' => sprintf( 'wc_acof_%d', $ac_key ),
					'label' => sprintf( __( 'Admin Custom Order Field: %s', 'woocommerce-exporter' ), $ac_field['label'] )
				);
			}
		}
		unset( $ac_fields, $ac_field, $ac_key );
	}

	// YITH WooCommerce Delivery Date Premium - http://yithemes.com/themes/plugins/yith-woocommerce-delivery-date/
	if( woo_ce_detect_export_plugin( 'yith_delivery_pro' ) ) {
		$fields[] = array(
			'name' => 'shipping_date',
			'label' => __( 'Shipping Date', 'woocommerce-exporter' ),
			'hover' => __( 'YITH WooCommerce Delivery Date Premium', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'delivery_date',
			'label' => __( 'Delivery Date', 'woocommerce-exporter' ),
			'hover' => __( 'YITH WooCommerce Delivery Date Premium', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'delivery_time_slot',
			'label' => __( 'Delivery Time Slot', 'woocommerce-exporter' ),
			'hover' => __( 'YITH WooCommerce Delivery Date Premium', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Point of Sale - https://codecanyon.net/item/woocommerce-point-of-sale-pos/7869665
	if( woo_ce_detect_export_plugin( 'wc_point_of_sales' ) ) {
		$fields[] = array(
			'name' => 'order_type',
			'label' => __( 'Order Type', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Point of Sale', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_register_id',
			'label' => __( 'Register ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Point of Sale', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_cashier',
			'label' => __( 'Cashier', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Point of Sale', 'woocommerce-exporter' )
		);
	}

	// WooCommerce PDF Product Vouchers - http://www.woothemes.com/products/pdf-product-vouchers/
	if( woo_ce_detect_export_plugin( 'wc_pdf_product_vouchers' ) ) {
		$fields[] = array(
			'name' => 'voucher_redeemed',
			'label' => __( 'Voucher Redeemed', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce PDF Product Vouchers', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Delivery Slots - https://iconicwp.com/products/woocommerce-delivery-slots/
	if( woo_ce_detect_export_plugin( 'wc_deliveryslots' ) ) {
		$fields[] = array(
			'name' => 'delivery_date',
			'label' => __( 'Delivery Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Delivery Slots', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'delivery_timeslot',
			'label' => __( 'Delivery Timeslot', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Delivery Slots', 'woocommerce-exporter' )
		);
	}
	
	// WooCommerce Ship to Multiple Addresses - http://woothemes.com/woocommerce
	if( woo_ce_detect_export_plugin( 'wc_ship_multiple' ) ) {
		$fields[] = array(
			'name' => 'wcms_number_packages',
			'label' => __( 'Number of Packages', 'woocommerce-exporter' ),
			'hover' => __( 'Ship to Multiple Addresses', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Deposits - https://woocommerce.com/products/woocommerce-deposits/
	if( woo_ce_detect_export_plugin( 'wc_deposits' ) ) {
		$fields[] = array(
			'name' => 'has_deposit',
			'label' => __( 'Has Deposit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'deposit_paid',
			'label' => __( 'Deposit Paid', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'deposit_second_payment_paid',
			'label' => __( 'Second Payment Paid', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'deposit_amount',
			'label' => __( 'Deposit Amount', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'deposit_second_payment',
			'label' => __( 'Second Payment Amount', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'deposit_original_total',
			'label' => __( 'Original Total', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' )
		);
	}

	// Tickera - https://tickera.com/
	if( woo_ce_detect_export_plugin( 'tickera' ) ) {
		$fields[] = array(
			'name' => 'ticket_id',
			'label' => __( 'Ticket ID', 'woocommerce-exporter' ),
			'hover' => __( 'Tickera', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'ticket_code',
			'label' => __( 'Ticket Code', 'woocommerce-exporter' ),
			'hover' => __( 'Tickera', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'ticket_type_id',
			'label' => __( 'Ticket Type ID', 'woocommerce-exporter' ),
			'hover' => __( 'Tickera', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'ticket_event_id',
			'label' => __( 'Ticket Event ID', 'woocommerce-exporter' ),
			'hover' => __( 'Tickera', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'ticket_first_name',
			'label' => __( 'Ticket First Name', 'woocommerce-exporter' ),
			'hover' => __( 'Tickera', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'ticket_last_name',
			'label' => __( 'Ticket Last Name', 'woocommerce-exporter' ),
			'hover' => __( 'Tickera', 'woocommerce-exporter' )
		);
		$tickera_fields = woo_ce_get_tickera_custom_fields();
		if( !empty( $tickera_fields ) ) {
			foreach( $tickera_fields as $tickera_field ) {
				$fields[] = array(
					'name' => sprintf( 'ticket_custom_%s', sanitize_key( $tickera_field['name'] ) ),
					'label' => sprintf( __( 'Ticket: %s', 'woocommerce-exporter' ), $tickera_field['label'] ),
					'hover' => __( 'Tickera', 'woocommerce-exporter' )
				);
			}
		}
		unset( $tickera_fields );

	}

	// WooCommerce Stripe Payment Gateway - https://wordpress.org/plugins/woocommerce-gateway-stripe/
	if( woo_ce_detect_export_plugin( 'wc_stripe' ) ) {
		$fields[] = array(
			'name' => 'stripe_customer_id',
			'label' => __( 'Stripe: Customer ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Stripe Payment Gateway', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'stripe_card_id',
			'label' => __( 'Stripe: Card ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Stripe Payment Gateway', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'stripe_charge_captured',
			'label' => __( 'Stripe: Charge Captured', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Stripe Payment Gateway', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'stripe_payment_id',
			'label' => __( 'Stripe: Payment ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Stripe Payment Gateway', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'stripe_fee',
			'label' => __( 'Stripe: Fee', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Stripe Payment Gateway', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'stripe_net_revenue',
			'label' => __( 'Stripe: Net Revenue from Stripe', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Stripe Payment Gateway', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( woo_ce_detect_export_plugin( 'wc_customfields' ) ) {
		if( get_option( 'wccf_migrated_to_20' ) ) {
			// Order Fields
			$custom_fields = woo_ce_get_wccf_order_fields();
			if( !empty( $custom_fields ) ) {
				foreach( $custom_fields as $custom_field ) {
					$label = get_post_meta( $custom_field->ID, 'label', true );
					$key = get_post_meta( $custom_field->ID, 'key', true );
					$fields[] = array(
						'name' => sprintf( 'wccf_of_%s', sanitize_key( $key ) ),
						'label' => ucfirst( $label ),
						'hover' => sprintf( '%s: %s (%s)', __( 'WooCommerce Custom Fields', 'woocommerce-exporter' ), __( 'Order Field', 'woocommerce-exporter' ), sanitize_key( $key ) )
					);
				}
			}
			unset( $custom_fields, $custom_field, $label, $key );
			// Checkout Fields
			$custom_fields = woo_ce_get_wccf_checkout_fields();
			if( !empty( $custom_fields ) ) {
				foreach( $custom_fields as $custom_field ) {
					$label = get_post_meta( $custom_field->ID, 'label', true );
					$key = get_post_meta( $custom_field->ID, 'key', true );
					$fields[] = array(
						'name' => sprintf( 'wccf_cf_%s', sanitize_key( $key ) ),
						'label' => ucfirst( $label ),
						'hover' => sprintf( '%s: %s (%s)', __( 'WooCommerce Custom Fields', 'woocommerce-exporter' ), __( 'Checkout Field', 'woocommerce-exporter' ), sanitize_key( $key ) )
					);
				}
			}
			unset( $custom_fields, $custom_field, $label, $key );
		}
	}

	// Custom User fields
	$custom_users = woo_ce_get_option( 'custom_users', '' );
	if( !empty( $custom_users ) ) {
		foreach( $custom_users as $custom_user ) {
			if( !empty( $custom_user ) ) {
				$fields[] = array(
					'name' => $custom_user,
					'label' => woo_ce_clean_export_label( $custom_user ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_custom_user_hover', '%s: %s' ), __( 'Custom User', 'woocommerce-exporter' ), $custom_user )
				);
			}
		}
	}
	unset( $custom_users, $custom_user );

	// Custom Order fields
	$custom_orders = woo_ce_get_option( 'custom_orders', '' );
	if( !empty( $custom_orders ) ) {
		foreach( $custom_orders as $custom_order ) {
			if( !empty( $custom_order ) ) {
				$fields[] = array(
					'name' => $custom_order,
					'label' => woo_ce_clean_export_label( $custom_order ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_custom_order_hover', '%s: %s' ), __( 'Custom Order', 'woocommerce-exporter' ), $custom_order )
				);
			}
		}
		unset( $custom_orders, $custom_order );
	}

	// Order Items go in woo_ce_extend_order_items_fields()

	return $fields;

}
add_filter( 'woo_ce_order_fields', 'woo_ce_extend_order_fields' );

// Adds custom Order Item columns to the Order Items fields list
function woo_ce_extend_order_items_fields( $fields = array() ) {

	// WooCommerce Checkout Add-Ons - http://www.skyverge.com/product/woocommerce-checkout-add-ons/
	if( woo_ce_detect_export_plugin( 'checkout_addons' ) ) {
		$fields[] = array(
			'name' => 'order_items_checkout_addon_id',
			'label' => __( 'Order Items: Checkout Add-ons ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Checkout Add-Ons', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_checkout_addon_label',
			'label' => __( 'Order Items: Checkout Add-ons Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Checkout Add-Ons', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_checkout_addon_value',
			'label' => __( 'Order Items: Checkout Add-ons Value', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Checkout Add-Ons', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	if( woo_ce_detect_product_brands() ) {
		$fields[] = array(
			'name' => 'order_items_brand',
			'label' => __( 'Order Items: Brand', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Brands or WooCommerce Brands Addon', 'woocommerce-exporter' )
		);
	}

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( woo_ce_detect_export_plugin( 'vendors' ) ) {
		$fields[] = array(
			'name' => 'order_items_vendor',
			'label' => __( 'Order Items: Product Vendor', 'woocommerce-exporter' ),
			'hover' => __( 'Product Vendors', 'woocommerce-exporter' )
		);
	}

	// YITH WooCommerce Multi Vendor Premium - http://yithemes.com/themes/plugins/yith-woocommerce-product-vendors/
	if( woo_ce_detect_export_plugin( 'yith_vendor' ) ) {
		$fields[] = array(
			'name' => 'order_items_vendor',
			'label' => __( 'Order Items: Product Vendor', 'woocommerce-exporter' ),
			'hover' => __( 'Product Vendors', 'woocommerce-exporter' )
		);
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( woo_ce_detect_export_plugin( 'wc_cog' ) ) {
		$fields[] = array(
			'name' => 'cost_of_goods',
			'label' => __( 'Order Total Cost of Goods', 'woocommerce-exporter' ),
			'hover' => __( 'Cost of Goods', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_cost_of_goods',
			'label' => __( 'Order Items: Cost of Goods', 'woocommerce-exporter' ),
			'hover' => __( 'Cost of Goods', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_total_cost_of_goods',
			'label' => __( 'Order Items: Total Cost of Goods', 'woocommerce-exporter' ),
			'hover' => __( 'Cost of Goods', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Profit of Sales Report - http://codecanyon.net/item/woocommerce-profit-of-sales-report/9190590
	if( woo_ce_detect_export_plugin( 'wc_posr' ) ) {
		$fields[] = array(
			'name' => 'order_items_posr',
			'label' => __( 'Order Items: Cost of Good', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Profit of Sales Report', 'woocommerce-exporter' )
		);
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( woo_ce_detect_export_plugin( 'wc_msrp' ) ) {
		$fields[] = array(
			'name' => 'order_items_msrp',
			'label' => __( 'Order Items: MSRP', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce MSRP Pricing', 'woocommerce-exporter' )
		);
	}

	// Local Pickup Plus - http://www.woothemes.com/products/local-pickup-plus/
	if( woo_ce_detect_export_plugin( 'local_pickup_plus' ) ) {
		$fields[] = array(
			'name' => 'order_items_pickup_location',
			'label' => __( 'Order Items: Pickup Location', 'woocommerce-exporter' ),
			'hover' => __( 'Local Pickup Plus', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( woo_ce_detect_export_plugin( 'woocommerce_bookings' ) ) {
		$fields[] = array(
			'name' => 'order_items_booking_id',
			'label' => __( 'Order Items: Booking ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_date',
			'label' => __( 'Order Items: Booking Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_type',
			'label' => __( 'Order Items: Booking Type', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_start_date',
			'label' => __( 'Order Items: Start Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_end_date',
			'label' => __( 'Order Items: End Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_all_day',
			'label' => __( 'Order Items: All Day Booking', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_resource_id',
			'label' => __( 'Order Items: Booking Resource ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_resource_title',
			'label' => __( 'Order Items: Booking Resource Name', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_persons',
			'label' => __( 'Order Items: Booking # of Persons', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
	}

	// Gravity Forms - http://woothemes.com/woocommerce
	if( woo_ce_detect_export_plugin( 'gravity_forms' ) && woo_ce_detect_export_plugin( 'woocommerce_gravity_forms' ) ) {
		// Check if there are any Products linked to Gravity Forms
		$gf_fields = woo_ce_get_gravity_forms_fields();
		if( !empty( $gf_fields ) ) {
			$fields[] = array(
				'name' => 'order_items_gf_form_id',
				'label' => __( 'Order Items: Gravity Form ID', 'woocommerce-exporter' ),
				'hover' => __( 'Gravity Forms', 'woocommerce-exporter' )
			);
			$fields[] = array(
				'name' => 'order_items_gf_form_label',
				'label' => __( 'Order Items: Gravity Form Label', 'woocommerce-exporter' ),
				'hover' => __( 'Gravity Forms', 'woocommerce-exporter' )
			);
			foreach( $gf_fields as $gf_field ) {
				$gf_field_duplicate = false;
				// Check if this isn't a duplicate Gravity Forms field
				foreach( $fields as $field ) {
					if( isset( $field['name'] ) && $field['name'] == sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] ) ) {
						// Duplicate exists
						$gf_field_duplicate = true;
						break;
					}
				}
				// If it's not a duplicate go ahead and add it to the list
				if( $gf_field_duplicate !== true ) {
					$fields[] = array(
						'name' => sprintf( 'order_items_gf_%d_%s', $gf_field['formId'], $gf_field['id'] ),
						'label' => sprintf( apply_filters( 'woo_ce_extend_order_fields_gf_label', __( 'Order Items: %s - %s', 'woocommerce-exporter' ) ), ucwords( strtolower( $gf_field['formTitle'] ) ), ucfirst( strtolower( $gf_field['label'] ) ) ),
						'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_gf_hover', '%s: %s (ID: %d)' ), __( 'Gravity Forms', 'woocommerce-exporter' ), ucwords( strtolower( $gf_field['formTitle'] ) ), $gf_field['formId'] )
					);
				}
			}
		}
		unset( $gf_fields, $gf_field );
	}

	// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
	if( woo_ce_detect_export_plugin( 'extra_product_options' ) ) {
		$tm_fields = woo_ce_get_extra_product_option_fields();
		if( !empty( $tm_fields ) ) {
			foreach( $tm_fields as $tm_field ) {
				$fields[] = array(
					'name' => sprintf( 'order_items_tm_%s', sanitize_key( $tm_field['name'] ) ),
					'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), ( !empty( $tm_field['section_label'] ) ? $tm_field['section_label'] : $tm_field['name'] ) ),
					'hover' => __( 'WooCommerce TM Extra Product Options', 'woocommerce-exporter' )
				);
			}
		}
		unset( $tm_fields, $tm_field );
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( woo_ce_detect_export_plugin( 'wc_customfields' ) ) {
		if( !get_option( 'wccf_migrated_to_20' ) ) {
			$options = get_option( 'rp_wccf_options' );
			if( !empty( $options ) ) {
				$options = ( isset( $options[1] ) ? $options[1] : false );
				if( !empty( $options ) ) {
					// Product Fields
					$custom_fields = ( isset( $options['product_fb_config'] ) ? $options['product_fb_config'] : false );
					if( !empty( $custom_fields ) ) {
						foreach( $custom_fields as $custom_field ) {
							$fields[] = array(
								'name' => sprintf( 'order_items_wccf_%s', sanitize_key( $custom_field['key'] ) ),
								'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), ucfirst( $custom_field['label'] ) ),
								'hover' => sprintf( '%s: %s (ID: %s)', __( 'WooCommerce Custom Fields', 'woocommerce-exporter' ), __( 'Product Field', 'woocommerce-exporter' ), sanitize_key( $custom_field['key'] ) )
							);
						}
						unset( $custom_fields, $custom_field );
					}
				}
				unset( $options );
			}
		} else {
			// Product Fields
			$custom_fields = woo_ce_get_wccf_product_fields();
			if( !empty( $custom_fields ) ) {
				foreach( $custom_fields as $custom_field ) {
					$label = get_post_meta( $custom_field->ID, 'label', true );
					$key = get_post_meta( $custom_field->ID, 'key', true );
					$fields[] = array(
						'name' => sprintf( 'order_items_wccf_%s', sanitize_key( $key ) ),
						'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), ucfirst( $label ) ),
						'hover' => sprintf( '%s: %s (ID: %s)', __( 'WooCommerce Custom Fields', 'woocommerce-exporter' ), __( 'Product Field', 'woocommerce-exporter' ), sanitize_key( $key ) )
					);
				}
			}
			unset( $custom_fields, $custom_field, $key );
		}
	}

	// WooCommerce Product Custom Options Lite - https://wordpress.org/plugins/woocommerce-custom-options-lite/
	if( woo_ce_detect_export_plugin( 'wc_product_custom_options' ) ) {
		$custom_options = woo_ce_get_product_custom_options();
		if( !empty( $custom_options ) ) {
			foreach( $custom_options as $custom_option ) {
				$fields[] = array(
					'name' => sprintf( 'order_items_pco_%s', sanitize_key( $custom_option ) ),
					'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), ucfirst( $custom_option ) ),
					'hover' => __( 'WooCommerce Product Custom Options Lite', 'woocommerce-exporter' )
				);
			}
		}
		unset( $custom_options, $custom_option );
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( woo_ce_detect_export_plugin( 'wc_barcodes' ) ) {
		$fields[] = array(
			'name' => 'order_items_barcode_type',
			'label' => __( 'Order Items: Barcode Type', 'woocommerce-exporter' ),
			'hover' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_barcode',
			'label' => __( 'Order Items: Barcode', 'woocommerce-exporter' ),
			'hover' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Easy Bookings - https://wordpress.org/plugins/woocommerce-easy-booking-system/
	if( woo_ce_detect_export_plugin( 'wc_easybooking' ) ) {
		$fields[] = array(
			'name' => 'order_items_booking_start_date',
			'label' => __( 'Order Items: Start', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Easy Booking', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_end_date',
			'label' => __( 'Order Items: End', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Easy Booking', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Appointments - http://www.bizzthemes.com/plugins/woocommerce-appointments/
	if( woo_ce_detect_export_plugin( 'wc_appointments' ) ) {
		$fields[] = array(
			'name' => 'order_items_appointment_id',
			'label' => __( 'Order Items: Appointment ID', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Appointments', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_start_date',
			'label' => __( 'Order Items: Start Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Appointments', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_start_time',
			'label' => __( 'Order Items: Start Time', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Appointments', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_end_date',
			'label' => __( 'Order Items: End Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Appointments', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_end_time',
			'label' => __( 'Order Items: End Time', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Appointments', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'order_items_booking_all_day',
			'label' => __( 'Order Items: All Day Booking' ),
			'hover' => __( 'WooCommerce Appointments', 'woocommerce-exporter' )
		);
	}

	// Variation Attributes
	if( apply_filters( 'woo_ce_enable_product_attributes', true ) ) {
		$attributes = woo_ce_get_product_attributes();
		if( !empty( $attributes ) ) {
			foreach( $attributes as $attribute ) {
				$attribute->attribute_label = trim( $attribute->attribute_label );
				if( empty( $attribute->attribute_label ) )
					$attribute->attribute_label = $attribute->attribute_name;
				$key = sanitize_key( $attribute->attribute_name );
				// First row is to fetch the Variation Attribute linked to the Order Item
				$fields[] = array(
					'name' => sprintf( 'order_items_attribute_%s', $key ),
					'label' => sprintf( __( 'Order Items: %s Variation', 'woocommerce-exporter' ), ucwords( $attribute->attribute_label ) ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_attribute', '%s: %s (#%d)' ), __( 'Product Variation', 'woocommerce-exporter' ), $attribute->attribute_name, $attribute->attribute_id )
				);
				// The second row is to fetch the Product Attribute from the Order Item Product
				$fields[] = array(
					'name' => sprintf( 'order_items_product_attribute_%s', $key ),
					'label' => sprintf( __( 'Order Items: %s Attribute', 'woocommerce-exporter' ), ucwords( $attribute->attribute_label ) ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_product_attribute', '%s: %s (#%d)' ), __( 'Product Attribute', 'woocommerce-exporter' ), $attribute->attribute_name, $attribute->attribute_id )
				);
			}
		}
		unset( $attributes, $attribute );
	}

	// Custom Order Items fields
	$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
	if( !empty( $custom_order_items ) ) {
		foreach( $custom_order_items as $custom_order_item ) {
			if( !empty( $custom_order_item ) ) {
				$fields[] = array(
					'name' => sprintf( 'order_items_%s', sanitize_key( $custom_order_item ) ),
					'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), woo_ce_clean_export_label( $custom_order_item ) ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_custom_order_item_hover', '%s: %s' ), __( 'Custom Order Item', 'woocommerce-exporter' ), $custom_order_item )
				);
			}
		}
	}
	unset( $custom_order_items, $custom_order_item );

	// Custom Order Item Product fields
	$custom_order_products = woo_ce_get_option( 'custom_order_products', '' );
	if( !empty( $custom_order_products ) ) {
		foreach( $custom_order_products as $custom_order_product ) {
			if( !empty( $custom_order_product ) ) {
				$fields[] = array(
					'name' => sprintf( 'order_items_%s', sanitize_key( $custom_order_product ) ),
					'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), woo_ce_clean_export_label( $custom_order_product ) ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_custom_order_product_hover', '%s: %s' ), __( 'Custom Order Item Product', 'woocommerce-exporter' ), $custom_order_product )
				);
			}
		}
	}
	unset( $custom_order_products, $custom_order_product );

	// Custom Product fields
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				$fields[] = array(
					'name' => sprintf( 'order_items_%s', sanitize_key( $custom_product ) ),
					'label' => sprintf( __( 'Order Items: %s', 'woocommerce-exporter' ), woo_ce_clean_export_label( $custom_product ) ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_order_fields_custom_product_hover', '%s: %s' ), __( 'Custom Product', 'woocommerce-exporter' ), $custom_product )
				);
			}
		}
	}
	unset( $custom_products, $custom_product );

	return $fields;

}
add_filter( 'woo_ce_order_items_fields', 'woo_ce_extend_order_items_fields' );
// Gravity Forms - http://woothemes.com/woocommerce
function woo_ce_get_gravity_forms_fields() {

	if( apply_filters( 'woo_ce_enable_addon_gravity_forms', true ) == false )
		return;

	global $export;

	if( WOO_CD_LOGGING ) {
		if( isset( $export->start_time ) )
			woo_ce_error_log( sprintf( 'Debug: %s', 'begin woo_ce_get_gravity_forms_fields(): ' . ( time() - $export->start_time ) ) );
	}

	// Can we use the existing Transient?
	if ( false === ( $fields = get_transient( WOO_CE_PREFIX . '_gravity_forms_fields' ) ) ) {

		$fields = array();
		$gf_products = woo_ce_get_gravity_forms_products();
		if( !empty( $gf_products ) ) {
			foreach( $gf_products as $gf_product ) {
				if( $gf_product_data = maybe_unserialize( get_post_meta( $gf_product->post_id, '_gravity_form_data', true ) ) ) {
					// Check the class and method for Gravity Forms exists
					if( class_exists( 'RGFormsModel' ) && method_exists( 'RGFormsModel', 'get_form_meta' ) ) {
						// Check the form exists
						$gf_form_meta = RGFormsModel::get_form_meta( $gf_product_data['id'] );
						if( !empty( $gf_form_meta ) ) {
							// Check that the form has fields assigned to it
							if( !empty( $gf_form_meta['fields'] ) ) {
								foreach( $gf_form_meta['fields'] as $gf_form_field ) {
									// Check for duplicate Gravity Form fields
									$gf_form_field['formTitle'] = $gf_form_meta['title'];
									// Do not include page and section breaks, hidden as exportable fields
									if( !in_array( $gf_form_field['type'], array( 'page', 'section', 'hidden' ) ) )
										$fields[] = $gf_form_field;
								}
							}
						}
						unset( $gf_form_meta );
					}
				}
				unset( $gf_product_data );
			}
			unset( $gf_products, $gf_product );
		}

		// Save as Transient
		set_transient( WOO_CE_PREFIX . '_gravity_forms_fields', $fields, HOUR_IN_SECONDS );

	}

	if( WOO_CD_LOGGING ) {
		if( isset( $export->start_time ) )
			woo_ce_error_log( sprintf( 'Debug: %s', 'after woo_ce_get_gravity_forms_fields(): ' . ( time() - $export->start_time ) ) );
	}

	return $fields;

}

// WooCommerce TM Extra Product Options - http://codecanyon.net/item/woocommerce-extra-product-options/7908619
function woo_ce_get_extra_product_option_fields() {

	global $wpdb;

	$meta_key = '_tmcartepo_data';
	$tm_fields_sql = $wpdb->prepare( "SELECT order_itemmeta.`meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_items` as order_items, `" . $wpdb->prefix . "woocommerce_order_itemmeta` as order_itemmeta WHERE order_items.`order_item_id` = order_itemmeta.`order_item_id` AND order_items.`order_item_type` = 'line_item' AND order_itemmeta.`meta_key` = %s", $meta_key );
	$tm_fields = $wpdb->get_col( $tm_fields_sql );
	if( !empty( $tm_fields ) ) {
		$fields = array();
		foreach( $tm_fields as $tm_field ) {
			$tm_field = maybe_unserialize( $tm_field );
			$size = count( $tm_field );
			for( $i = 0; $i < $size; $i++ ) {
				// Check that the name is set
				if( !empty( $tm_field[$i]['name'] ) ) {
				// Check if we haven't already set this
					if( !array_key_exists( sanitize_key( $tm_field[$i]['name'] ), $fields ) )
						$fields[sanitize_key( $tm_field[$i]['name'] )] = $tm_field[$i];
				}
			}
		}
	}

	return $fields;

}

// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
function woo_ce_get_wccf_product_fields() {

	$post_type = 'wccf_product_field';
	$args = array(
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => -1
	);
	$product_fields = new WP_Query( $args );
	if( !empty( $product_fields->posts ) ) {
		return $product_fields->posts;
	}

}

// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
function woo_ce_get_wccf_order_fields() {

	$post_type = 'wccf_order_field';
	$args = array(
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => -1
	);
	$order_fields = new WP_Query( $args );
	if( !empty( $order_fields->posts ) ) {
		return $order_fields->posts;
	}

}

// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
function woo_ce_get_wccf_checkout_fields() {

	$post_type = 'wccf_checkout_field';
	$args = array(
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => -1
	);
	$checkout_fields = new WP_Query( $args );
	if( !empty( $checkout_fields->posts ) ) {
		return $checkout_fields->posts;
	}

}