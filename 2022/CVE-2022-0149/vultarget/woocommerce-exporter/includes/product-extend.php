<?php
function woo_ce_extend_product_fields( $fields = array() ) {

	// WordPress MultiSite
	if( is_multisite() ) {
		$fields[] = array(
			'name' => 'blog_id',
			'label' => __( 'Blog ID', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress Multisite', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

/*
	// Attributes
	if( $attributes = woo_ce_get_product_attributes() ) {
		foreach( $attributes as $attribute ) {
			if( empty( $attribute->attribute_label ) )
				$attribute->attribute_label = $attribute->attribute_name;
			$fields[] = array(
				'name' => sprintf( 'attribute_%s', $attribute->attribute_name ),
				'label' => sprintf( __( 'Attribute: %s', 'woocommerce-exporter' ), $attribute->attribute_label )
			);
		}
	}
*/

	// WooCommerce Google Product Feed - http://www.leewillis.co.uk/wordpress-plugins/
	if( woo_ce_detect_export_plugin( 'gpf' ) ) {
		$fields[] = array(
			'name' => 'gpf_availability',
			'label' => __( 'WooCommerce Google Product Feed - Availability', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_condition',
			'label' => __( 'WooCommerce Google Product Feed - Condition', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_brand',
			'label' => __( 'WooCommerce Google Product Feed - Brand', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_product_type',
			'label' => __( 'WooCommerce Google Product Feed - Product Type', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_google_product_category',
			'label' => __( 'WooCommerce Google Product Feed - Google Product Category', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_gtin',
			'label' => __( 'WooCommerce Google Product Feed - Global Trade Item Number (GTIN)', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_mpn',
			'label' => __( 'WooCommerce Google Product Feed - Manufacturer Part Number (MPN)', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_gender',
			'label' => __( 'WooCommerce Google Product Feed - Gender', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_agegroup',
			'label' => __( 'WooCommerce Google Product Feed - Age Group', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_color',
			'label' => __( 'WooCommerce Google Product Feed - Colour', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_size',
			'label' => __( 'WooCommerce Google Product Feed - Size', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Google Product Feed', 'woocommerce-exporter' )
		);
	}

	// All in One SEO Pack - http://wordpress.org/extend/plugins/all-in-one-seo-pack/
	if( woo_ce_detect_export_plugin( 'aioseop' ) ) {
		$fields[] = array(
			'name' => 'aioseop_keywords',
			'label' => __( 'All in One SEO - Keywords', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'aioseop_description',
			'label' => __( 'All in One SEO - Description', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'aioseop_title',
			'label' => __( 'All in One SEO - Title', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'aioseop_title_attributes',
			'label' => __( 'All in One SEO - Title Attributes', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'aioseop_menu_label',
			'label' => __( 'All in One SEO - Menu Label', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
	}

	// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
	if( woo_ce_detect_export_plugin( 'wpseo' ) ) {
		$fields[] = array(
			'name' => 'wpseo_focuskw',
			'label' => __( 'WordPress SEO - Focus Keyword', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_metadesc',
			'label' => __( 'WordPress SEO - Meta Description', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_title',
			'label' => __( 'WordPress SEO - SEO Title', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_noindex',
			'label' => __( 'WordPress SEO - Noindex', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_follow',
			'label' => __( 'WordPress SEO - Follow', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_googleplus_description',
			'label' => __( 'WordPress SEO - Google+ Description', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_opengraph_title',
			'label' => __( 'WordPress SEO - Facebook Title', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_opengraph_description',
			'label' => __( 'WordPress SEO - Facebook Description', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_opengraph_image',
			'label' => __( 'WordPress SEO - Facebook Image', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_twitter_title',
			'label' => __( 'WordPress SEO - Twitter Title', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_twitter_description',
			'label' => __( 'WordPress SEO - Twitter Description', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_twitter_image',
			'label' => __( 'WordPress SEO - Twitter Image', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_canonical',
			'label' => __( 'WordPress SEO - Canonical URL', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
	}

	// Ultimate SEO - http://wordpress.org/plugins/seo-ultimate/
	if( woo_ce_detect_export_plugin( 'ultimate_seo' ) ) {
		$fields[] = array(
			'name' => 'useo_meta_title',
			'label' => __( 'Ultimate SEO - Title Tag', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_meta_description',
			'label' => __( 'Ultimate SEO - Meta Description', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_meta_keywords',
			'label' => __( 'Ultimate SEO - Meta Keywords', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_social_title',
			'label' => __( 'Ultimate SEO - Social Title', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_social_description',
			'label' => __( 'Ultimate SEO - Social Description', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_meta_noindex',
			'label' => __( 'Ultimate SEO - Noindex', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_meta_noautolinks',
			'label' => __( 'Ultimate SEO - Disable Autolinks', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() ) {
		$fields[] = array(
			'name' => 'brands',
			'label' => __( 'Brands', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Brands', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( woo_ce_detect_export_plugin( 'wc_msrp' ) ) {
		$fields[] = array(
			'name' => 'msrp',
			'label' => __( 'MSRP', 'woocommerce-exporter' ),
			'hover' => __( 'Manufacturer Suggested Retail Price (MSRP)', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Germanized Pro - https://www.vendidero.de/woocommerce-germanized
	if( woo_ce_detect_export_plugin( 'wc_germanized_pro' ) ) {
		// Check for Product Units
		if( get_option( 'woocommerce_gzd_display_listings_product_units' ) == 'yes' ) {
			$fields[] = array(
				'name' => 'sale_price_label',
				'label' => __( 'Sale Label', 'woocommerce-germanized' ),
				'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'sale_price_regular_label',
				'label' => __( 'Sale Regular Label', 'woocommerce-germanized' ),
				'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'unit',
				'label' => __( 'Unit', 'woocommerce-germanized' ),
				'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'unit_product',
				'label' => __( 'Product Units', 'woocommerce-germanized' ),
				'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'unit_base',
				'label' => __( 'Base Price Units', 'woocommerce-germanized' ),
				'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'unit_price_auto',
				'label' => __( 'Calculation', 'woocommerce-germanized' ),
				'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'unit_price_regular',
				'label' => __( 'Regular Base Price', 'woocommerce-germanized' ),
				'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'unit_price_sale',
				'label' => __( 'Sale Base Price', 'woocommerce-germanized' ),
				'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'unit_price_regular_display',
				'label' => __( 'Regular Base Price Display', 'woocommerce-germanized' ),
				'hover' => __( 'WooCommerce Germanized', 'woocommerce-exporter' ),
				'disabled' => 1
			);
		}
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( woo_ce_detect_export_plugin( 'wc_cog' ) ) {
		$fields[] = array(
			'name' => 'cost_of_goods',
			'label' => __( 'Cost of Good', 'woocommerce-exporter' ),
			'hover' => __( 'Cost of Goods', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// Per Product Shipping - http://www.woothemes.com/products/per-product-shipping/
	if( woo_ce_detect_export_plugin( 'per_product_shipping' ) ) {
		$fields[] = array(
			'name' => 'per_product_shipping',
			'label' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
			'hover' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'per_product_shipping_country',
			'label' => __( 'Per-Product Shipping - Country', 'woocommerce-exporter' ),
			'hover' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'per_product_shipping_state',
			'label' => __( 'Per-Product Shipping - State', 'woocommerce-exporter' ),
			'hover' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'per_product_shipping_postcode',
			'label' => __( 'Per-Product Shipping - Postcode', 'woocommerce-exporter' ),
			'hover' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'per_product_shipping_cost',
			'label' => __( 'Per-Product Shipping - Cost', 'woocommerce-exporter' ),
			'hover' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'per_product_shipping_item_cost',
			'label' => __( 'Per-Product Shipping - Item Cost', 'woocommerce-exporter' ),
			'hover' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'per_product_shipping_order',
			'label' => __( 'Per-Product Shipping - Priority', 'woocommerce-exporter' ),
			'hover' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( woo_ce_detect_export_plugin( 'vendors' ) ) {
		$fields[] = array(
			'name' => 'vendors',
			'label' => __( 'Product Vendors', 'woocommerce-exporter' ),
			'hover' => __( 'Product Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'vendor_ids',
			'label' => __( 'Product Vendor ID\'s', 'woocommerce-exporter' ),
			'hover' => __( 'Product Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'vendor_commission',
			'label' => __( 'Vendor Commission', 'woocommerce-exporter' ),
			'hover' => __( 'Product Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WC Vendors - http://wcvendors.com
	if( woo_ce_detect_export_plugin( 'wc_vendors' ) ) {
		$fields[] = array(
			'name' => 'vendor',
			'label' => __( 'Vendor' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'vendor_commission_rate',
			'label' => __( 'Commission (%)' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// YITH WooCommerce Multi Vendor Premium - http://yithemes.com/themes/plugins/yith-woocommerce-product-vendors/
	if( woo_ce_detect_export_plugin( 'yith_vendor' ) ) {
		$fields[] = array(
			'name' => 'vendor',
			'label' => __( 'Vendor' ),
			'hover' => __( 'YITH WooCommerce Multi Vendor Premium', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'vendor_commission_rate',
			'label' => __( 'Commission (%)' ),
			'hover' => __( 'YITH WooCommerce Multi Vendor Premium', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WC Marketplace - https://wc-marketplace.com/
	if( woo_ce_detect_export_plugin( 'wc_marketplace' ) ) {
		$fields[] = array(
			'name' => 'vendor',
			'label' => __( 'Vendor' ),
			'hover' => __( 'WC Marketplace', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'vendor_commission',
			'label' => __( 'Commission' ),
			'hover' => __( 'WC Marketplace', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Wholesale Pricing - http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-wholesale-pricing/
	if( woo_ce_detect_export_plugin( 'wholesale_pricing' ) ) {
		$fields[] = array(
			'name' => 'wholesale_price',
			'label' => __( 'Wholesale Price', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Wholesale Pricing', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'wholesale_price_text',
			'label' => __( 'Wholesale Text', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Wholesale Pricing', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// Advanced Custom Fields - http://www.advancedcustomfields.com
	if( woo_ce_detect_export_plugin( 'acf' ) ) {
		$custom_fields = woo_ce_get_acf_product_fields();
		if( !empty( $custom_fields ) ) {
			foreach( $custom_fields as $custom_field ) {
				$fields[] = array(
					'name' => sprintf( 'acf_%s', sanitize_key( $custom_field['name'] ) ),
					'label' => $custom_field['label'],
					'hover' => __( 'Advanced Custom Fields', 'woocommerce-exporter' ),
					'disabled' => 1
				);
			}
		}
		unset( $custom_fields, $custom_field );
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( woo_ce_detect_export_plugin( 'wc_customfields' ) ) {
		if( !get_option( 'wccf_migrated_to_20' ) ) {
			// Legacy WooCommerce Custom Fields was stored in a single Option
			$options = get_option( 'rp_wccf_options' );
			if( !empty( $options ) ) {
				$custom_fields = ( isset( $options[1]['product_admin_fb_config'] ) ? $options[1]['product_admin_fb_config'] : false );
				if( !empty( $custom_fields ) ) {
					foreach( $custom_fields as $custom_field ) {
						$fields[] = array(
							'name' => sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) ),
							'label' => ucfirst( $custom_field['label'] ),
							'hover' => __( 'WooCommerce Custom Fields', 'woocommerce-exporter' ),
							'disabled' => 1
						);
					}
				}
			}
			unset( $options );
		} else {
			// WooCommerce Custom Fields uses CPT for Product properties
			$custom_fields = woo_ce_get_wccf_product_properties();
			if( !empty( $custom_fields ) ) {
				foreach( $custom_fields as $custom_field ) {
					$label = get_post_meta( $custom_field->ID, 'label', true );
					$key = get_post_meta( $custom_field->ID, 'key', true );
					$fields[] = array(
						'name' => sprintf( 'wccf_pp_%s', sanitize_key( $key ) ),
						'label' => ucfirst( $label ),
						'hover' => __( 'WooCommerce Custom Fields', 'woocommerce-exporter' ),
						'disabled' => 1
					);
				}
			}
			unset( $label, $key );
		}
		unset( $custom_fields, $custom_field );
	}

	// WC Fields Factory - https://wordpress.org/plugins/wc-fields-factory/
	if( woo_ce_detect_export_plugin( 'wc_fields_factory' ) ) {
		// Admin Fields
		$admin_fields = woo_ce_get_wcff_admin_fields();
		if( !empty( $admin_fields ) ) {
			foreach( $admin_fields as $admin_field ) {
				$fields[] = array(
					'name' => sprintf( 'wccaf_%s', sanitize_key( $admin_field['name'] ) ),
					'label' => ucfirst( $admin_field['label'] ),
					'hover' => sprintf( '%s: %s (%s)', __( 'WC Fields Factory', 'woocommerce-exporter' ), __( 'Admin Field', 'woocommerce-exporter' ), sanitize_key( $admin_field['name'] ) ),
					'disabled' => 1
				);
			}
		}
		unset( $admin_fields, $admin_field );
	}

	// WooCommerce Subscriptions - http://www.woothemes.com/products/woocommerce-subscriptions/
	if( woo_ce_detect_export_plugin( 'subscriptions' ) ) {
		$fields[] = array(
			'name' => 'subscription_price',
			'label' => __( 'Subscription Price', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'subscription_period_interval',
			'label' => __( 'Subscription Period Interval', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'subscription_period',
			'label' => __( 'Subscription Period', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'subscription_length',
			'label' => __( 'Subscription Length', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'subscription_sign_up_fee',
			'label' => __( 'Subscription Sign-up Fee', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'subscription_trial_length',
			'label' => __( 'Subscription Trial Length', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'subscription_trial_period',
			'label' => __( 'Subscription Trial Period', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'subscription_limit',
			'label' => __( 'Limit Subscription', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( woo_ce_detect_export_plugin( 'woocommerce_bookings' ) ) {
		$fields[] = array(
			'name' => 'booking_has_persons',
			'label' => __( 'Booking Has Persons', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_has_resources',
			'label' => __( 'Booking Has Resources', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_base_cost',
			'label' => __( 'Booking Base Cost', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_block_cost',
			'label' => __( 'Booking Block Cost', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_display_cost',
			'label' => __( 'Booking Display Cost', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_requires_confirmation',
			'label' => __( 'Booking Requires Confirmation', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_user_can_cancel',
			'label' => __( 'Booking Can Be Cancelled', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( woo_ce_detect_export_plugin( 'wc_barcodes' ) ) {
		$fields[] = array(
			'name' => 'barcode_type',
			'label' => __( 'Barcode Type', 'woocommerce-exporter' ),
			'hover' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'barcode',
			'label' => __( 'Barcode', 'woocommerce-exporter' ),
			'hover' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Pre-Orders - http://www.woothemes.com/products/woocommerce-pre-orders/
	if( woo_ce_detect_export_plugin( 'wc_preorders' ) ) {
		$fields[] = array(
			'name' => 'pre_orders_enabled',
			'label' => __( 'Pre-Order Enabled', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'pre_orders_availability_date',
			'label' => __( 'Pre-Order Availability Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'pre_orders_fee',
			'label' => __( 'Pre-Order Fee', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'pre_orders_charge',
			'label' => __( 'Pre-Order Charge', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Product Fees - https://wordpress.org/plugins/woocommerce-product-fees/
	if( woo_ce_detect_export_plugin( 'wc_productfees' ) ) {
		$fields[] = array(
			'name' => 'fee_name',
			'label' => __( 'Product Fee Name', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Product Fees', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'fee_amount',
			'label' => __( 'Product Fee Amount', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Product Fees', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'fee_multiplier',
			'label' => __( 'Product Fee Multiplier', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Product Fees', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// FooEvents for WooCommerce - http://www.woocommerceevents.com/
	if( woo_ce_detect_export_plugin( 'fooevents' ) ) {
		$fields[] = array(
			'name' => 'is_event',
			'label' => __( 'Is Event', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_date',
			'label' => __( 'Event Date', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_start_time',
			'label' => __( 'Event Start Time', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_end_time',
			'label' => __( 'Event End Time', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_venue',
			'label' => __( 'Event Venue', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_gps',
			'label' => __( 'Event GPS Coordinates', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_googlemaps',
			'label' => __( 'Event Google Maps Coordinates', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_directions',
			'label' => __( 'Event Directions', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_phone',
			'label' => __( 'Event Phone', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_email',
			'label' => __( 'Event E-mail', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_ticket_logo',
			'label' => __( 'Event Ticket Logo', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_ticket_subject',
			'label' => __( 'Event Ticket Subject', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_ticket_text',
			'label' => __( 'Event Ticket Text', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_ticket_thankyou_text',
			'label' => __( 'Event Ticket Thank You Page Text', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_ticket_background_color',
			'label' => __( 'Event Ticket Background Colour', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_ticket_button_color',
			'label' => __( 'Event Ticket Background Colour', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'event_ticket_text_color',
			'label' => __( 'Event Ticket Background Colour', 'woocommerce-exporter' ),
			'hover' => __( 'FooEvents for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);		
	}

	// WooCommerce Uploads - https://wpfortune.com/shop/plugins/woocommerce-uploads/
	if( woo_ce_detect_export_plugin( 'wc_uploads' ) ) {
		$fields[] = array(
			'name' => 'enable_uploads',
			'label' => __( 'Enable Uploads', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Uploads', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Profit of Sales Report - http://codecanyon.net/item/woocommerce-profit-of-sales-report/9190590
	if( woo_ce_detect_export_plugin( 'wc_posr' ) ) {
		$fields[] = array(
			'name' => 'posr',
			'label' => __( 'Cost of Good', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Profit of Sales Report', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Product Bundles - http://www.woothemes.com/products/product-bundles/
	if( woo_ce_detect_export_plugin( 'wc_product_bundles' ) ) {
		$fields[] = array(
			'name' => 'bundled_products',
			'label' => __( 'Bundled Products', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Product Bundles', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'bundled_product_ids',
			'label' => __( 'Bundled Product ID\'s', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Product Bundles', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Min/Max Quantities - https://woocommerce.com/products/minmax-quantities/
	if( woo_ce_detect_export_plugin( 'wc_min_max' ) ) {
		$fields[] = array(
			'name' => 'minimum_quantity',
			'label' => __( 'Minimum Quantity', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Min/Max Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'maximum_quantity',
			'label' => __( 'Maximum Quantity', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Min/Max Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'group_of',
			'label' => __( 'Group of', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Min/Max Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Tab Manager - http://www.woothemes.com/products/woocommerce-tab-manager/
	if( woo_ce_detect_export_plugin( 'wc_tabmanager' ) ) {
		// Custom Product Tabs
		$custom_product_tabs = woo_ce_get_option( 'custom_product_tabs', '' );
		if( !empty( $custom_product_tabs ) ) {
			foreach( $custom_product_tabs as $custom_product_tab ) {
				if( !empty( $custom_product_tab ) ) {
					$fields[] = array(
						'name' => sprintf( 'product_tab_%s', sanitize_key( $custom_product_tab ) ),
						'label' => sprintf( __( 'Product Tab: %s', 'woocommerce-exporter' ), woo_ce_clean_export_label( $custom_product_tab ) ),
						'hover' => sprintf( __( 'Custom Product Tab: %s', 'woocommerce-exporter' ), $custom_product_tab ),
						'disabled' => 1
					);
				}
			}
		}
		unset( $custom_product_tabs, $custom_product_tab );
	}

	// WooTabs - https://codecanyon.net/item/wootabsadd-extra-tabs-to-woocommerce-product-page/7891253
	if( woo_ce_detect_export_plugin( 'wootabs' ) ) {
		// Custom WooTabs
		$custom_wootabs = woo_ce_get_option( 'custom_wootabs', '' );
		if( !empty( $custom_wootabs ) ) {
			foreach( $custom_wootabs as $custom_wootab ) {
				if( !empty( $custom_wootab ) ) {
					$fields[] = array(
						'name' => sprintf( 'wootab_%s', sanitize_key( $custom_wootab ) ),
						'label' => sprintf( __( 'WooTab: %s', 'woocommerce-exporter' ), woo_ce_clean_export_label( $custom_wootab ) ),
						'hover' => sprintf( __( 'WooTab: %s', 'woocommerce-exporter' ), $custom_wootab ),
						'disabled' => 1
					);
				}
			}
		}
		unset( $custom_wootabs, $custom_wootab );
	}

	// WooCommerce Tiered Pricing - http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-tiered-pricing/
	if( woo_ce_detect_export_plugin( 'ign_tiered' ) ) {

		global $wp_roles;

		// User Roles
		if( isset( $wp_roles->roles ) ) {
			asort( $wp_roles->roles );
			foreach( $wp_roles->roles as $role => $role_data ) {
				// Skip default User Roles
				if( 'ignite_level_' != substr( $role, 0, 13 ) )
					continue;
				$fields[] = array(
					'name' => sanitize_key( $role ),
					'label' => sprintf( __( '%s ($)', 'woocommerce-exporter' ), woo_ce_clean_export_label( stripslashes( $role_data['name'] ) ) ),
					'hover' => __( 'WooCommerce Tiered Pricing', 'woocommerce-exporter' ),
					'disabled' => 1
				);
			}
			unset( $role, $role_data );
		}
	}

	// WooCommerce BookStore - http://www.wpini.com/woocommerce-bookstore-plugin/
	if( woo_ce_detect_export_plugin( 'wc_books' ) ) {
		$custom_books = ( function_exists( 'woo_book_get_custom_fields' ) ? woo_book_get_custom_fields() : false );
		if( !empty( $custom_books ) ) {
			foreach( $custom_books as $custom_book ) {
				if( !empty( $custom_book ) ) {
					$fields[] = array(
						'name' => sprintf( 'book_%s', sanitize_key( $custom_book['name'] ) ),
						'label' => $custom_book['name'],
						'hover' => __( 'WooCommerce BookStore', 'woocommerce-exporter' ),
						'disabled' => 1
					);
				}
			}
		}
		unset( $custom_books, $custom_book );

		$fields[] = array(
			'name' => 'book_category',
			'label' => __( 'Book Category', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce BookStore', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'book_author',
			'label' => __( 'Book Author', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce BookStore', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'book_publisher',
			'label' => __( 'Book Publisher', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce BookStore', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Multilingual - https://wordpress.org/plugins/woocommerce-multilingual/
	if(
		woo_ce_detect_wpml() && 
		woo_ce_detect_export_plugin( 'wpml_wc' )
	) {
		$fields[] = array(
			'name' => 'language',
			'label' => __( 'Language', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Multilingual', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Jetpack - http://woojetpack.com/shop/wordpress-woocommerce-jetpack-plus/
	if(
		woo_ce_detect_export_plugin( 'woocommerce_jetpack' ) || 
		woo_ce_detect_export_plugin( 'woocommerce_jetpack_plus' )
	) {

		// @mod - Needs alot of love in 2.4+, JetPack Plus, now Booster is huge

		// Check for Product Cost Price
		if( get_option( 'wcj_purchase_price_enabled', false ) == 'yes' ) {
			$fields[] = array(
				'name' => 'wcj_purchase_price',
				'label' => __( 'Product cost (purchase) price', 'woocommerce-jetpack' ),
				'hover' => __( 'WooCommerce Jetpack', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'wcj_purchase_price_extra',
				'label' => __( 'Extra expenses (shipping etc.)', 'woocommerce-jetpack' ),
				'hover' => __( 'WooCommerce Jetpack', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'wcj_purchase_price_affiliate_commission',
				'label' => __( 'Affiliate commission', 'woocommerce-jetpack' ),
				'hover' => __( 'WooCommerce Jetpack', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			// @mod - Let's add custom Product Cost Price fields once we get some more Booster modules sorted.
			$fields[] = array(
				'name' => 'wcj_purchase_date',
				'label' => __( '(Last) Purchase date', 'woocommerce-jetpack' ),
				'hover' => __( 'WooCommerce Jetpack', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'wcj_purchase_partner',
				'label' => __( 'Seller', 'woocommerce-jetpack' ),
				'hover' => __( 'WooCommerce Jetpack', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			$fields[] = array(
				'name' => 'wcj_purchase_info',
				'label' => __( 'Purchase info', 'woocommerce-jetpack' ),
				'hover' => __( 'WooCommerce Jetpack', 'woocommerce-exporter' ),
				'disabled' => 1
			);
		}

/*
		// Check if Call for Price is enabled
		if( get_option( 'wcj_call_for_price_enabled', false ) == 'yes' ) {
			// Instead of the price
			$fields[] = array(
				'name' => 'wcf_price_instead',
				'label' => __( 'Instead of the ', 'woocommerce-exporter' ),
				'hover' => __( 'WooCommerce Jetpack', 'woocommerce-exporter' ),
				'disabled' => 1
			);
			// WooCommerce Jetpack Plus fields
			if( woo_ce_detect_export_plugin( 'woocommerce_jetpack_plus' ) ) {
				// Do something
			}
		}
*/

	}

	// WooCommerce Ultimate Multi Currency Suite - https://codecanyon.net/item/woocommerce-ultimate-multi-currency-suite/11997014
	if( woo_ce_detect_export_plugin( 'wc_umcs' ) ) {
		$currencies = json_decode( get_option( 'wcumcs_available_currencies' ) );
		if( !empty( $currencies ) ) {
			$current_currency = ( function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : false );
			foreach( $currencies as $currency_code => $currency_data ) {
				// Skip the base currency
				if( $currency_code == $current_currency )
					continue;
				// Regular Price
				$fields[] = array(
					'name' => sprintf( 'wcumcs_regular_price_%s', sanitize_key( $currency_code ) ),
					'label' => sprintf( __( 'Regular Price (%s)', 'woocommerce-exporter' ), $currency_code ),
					'hover' => __( 'WooCommerce Ultimate Multi Currency Suite', 'woocommerce-exporter' ),
					'disabled' => 1
				);
				// Sale Price
				$fields[] = array(
					'name' => sprintf( 'wcumcs_sale_price_%s', sanitize_key( $currency_code ) ),
					'label' => sprintf( __( 'Sale Price (%s)', 'woocommerce-exporter' ), $currency_code ),
					'hover' => __( 'WooCommerce Ultimate Multi Currency Suite', 'woocommerce-exporter' ),
					'disabled' => 1
				);
			}
			unset( $currency_code, $currency_data, $current_currency );
		}
		unset( $currencies );
	}

	// Products Purchase Price for Woocommerce - https://wordpress.org/plugins/products-purchase-price-for-woocommerce/
	if( woo_ce_detect_export_plugin( 'wc_products_purchase_price' ) ) {
		$fields[] = array(
			'name' => 'purchase_price',
			'label' => __( 'Purchase Price', 'woocommerce-exporter' ),
			'hover' => __( 'Products Purchase Price for WooCommerce', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Wholesale Prices - https://wordpress.org/plugins/woocommerce-wholesale-prices/
	if( woo_ce_detect_export_plugin( 'wc_wholesale_prices' ) ) {
		$wholesale_roles = woo_ce_get_wholesale_prices_roles();
		if( !empty( $wholesale_roles ) ) {
			foreach( $wholesale_roles as $key => $wholesale_role ) {
				$fields[] = array(
					'name' => sprintf( '%s_wholesale_price', $key ),
					'label' => sprintf( __( 'Wholesale Price: %s', 'woocommerce-exporter' ), $wholesale_role['roleName'] ),
					'hover' => __( 'WooCommerce Wholesale Prices', 'woocommerce-exporter' ),
					'disabled' => 1
				);
			}
		}
		unset( $wholesale_roles, $wholesale_role, $key );
	}

	// WooCommerce Currency Switcher - http://aelia.co/shop/currency-switcher-woocommerce/
	if( woo_ce_detect_export_plugin( 'currency_switcher' ) ) {
		$options = get_option( 'wc_aelia_currency_switcher' );
		$currencies = ( isset( $options['enabled_currencies'] ) ? $options['enabled_currencies'] : false );
		if( !empty( $currencies ) ) {
			$woocommerce_currency = get_option( 'woocommerce_currency' );
			foreach( $currencies as $currency ) {

				// Skip the WooCommerce default currency
				if( $woocommerce_currency == $currency )
					continue;

				// Product Base Currency

				$fields[] = array(
					'name' => sprintf( 'wcae_regular_price_%s', sanitize_key( $currency ) ),
					'label' => sprintf( __( 'Regular Price (%s)', 'woocommerce-exporter' ), $currency ),
					'hover' => __( 'WooCommerce Currency Switcher', 'woocommerce-exporter' ),
					'disabled' => 1
				);
				$fields[] = array(
					'name' => sprintf( 'wcae_sale_price_%s', sanitize_key( $currency ) ),
					'label' => sprintf( __( 'Sale Price (%s)', 'woocommerce-exporter' ), $currency ),
					'hover' => __( 'WooCommerce Currency Switcher', 'woocommerce-exporter' ),
					'disabled' => 1
				);

			}
			unset( $woocommerce_currency, $currencies, $currency );
		}
		unset( $options );
	}

	// WooCommerce Show Single Variations - https://iconicwp.com/products/woocommerce-show-single-variations/
	if( woo_ce_detect_export_plugin( 'wc_show_single_variations' ) ) {
		$fields[] = array(
			'name' => 'show_search_results',
			'label' => __( 'Show in Search Results', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Show Single Variations', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'show_filtered_results',
			'label' => __( 'Show in Filtered Results', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Show Single Variations', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'show_catalog',
			'label' => __( 'Show in Catalog', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Show Single Variations', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'disable_add_to_cart',
			'label' => __( 'Disable Add to Cart', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Show Single Variations', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Deposits - https://woocommerce.com/products/woocommerce-deposits/
	if( woo_ce_detect_export_plugin( 'wc_deposits' ) ) {
		$fields[] = array(
			'name' => 'enable_deposit',
			'label' => __( 'Enable Deposit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'force_deposit',
			'label' => __( 'Force Deposit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'amount_type',
			'label' => __( 'Deposit Type', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'deposit_amount',
			'label' => __( 'Deposit Amount', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Deposits', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Unit of Measure - https://wordpress.org/plugins/woocommerce-unit-of-measure/
	if( woo_ce_detect_export_plugin( 'wc_unitofmeasure' ) ) {
		$fields[] = array(
			'name' => 'unit_of_measure',
			'label' => __( 'Unit of Measure', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Unit of Measure', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Easy Bookings - https://wordpress.org/plugins/woocommerce-easy-booking-system/
	if( woo_ce_detect_export_plugin( 'wc_easybooking' ) ) {
		$fields[] = array(
			'name' => 'bookable',
			'label' => __( 'Bookable', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Easy Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_dates',
			'label' => __( 'Number of Dates to Select', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Easy Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_duration',
			'label' => __( 'Booking Duration', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Easy Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_min',
			'label' => __( 'Minimum Booking Duration', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Easy Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'booking_max',
			'label' => __( 'Maximum Booking Duration', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Easy Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'first_available_date',
			'label' => __( 'First Available Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Easy Bookings', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Advanced Product Quantities - http://www.wpbackoffice.com/plugins/woocommerce-incremental-product-quantities/
	if( woo_ce_detect_export_plugin( 'wc_advanced_quantities' ) ) {
		$fields[] = array(
			'name' => 'deactivate_quantity_rules',
			'label' => __( 'De-activate Quantity Rules', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Advanced Product Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'override_quantity_rules',
			'label' => __( 'Override Quantity Rules', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Advanced Product Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'step_value',
			'label' => __( 'Step Value', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Advanced Product Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'minimum_quantity',
			'label' => __( 'Minimum Quantity', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Advanced Product Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'maximum_quantity',
			'label' => __( 'Maximum Quantity', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Advanced Product Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'oos_minimum',
			'label' => __( 'Out of Stock Minimum', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Advanced Product Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'oos_maximum',
			'label' => __( 'Out of Stock Maximum', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Advanced Product Quantities', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Chained Products - https://woocommerce.com/products/chained-products/
	if( woo_ce_detect_export_plugin( 'wc_chained_products' ) ) {
		$fields[] = array(
			'name' => 'chained_products',
			'label' => __( 'Chained Products', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Chained Products', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'chained_products_ids',
			'label' => __( 'Chained Product IDs', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Chained Products', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'chained_products_names',
			'label' => __( 'Chained Product Names', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Chained Products', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'chained_products_skus',
			'label' => __( 'Chained Product SKUs', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Chained Products', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'chained_products_units',
			'label' => __( 'Chained Product Units', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Chained Products', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'chained_products_manage_stock',
			'label' => __( 'Manage Stock for Chained Products', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Chained Products', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Sample - https://wordpress.org/plugins/woocommerce-sample/
	if( woo_ce_detect_export_plugin( 'wc_sample' ) ) {
		$fields[] = array(
			'name' => 'enable_sample',
			'label' => __( 'Enable Sample', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Sample', 'woocommerce-exporter' ),
			'disabled' => 1
		);

		// WooCommerce Chained Products - https://woocommerce.com/products/chained-products/
		if( woo_ce_detect_export_plugin( 'wc_chained_products' ) ) {
			$fields[] = array(
				'name' => 'enable_sample_chained',
				'label' => __( 'Enable Sample on Chained Products', 'woocommerce-exporter' ),
				'hover' => __( 'WooCommerce Sample', 'woocommerce-exporter' ),
			'disabled' => 1
			);
		}
		$fields[] = array(
			'name' => 'sample_shipping_mode',
			'label' => __( 'Sample Shipping Mode', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Sample', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'sample_shipping',
			'label' => __( 'Sample Shipping', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Sample', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'sample_price_mode',
			'label' => __( 'Sample Price Mode', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Sample', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'sample_price',
			'label' => __( 'Sample Price', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Sample', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// AG WooCommerce Barcode / ISBN & Amazon ASIN - PRO - https://www.weareag.co.uk/product/woocommerce-barcodeisbn-amazon-asin-pro/
	if( woo_ce_detect_export_plugin( 'wc_ag_barcode_pro' ) ) {
		$fields[] = array(
			'name' => 'barcode',
			'label' => __( 'Barcode', 'woocommerce-exporter' ),
			'hover' => __( 'AG WooCommerce Barcode / ISBN & Amazon ASIN - PRO', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'isbn',
			'label' => __( 'ISBN', 'woocommerce-exporter' ),
			'hover' => __( 'AG WooCommerce Barcode / ISBN & Amazon ASIN - PRO', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'amazon',
			'label' => __( 'ASIN', 'woocommerce-exporter' ),
			'hover' => __( 'AG WooCommerce Barcode / ISBN & Amazon ASIN - PRO', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// N-Media WooCommerce Personalized Product Meta Manager - https://najeebmedia.com/wordpress-plugin/woocommerce-personalized-product-option/
	// PPOM for WooCommerce - https://wordpress.org/plugins/woocommerce-product-addon/
	if(
		woo_ce_detect_export_plugin( 'wc_nm_personalizedproduct' ) || 
		woo_ce_detect_export_plugin( 'wc_ppom' )
	) {
		$fields[] = array(
			'name' => 'select_personalized_meta',
			'label' => __( 'Select Personalized Meta', 'woocommerce-exporter' ),
			'hover' => __( 'N-Media WooCommerce Personalized Product Meta Manager', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// SEO Squirrly - https://wordpress.org/plugins/squirrly-seo/
	if( woo_ce_detect_export_plugin( 'seo_squirrly' ) ) {
		$fields[] = array(
			'name' => 'sq_keywords',
			'label' => __( 'Keywords', 'woocommerce-exporter' ),
			'hover' => __( 'SEO Squirrly', 'woocommerce-exporter' ),
			'disabled' => 1
		);
	}

	// WooCommerce Measurement Price Calculator - http://www.woocommerce.com/products/measurement-price-calculator/
	if( woo_ce_detect_export_plugin( 'wc_measurement_price_calc' ) ) {

		$fields[] = array(
			'name' => 'area',
			'label' => __( 'Area', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'volume',
			'label' => __( 'Volume', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement',
			'label' => __( 'Measurement', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		// Dimensions
		$fields[] = array(
			'name' => 'measurement_dimension_pricing',
			'label' => __( 'Dimension: Show Product Price Per Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_dimension_pricing_label',
			'label' => __( 'Dimension: Pricing Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_dimension_pricing_unit',
			'label' => __( 'Dimension: Pricing Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);

		// Area
		$fields[] = array(
			'name' => 'measurement_area_pricing',
			'label' => __( 'Area: Show Product Price Per Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_pricing_label',
			'label' => __( 'Area: Pricing Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_pricing_unit',
			'label' => __( 'Area: Pricing Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);

		// Area (LxW)
		$fields[] = array(
			'name' => 'measurement_area_dimension_pricing',
			'label' => __( 'Area Dimension: Show Product Price Per Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_dimension_pricing_label',
			'label' => __( 'Area Dimension: Pricing Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_dimension_pricing_unit',
			'label' => __( 'Area Dimension: Pricing Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_dimension_length_label',
			'label' => __( 'Area Dimension: Length Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_dimension_length_unit',
			'label' => __( 'Area Dimension: Length Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_dimension_width_label',
			'label' => __( 'Area Dimension: Width Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_dimension_width_unit',
			'label' => __( 'Area Dimension: Width Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		// Perimeter
		$fields[] = array(
			'name' => 'measurement_area_linear_pricing',
			'label' => __( 'Perimeter Show Product Price Per Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_linear_pricing_label',
			'label' => __( 'Perimeter: Pricing Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_linear_pricing_unit',
			'label' => __( 'Perimeter: Pricing Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_linear_length_label',
			'label' => __( 'Perimeter: Length Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_linear_length_unit',
			'label' => __( 'Perimeter: Length Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_linear_width_label',
			'label' => __( 'Perimeter: Width Label', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);
		$fields[] = array(
			'name' => 'measurement_area_linear_width_unit',
			'label' => __( 'Perimete: Width Unit', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Measurement Price Calculator', 'woocommerce-exporter' ),
			'disabled' => 1
		);

		// Surface Area
		// Volume
		// Volume (LxWxH)
		// Volume (AxH)
		// Weight
		// Room Walls
	}

	// Custom Product meta
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				$fields[] = array(
					'name' => $custom_product,
					'label' => woo_ce_clean_export_label( $custom_product ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_product_fields_custom_product_hover', '%s: %s' ), __( 'Custom Product', 'woocommerce-exporter' ), $custom_product )
				);
			}
		}
	}
	unset( $custom_products, $custom_product );

	return $fields;

}
add_filter( 'woo_ce_product_fields', 'woo_ce_extend_product_fields' );

function woo_ce_extend_product_item( $product, $product_id ) {

	// Advanced Google Product Feed - http://plugins.leewillis.co.uk/downloads/wp-e-commerce-product-feeds/
	if( woo_ce_detect_export_plugin( 'gpf' ) ) {
		$gpf_data = get_post_meta( $product_id, '_woocommerce_gpf_data', true );
		$product->gpf_availability = ( isset( $gpf_data['availability'] ) ? woo_ce_format_gpf_availability( $gpf_data['availability'] ) : '' );
		$product->gpf_condition = ( isset( $gpf_data['condition'] ) ? woo_ce_format_gpf_condition( $gpf_data['condition'] ) : '' );
		$product->gpf_brand = ( isset( $gpf_data['brand'] ) ? $gpf_data['brand'] : '' );
		$product->gpf_product_type = ( isset( $gpf_data['product_type'] ) ? $gpf_data['product_type'] : '' );
		$product->gpf_google_product_category = ( isset( $gpf_data['google_product_category'] ) ? $gpf_data['google_product_category'] : '' );
		$product->gpf_gtin = ( isset( $gpf_data['gtin'] ) ? $gpf_data['gtin'] : '' );
		$product->gpf_mpn = ( isset( $gpf_data['mpn'] ) ? $gpf_data['mpn'] : '' );
		$product->gpf_gender = ( isset( $gpf_data['gender'] ) ? $gpf_data['gender'] : '' );
		$product->gpf_age_group = ( isset( $gpf_data['age_group'] ) ? $gpf_data['age_group'] : '' );
		$product->gpf_color = ( isset( $gpf_data['color'] ) ? $gpf_data['color'] : '' );
		$product->gpf_size = ( isset( $gpf_data['size'] ) ? $gpf_data['size'] : '' );
		unset( $gpf_data );
	}

	// All in One SEO Pack - http://wordpress.org/extend/plugins/all-in-one-seo-pack/
	if( woo_ce_detect_export_plugin( 'aioseop' ) ) {
		$product->aioseop_keywords = get_post_meta( $product_id, '_aioseop_keywords', true );
		$product->aioseop_description = get_post_meta( $product_id, '_aioseop_description', true );
		$product->aioseop_title = get_post_meta( $product_id, '_aioseop_title', true );
		$product->aioseop_title_attributes = get_post_meta( $product_id, '_aioseop_titleatr', true );
		$product->aioseop_menu_label = get_post_meta( $product_id, '_aioseop_menulabel', true );
	}

	// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
	if( woo_ce_detect_export_plugin( 'wpseo' ) ) {
		$product->wpseo_focuskw = get_post_meta( $product_id, '_yoast_wpseo_focuskw', true );
		$product->wpseo_metadesc = get_post_meta( $product_id, '_yoast_wpseo_metadesc', true );
		$product->wpseo_title = get_post_meta( $product_id, '_yoast_wpseo_title', true );
		$product->wpseo_noindex = woo_ce_format_wpseo_noindex( get_post_meta( $product_id, '_yoast_wpseo_meta-robots-noindex', true ) );
		$product->wpseo_follow = woo_ce_format_wpseo_follow( get_post_meta( $product_id, '_yoast_wpseo_meta-robots-nofollow', true ) );
		$product->wpseo_googleplus_description = get_post_meta( $product_id, '_yoast_wpseo_google-plus-description', true );
		$product->wpseo_opengraph_title = get_post_meta( $product_id, '_yoast_wpseo_opengraph-title', true );
		$product->wpseo_opengraph_description = get_post_meta( $product_id, '_yoast_wpseo_opengraph-description', true );
		$product->wpseo_opengraph_image = get_post_meta( $product_id, '_yoast_wpseo_opengraph-image', true );
		$product->wpseo_twitter_title = get_post_meta( $product_id, '_yoast_wpseo_twitter-title', true );
		$product->wpseo_twitter_description = get_post_meta( $product_id, '_yoast_wpseo_twitter-description', true );
		$product->wpseo_twitter_image = get_post_meta( $product_id, '_yoast_wpseo_twitter-image', true );
		$product->wpseo_canonical = get_post_meta( $product_id, '_yoast_wpseo_canonical', true );
	}

	// Ultimate SEO - http://wordpress.org/plugins/seo-ultimate/
	if( woo_ce_detect_export_plugin( 'ultimate_seo' ) ) {
		$product->useo_meta_title = get_post_meta( $product_id, '_su_title', true );
		$product->useo_meta_description = get_post_meta( $product_id, '_su_description', true );
		$product->useo_meta_keywords = get_post_meta( $product_id, '_su_keywords', true );
		$product->useo_social_title = get_post_meta( $product_id, '_su_og_title', true );
		$product->useo_social_description = get_post_meta( $product_id, '_su_og_description', true );
		$product->useo_meta_noindex = get_post_meta( $product_id, '_su_meta_robots_noindex', true );
		$product->useo_meta_noautolinks = get_post_meta( $product_id, '_su_disable_autolinks', true );
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( woo_ce_detect_export_plugin( 'wc_msrp' ) ) {
		$product->msrp = get_post_meta( $product_id, '_msrp_price', true );
		if( $product->msrp == false && $product->post_type == 'product_variation' )
			$product->msrp = get_post_meta( $product_id, '_msrp', true );
			// Check that a valid price has been provided and that wc_format_localized_price() exists
			if( isset( $product->msrp ) && $product->msrp != '' && function_exists( 'wc_format_localized_price' ) )
				$product->msrp = wc_format_localized_price( $product->msrp );
	}

	// Custom Product meta
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			// Check that the custom Product name is filled and it hasn't previously been set
			if( !empty( $custom_product ) && !isset( $product->{$custom_product} ) )
				$product->{$custom_product} = get_post_meta( $product_id, $custom_product, true );
		}
	}

	return $product;

}
add_filter( 'woo_ce_product_item', 'woo_ce_extend_product_item', 10, 2 );

// Returns list of Product Add-on columns
function woo_ce_get_product_addons() {

	// Product Add-ons - http://www.woothemes.com/
	if( woo_ce_detect_export_plugin( 'product_addons' ) ) {
		$post_type = 'global_product_addon';
		$args = array(
			'post_type' => $post_type,
			'numberposts' => -1
		);
		$output = array();

		// First grab the Global Product Add-ons
		$product_addons = get_posts( $args );
		if( !empty( $product_addons ) ) {
			foreach( $product_addons as $product_addon ) {
				$meta = maybe_unserialize( get_post_meta( $product_addon->ID, '_product_addons', true ) );
				if( !empty( $meta ) ) {
					$size = count( $meta );
					for( $i = 0; $i < $size; $i++ ) {
						$output[] = (object)array(
							'post_name' => $meta[$i]['name'],
							'post_title' => $meta[$i]['name'],
							'form_title' => sprintf( __( 'Global Product Add-on: %s', 'woocommerce-exporter' ), $product_addon->post_title )
						);
					}
					unset( $size );
				}
				unset( $meta );
			}
		}

		if( !empty( $output ) )
			return $output;
	}

}

// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
function woo_ce_get_wccf_product_properties() {

	$post_type = 'wccf_product_prop';
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

// WC Fields Factory - https://wordpress.org/plugins/wc-fields-factory/
function woo_ce_get_wcff_admin_fields() {

	$post_type = 'wccaf';
	$args = array(
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'fields' => 'ids'
	);
	$admin_groups = new WP_Query( $args );
	if( !empty( $admin_groups->posts ) ) {
		$admin_fields = array();
		$prefix = 'wccaf_';
		$excluded_meta = array(
			$prefix . 'condition_rules',
			$prefix . 'location_rules',
			$prefix . 'group_rules',
			$prefix . 'pricing_rules',
			$prefix . 'fee_rules',
			$prefix . 'sub_fields_group_rules'
		);
		foreach( $admin_groups->posts as $post_id ) {
			$meta = get_post_meta( $post_id );
			foreach( $meta as $key => $meta_value ) {
				// Meta name must contain the prefix
				if( preg_match( '/' . $prefix . '/', $key ) ) {
					// Skip default meta
					if( !in_array( $key, $excluded_meta ) ) {
						$meta_value = json_decode( $meta_value[0] );
						if( !is_object( $meta_value ) )
							continue;

						$admin_fields[] = array(
							'name' => $meta_value->name,
							'label' => $meta_value->label,
							'type' => $meta_value->type
						);

					}
				}
			}
		}
		unset( $admin_groups, $meta );
		return $admin_fields;
	}

}

// Advanced Google Product Feed - http://plugins.leewillis.co.uk/downloads/wp-e-commerce-product-feeds/
function woo_ce_format_gpf_availability( $availability = null ) {

	$output = '';
	if( !empty( $availability ) ) {
		switch( $availability ) {

			case 'in stock':
				$output = __( 'In Stock', 'woocommerce-exporter' );
				break;

			case 'available for order':
				$output = __( 'Available For Order', 'woocommerce-exporter' );
				break;

			case 'preorder':
				$output = __( 'Pre-order', 'woocommerce-exporter' );
				break;

		}
	}

	return $output;

}

// Advanced Google Product Feed - http://plugins.leewillis.co.uk/downloads/wp-e-commerce-product-feeds/
function woo_ce_format_gpf_condition( $condition ) {

	$output = '';
	if( !empty( $condition ) ) {
		switch( $condition ) {

			case 'new':
				$output = __( 'New', 'woocommerce-exporter' );
				break;

			case 'refurbished':
				$output = __( 'Refurbished', 'woocommerce-exporter' );
				break;

			case 'used':
				$output = __( 'Used', 'woocommerce-exporter' );
				break;

		}
	}

	return $output;

}

// Advanced Custom Fields - http://www.advancedcustomfields.com
function woo_ce_get_acf_product_fields() {

	global $wpdb;

	$post_type = 'acf';
	$args = array(
		'post_type' => $post_type,
		'numberposts' => -1
	);
	$field_groups = get_posts( $args );
	if( !empty( $field_groups ) ) {
		$fields = array();
		$post_types = array( 'product', 'product_variation' );
		foreach( $field_groups as $field_group ) {
			$has_fields = false;
			$rules = get_post_meta( $field_group->ID, 'rule' );
			if( !empty( $rules ) ) {
				$size = count( $rules );
				for( $i = 0; $i < $size; $i++ ) {
					if( ( $rules[$i]['param'] == 'post_type' ) && ( $rules[$i]['operator'] == '==' ) && ( in_array( $rules[$i]['value'], $post_types ) ) ) {
						$has_fields = true;
						$i = $size;
					}
				}
			}
			unset( $rules );

			if( !$has_fields )
				continue;

			$custom_fields_sql = "SELECT `meta_value` FROM `" . $wpdb->postmeta . "` WHERE `post_id` = " . absint( $field_group->ID ) . " AND `meta_key` LIKE 'field_%'";
			if( $custom_fields = $wpdb->get_col( $custom_fields_sql ) ) {
				foreach( $custom_fields as $custom_field ) {
					$custom_field = maybe_unserialize( $custom_field );
					$fields[] = array(
						'name' => $custom_field['name'],
						'label' => $custom_field['label']
					);
				}
			}
			unset( $custom_fields, $custom_field );
		}

		return $fields;

	}

}

// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
function woo_ce_format_wpseo_noindex( $noindex = '' ) {

	$output = $noindex;
	if( !empty( $noindex ) && $noindex !== '0' ) {
		switch( $noindex ) {

			case '0':
			case 'default':
			default:
				$output = __( 'Default', 'woocommerce-exporter' );
				break;

			case '2':
			case 'index':
				$output = __( 'Always index', 'woocommerce-exporter' );
				break;

			case '1':
			case 'noindex':
				$output = __( 'Always noindex', 'woocommerce-exporter' );
				break;

		}
	}

	return $output;

}

// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
function woo_ce_format_wpseo_follow( $follow = '' ) {

	$output = $follow;
	if( !empty( $follow ) && $follow !== '0' ) {
		switch( $follow ) {

			case '0':
			default:
				$output = __( 'follow', 'woocommerce-exporter' );
				break;

			case '1':
				$output = __( 'nofollow', 'woocommerce-exporter' );
				break;

		}
	}

	return $output;

}

// WooCommerce Wholesale Prices - https://wordpress.org/plugins/woocommerce-wholesale-prices/
function woo_ce_get_wholesale_prices_roles() {

	$output = false;
	$option_name = ( defined( 'WWP_OPTIONS_REGISTERED_CUSTOM_ROLES' ) ? WWP_OPTIONS_REGISTERED_CUSTOM_ROLES : 'wwp_options_registered_custom_roles' );
	$wholesale_roles = unserialize( get_option( $option_name ) );
	if( is_array( $wholesale_roles ) )
		$output = $wholesale_roles;
	unset( $wholesale_roles );

	return $output;

}