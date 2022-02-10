<?php
/**
 * Settings for Custom product feeds
 */
class WooSEA_customfeed {
	public $customfeed;

        public static function get_channel_attributes() {
                $sitename = get_option('blogname');

        	$customfeed = array(
			"Basic product data" => array(
				"Product ID" => array(
					"name" => "id",
					"feed_name" => "id",
					"format" => "required",
					"woo_suggest" => "id",
				),
            			"Product title" => array(
					"name" => "title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "title",
				),
            			"Product description" => array(
					"name" => "description",
					"feed_name" => "description",
					"format" => "required",
					"woo_suggest" => "description",
            			),
				"Product URL" => array(
					"name" => "link",
					"feed_name" => "link",
					"format" => "required",
					"woo_suggest" => "link",
            			),
            			"Main image URL" => array(
					"name" => "image_link",
					"feed_name" => "image_link",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Additional image URL" => array(
					"name" => "additional_image_link",
					"feed_name" => "additional_image_link",
					"format" => "optional",
				),
				"Product URL mobile" => array(
					"name" => "mobile_link",
					"feed_name" => "mobile_link", 
					"format" => "optional",
				),
			),
			"Price & availability" => array(
            			"Stock status" => array(
					"name" => "availability",
					"feed_name" => "availability", 
					"format" => "optional",
					"woo_suggest" => "availability",
            			),
				"Availability date" => array(
					"name" => "availability_date",
					"feed_name" => "availability_date",
					"format" => "optional",
				),
				"Expiration date" => array(
					"name" => "expiration_date",
					"feed_name" => "expiration_date",
					"format" => "optional",
				),
				"Price" => array(
					"name" => "Price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Sale price" => array(
					"name" => "sale_price",
					"feed_name" => "sale_price",
					"format" => "optional",
					"woo_suggest" => "sale_price",
				),
				"Sale price effective date" => array(
					"name" => "sale_price_effective_date",
					"feed_name" => "sale_price_effective_date",
					"format" => "optional",
				),
				"Unit pricing measure" => array(
					"name" => "unit_pricing_measure",
					"feed_name" => "unit_pricing_measure",
					"format" => "optional",
				),
				"Unit pricing base measure" => array(
					"name" => "unit_pricing_measure",
					"feed_name" => "unit_pricing_measure",
					"format" => "optional",
				),
				"Installment" => array(
					"name" => "installment",
					"feed_name" => "installment",
					"format" => "optional",
				),
				"Loyalty points" => array(
					"name" => "loyalty_points",
					"feed_name" => "loyalty_points",
					"format" => "optional",
				),
			),
			"Product category" => array(
				"Categories" => array(
					"name" => "categories",
					"feed_name" => "categories",
					"format" => "required",
					"woo_suggest" => "categories",
				),
				"Product type" => array(
					"name" => "product_type",
					"feed_name" => "product_type",
					"format" => "optional",
					"woo_suggest" => "product_type",
				),
			),
			"Product identifiers" => array(
				"Brand" => array(
					"name" => "brand",
					"feed_name" => "brand",
					"format" => "required",
				),
				"Gtin" => array(
					"name" => "gtin",
					"feed_name" => "gtin",
					"format" => "optional",
				),
				"MPN" => array(
					"name" => "mpn",
					"feed_name" => "mpn",
					"format" => "optional",
				),
				"EAN" => array(
					"name" => "ean",
					"feed_name" => "ean",
					"format" => "optional",
				),
			),
			"Detailed product description" => array(
				"Condition" => array(
					"name" => "condition",
					"feed_name" => "condition",
					"format" => "optional",
					"woo_suggest" => "condition",
				),
				"Adult" => array(
					"name" => "adult",
					"feed_name" => "adult",
					"format" => "optional",
				),
				"Multipack" => array(
					"name" => "multipack",
					"feed_name" => "multipack",
					"format" => "optional",
				),
				"Is bundle" => array(
					"name" => "is_bundle",
					"feed_name" => "is_bundle",
					"format" => "optional",
				),
				"Energy efficiency class" => array(
					"name" => "energy_efficiency_class",
					"feed_name" => "energy_efficiency_class",
					"format" => "optional",
				),
				"Age group" => array(
					"name" => "age_group",
					"feed_name" => "age_group",
					"format" => "optional",
				),
				"Color" => array(
					"name" => "color",
					"feed_name" => "color",
					"format" => "optional",
				),
				"Gender" => array(
					"name" => "gender",
					"feed_name" => "gender",
					"format" => "optional",
				),
				"Material" => array(
					"name" => "material",
					"feed_name" => "material",
					"format" => "optional",
				),
				"Pattern" => array(
					"name" => "pattern",
					"feed_name" => "pattern",
					"format" => "optional",
				),
				"Size" => array(
					"name" => "size",
					"feed_name" => "size",
					"format" => "optional",
				),
				"Size type" => array(
					"name" => "size_type",
					"feed_name" => "size_type",
					"format" => "optional",
				),
				"Size system" => array(
					"name" => "size_system",
					"feed_name" => "size_system",
					"format" => "optional",
				),
				"Item group ID" => array(
					"name" => "item_group_id",
					"feed_name" => "item_group_id",
					"format" => "optional",
				),
			),
			"Shipping" => array(
				"Shipping" => array(
					"name" => "shipping",
					"feed_name" => "shipping",
					"format" => "optional",
				),
				"Shipping label" => array(
					"name" => "shipping_label",
					"feed_name" => "shipping_label",
					"format" => "optional",
				),
				"Shipping weight" => array(
					"name" => "shipping_weight",
					"feed_name" => "shipping_weight",
					"format" => "optional",
				),
				"Shipping length" => array(
					"name" => "shipping_length",
					"feed_name" => "shipping_length",
					"format" => "optional",
				),
				"Shipping width" => array(
					"name" => "shipping_width",
					"feed_name" => "shipping_width",
					"format" => "optional",
				),
				"Shipping height" => array(
					"name" => "shipping_height",
					"feed_name" => "shipping_height",
					"format" => "optional",
				),
			),
			"Tax" => array(
				"Tax" => array(
					"name" => "tax",
					"feed_name" => "tax",
					"format" => "optional",
				),
			),
		);
		return $customfeed;
	}
}
?>
