<?php
/**
 * Settings for Snapchat Product Catalog feeds
 */
class WooSEA_snapchat {
	public $snapchat;

        public static function get_channel_attributes() {
                $sitename = get_option('blogname');

        	$snapchat = array(
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
			),
			"App product metadata" => array(
				"Icon media URL" => array(
					"name" => "icon_media_url",
					"feed_name" => "icon_media_url", 
					"format" => "optional",
				),
				"IOS app name" => array(
					"name" => "ios_app_name",
					"feed_name" => "ios_app_name", 
					"format" => "optional",
				),
				"IOS app store ID" => array(
					"name" => "ios_app_store_id",
					"feed_name" => "ios_app_store_id", 
					"format" => "optional",
				),
				"IOS URL" => array(
					"name" => "ios_url",
					"feed_name" => "ios_url", 
					"format" => "optional",
				),
				"Android app name" => array(
					"name" => "android_app_name",
					"feed_name" => "android_app_name", 
					"format" => "optional",
				),
				"Android package" => array(
					"name" => "android_package",
					"feed_name" => "android_package", 
					"format" => "optional",
				),
				"Android URL" => array(
					"name" => "android_url",
					"feed_name" => "android_url", 
					"format" => "optional",
				),
				"Mobile URL" => array(
					"name" => "mobile_link",
					"feed_name" => "mobile_link", 
					"format" => "optional",
				),
			),
			"Price & availability" => array(
            			"Stock status" => array(
					"name" => "availability",
					"feed_name" => "availability", 
					"format" => "required",
					"woo_suggest" => "availability",
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
					"woo_suggest" => "sale_price_effective_date",
				),
				"Address" => array(
					"name" => "address",
					"feed_name" => "address",
					"format" => "optional",
				),
			),
			"Product category" => array(
				"Google product category" => array(
					"name" => "google_product_category",
					"feed_name" => "google_product_category",
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
					"format" => "required",
				),
				"MPN" => array(
					"name" => "mpn",
					"feed_name" => "mpn",
					"format" => "required",
				),
			),
			"Detailed product description" => array(
				"Condition" => array(
					"name" => "condition",
					"feed_name" => "condition",
					"format" => "required",
					"woo_suggest" => "condition",
				),
				"Adult" => array(
					"name" => "adult",
					"feed_name" => "adult",
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
				"Size" => array(
					"name" => "size",
					"feed_name" => "size",
					"format" => "optional",
				),
				"Item group ID" => array(
					"name" => "item_group_id",
					"feed_name" => "item_group_id",
					"format" => "required",
					"woo_suggest" => "item_group_id",
				),
			),
			"Custom labels" => array(
				"Custom label 0" => array(
					"name" => "custom_label_0",
					"feed_name" => "custom_label_0",
					"format" => "optional",
				),
				"Custom label 1" => array(
					"name" => "custom_label_1",
					"feed_name" => "custom_label_1",
					"format" => "optional",
				),
				"Custom label 2" => array(
					"name" => "custom_label_2",
					"feed_name" => "custom_label_2",
					"format" => "optional",
				),
				"Custom label 3" => array(
					"name" => "custom_label_3",
					"feed_name" => "custom_label_3",
					"format" => "optional",
				),
				"Custom label 4" => array(
					"name" => "custom_label_4",
					"feed_name" => "custom_label_4",
					"format" => "optional",
				),
			),
		);
		return $snapchat;
	}
}
?>
