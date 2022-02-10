<?php
/**
 * Settings for ZAP israel feeds
 */
class WooSEA_zap {
	public $zap;

        public static function get_channel_attributes() {
                $sitename = get_option('blogname');

        	$zap = array(
			"Feed fields" => array(
				"Product URL" => array(
					"name" => "PRODUCT_URL",
					"feed_name" => "PRODUCT_URL",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"Product Name" => array(
					"name" => "PRODUCT_NAME",
					"feed_name" => "PRODUCT_NAME",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Product Type" => array(
					"name" => "PRODUCT_TYPE",
					"feed_name" => "PRODUCT_TYPE",
					"format" => "optional",
				),
				"Model" => array(
					"name" => "MODEL",
					"feed_name" => "MODEL",
					"format" => "optional",
				),
				"Details" => array(
					"name" => "DETAILS",
					"feed_name" => "DETAILS",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"Catalog Number" => array(
					"name" => "CATALOG_NUMBER",
					"feed_name" => "CATALOG_NUMBER",
					"format" => "optional",
				),
                                "Productcode" => array(
                                        "name" => "PRODUCTCODE",
                                        "feed_name" => "PRODUCTCODE",
                                        "format" => "required",
                                        "woo_suggest" => "id",
                                ),
                                "Currency" => array(
                                        "name" => "CURRENCY",
                                        "feed_name" => "CURRENCY",
                                        "format" => "required",
                                ),
                                "Price" => array(
                                        "name" => "PRICE",
                                        "feed_name" => "PRICE",
                                        "format" => "required",
                                        "woo_suggest" => "price",
                                ),
                                "Open Price" => array(
                                        "name" => "OPEN_PRICE",
                                        "feed_name" => "OPEN_PRICE",
                                        "format" => "optional",
                                ),
                                "Shipment Cost" => array(
                                        "name" => "SHIPMENT_COST",
                                        "feed_name" => "SHIPMENT_COST",
                                        "format" => "required",
                                ),
                                "Delivery Time" => array(
                                        "name" => "DELIVERY_TIME",
                                        "feed_name" => "DELIVERY_TIME",
                                        "format" => "required",
                                ),
                                "Manufacturer" => array(
                                        "name" => "MANUFACTURER",
                                        "feed_name" => "MANUFACTURER",
                                        "format" => "optional",
                                ),
                                "Warrenty" => array(
                                        "name" => "WARRENTY",
                                        "feed_name" => "WARRENTY",
                                        "format" => "optional",
                                ),
                                "Image" => array(
                                        "name" => "IMAGE",
                                        "feed_name" => "IMAGE",
                                        "format" => "required",
					"woo_suggest" => "image",
                                ),
                                "Tax" => array(
                                        "name" => "TAX",
                                        "feed_name" => "TAX",
                                        "format" => "optional",
                               	),
			),
		);
		return $zap;
	}
}
?>
