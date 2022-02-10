<?php
/**
 * Settings for Kogan.com.au feeds
 */
class WooSEA_kogan {
	public $kogan;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$kogan = array(
			"Feed fields" => array(
				"PRODUCT_SKU" => array(
					"name" => "PRODUCT_SKU",
					"feed_name" => "PRODUCT_SKU",
					"format" => "required",
					"woo_suggest" => "sku",
				),
				"PRODUCT_TITLE" => array(
					"name" => "PRODUCT_TITLE",
					"feed_name" => "PRODUCT_TITLE",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"PRODUCT_DESCRIPTION" => array(
					"name" => "PRODUCT_DESCRIPTION",
					"feed_name" => "PRODUCT_DESCRIPTION",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"BRAND" => array(
					"name" => "BRAND",
					"feed_name" => "BRAND",
					"format" => "required",
				),
				"CATEGORY" => array(
					"name" => "CATEGORY",
					"feed_name" => "CATEGORY",
					"format" => "required",
				),
                                "DEPARTMENT" => array(
                                        "name" => "DEPARTMENT",
                                        "feed_name" => "DEPARTMENT",
                                        "format" => "required",
                                ),
                                "STOCK" => array(
                                        "name" => "STOCK",
                                        "feed_name" => "STOCK",
                                        "format" => "required",
					"woo_suggest" => "quantity",
                                ),
                                "PRICE" => array(
                                        "name" => "PRICE",
                                        "feed_name" => "PRICE",
                                        "format" => "required",
					"woo_suggest" => "price",
                                ),
                                "SHIPPING" => array(
                                        "name" => "SHIPPING",
                                        "feed_name" => "SHIPPING",
                                        "format" => "required",
                                ),
				"IMAGES" => array(
					"name" => "IMAGES",
					"feed_name" => "IMAGES",
					"format" => "required",
					"woo_suggest" => "all_images_kogan",
				),
				"product_subtitle" => array(
					"name" => "product_subtitle",
					"feed_name" => "product_subtitle",
					"format" => "optional",
				),
				"product_inbox" => array(
					"name" => "product_inbox",
					"feed_name" => "product_inbox",
					"format" => "optional",
				),
				"product_gtin" => array(
					"name" => "product_gtin",
					"feed_name" => "product_gtin",
					"format" => "optional",
				),
				"rrp" => array(
					"name" => "rrp",
					"feed_name" => "rrp",
					"format" => "optional",
				),
				"handling_days" => array(
					"name" => "handling_days",
					"feed_name" => "handling_days",
					"format" => "optional",
				),
				"product_location" => array(
					"name" => "product_location",
					"feed_name" => "product_location",
					"format" => "optional",
				),
			),
		);
		return $kogan;
	}
}
?>
