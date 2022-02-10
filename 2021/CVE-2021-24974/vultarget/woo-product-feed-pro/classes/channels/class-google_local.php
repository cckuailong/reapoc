<?php
/**
 * Settings for Google Local Product Inventory feeds
 */
class WooSEA_google_local {
	public $google_local;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$google_local = array(
			"Local product inventory fields" => array(
				"Itemid" => array(
					"name" => "id",
					"feed_name" => "id",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Store code" => array(
					"name" => "Store code",
					"feed_name" => "store code",
					"format" => "required",
				),
				"Quantity" => array(
					"name" => "Quantity",
					"feed_name" => "quantity",
					"format" => "required",
					"woo_suggest" => "quantity",
				),
				"Price" => array(
					"name" => "Price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Sale price" => array(
					"name" => "Sale price",
					"feed_name" => "Sale price",
					"format" => "optional",
					"woo_suggest" => "sale_price",
				),
                                "Sale price effective date" => array(
                                        "name" => "Sale price effective date",
                                        "feed_name" => "sale price effective date",
                                        "format" => "optional",
                                        "woo_suggest" => "sale_price_effective_date",
                                ),
                                "Availability" => array(
                                        "name" => "Availability",
                                        "feed_name" => "availability",
                                        "format" => "optional",
                                        "woo_suggest" => "availability",
                                ),
                                "Weeks of supply" => array(
                                        "name" => "Weeks of supply",
                                        "feed_name" => "weeks of supply",
                                        "format" => "optional",
                                ),
                                "Pickup method" => array(
                                        "name" => "Pickup method",
                                        "feed_name" => "pickup method",
                                        "format" => "optional",
                                ),
                                "Pickup sla" => array(
                                        "name" => "Pickup sla",
                                        "feed_name" => "pickup sla",
                                        "format" => "optional",
                                ),
                                "Webitemid" => array(
                                        "name" => "Webitemid",
                                        "feed_name" => "webitemid",
                                        "format" => "optional",
                                ),
			),
		);
		return $google_local;
	}
}
?>
