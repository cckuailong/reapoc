<?php
/**
 * Settings for Shopmania Romania feeds
 */
class WooSEA_shopmania_ro {
	public $shopmania_ro;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$shopmania_ro = array(
			"Feed fields" => array(
                                "MPC" => array(
                                        "name" => "MPC",
                                        "feed_name" => "MPC",
                                        "format" => "required",
                                        "woo_suggest" => "id",
                                ),
				"Category" => array(
					"name" => "Category",
					"feed_name" => "Category",
					"format" => "required",
					"woo_suggest" => "categories",
				),
				"Manufacturer" => array(
					"name" => "Manufacturer",
					"feed_name" => "Manufacturer",
					"format" => "required",
				),
				"MPN" => array(
					"name" => "MPN",
					"feed_name" => "MPN",
					"format" => "required",
				),
				"Name" => array(
					"name" => "Name",
					"feed_name" => "Name",
					"format" => "required",
					"woo_suggest" => "title",
				),
                                "Description" => array(
                                        "name" => "Description",
                                        "feed_name" => "Description",
                                        "format" => "optional",
                                        "woo_suggest" => "description",
                                ),
				"URL" => array(
					"name" => "URL",
					"feed_name" => "URL",
					"format" => "required",
					"woo_suggest" => "link",
				),
        			"Image" => array(
					"name" => "Image",
					"feed_name" => "Image",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Price" => array(
					"name" => "Price",
					"feed_name" => "Price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Currency" => array(
					"name" => "Currency",
					"feed_name" => "Currency",
					"format" => "required",
				),
				"Shipping" => array(
					"name" => "Shipping",
					"feed_name" => "Shipping",
					"format" => "required",
				),
				"Availability" => array(
					"name" => "Availability",
					"feed_name" => "Availability",
					"format" => "required",
                                        "woo_suggest" => "availability",
				),
				"GTIN" => array(
					"name" => "GTIN",
					"feed_name" => "GTIN",
					"format" => "required",
				),
			),
		);
		return $shopmania_ro;
	}
}
?>
