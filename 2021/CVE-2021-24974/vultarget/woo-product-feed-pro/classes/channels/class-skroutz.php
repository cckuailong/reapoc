<?php
/**
 * Settings for Skroutz feeds
 */
class WooSEA_skroutz {
	public $skroutz;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$skroutz = array(
			"Feed fields" => array(
				"ID" => array(
					"name" => "id",
					"feed_name" => "id",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Name" => array(
					"name" => "name",
					"feed_name" => "name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Link" => array(
					"name" => "link",
					"feed_name" => "link",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"Image" => array(
					"name" => "image",
					"feed_name" => "image",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Additional Image" => array(
					"name" => "additionalimage",
					"feed_name" => "additionalimage",
					"format" => "optional",
				),
				"Category Name" => array(
					"name" => "category name",
					"feed_name" => "category",
					"format" => "required",
					"woo_suggest" => "category_path_skroutz",
				),
				"Category Path" => array(
					"name" => "category path",
					"feed_name" => "category_path",
					"format" => "required",
					"woo_suggest" => "category_path_short",
				),
				"Price with VAT" => array(
					"name" => "price with vat",
					"feed_name" => "price_with_vat",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Manufacturer" => array(
					"name" => "manufacturer",
					"feed_name" => "manufacturer",
					"format" => "required",
				),
        			"MPN" => array(
					"name" => "mpn/ isbn",
					"feed_name" => "mpn",
					"format" => "required",
				),
                                "EAN" => array(
                                        "name" => "ean",
                                        "feed_name" => "ean",
                                        "format" => "optional",
                                ),
                                "instock" => array(
                                        "name" => "instock",
                                        "feed_name" => "instock",
                                        "format" => "optional",
                                ),
                                "shipping costs" => array(
                                        "name" => "shipping costs",
                                        "feed_name" => "shipping_costs",
                                        "format" => "optional",
                                ),
                                "availability" => array(
                                        "name" => "availability",
                                        "feed_name" => "availability",
                                        "format" => "required",
					"woo_suggest" => "availability",
                                ),
                                "size" => array(
                                        "name" => "size",
                                        "feed_name" => "size",
                                        "format" => "required",
                                ),
                                "weight" => array(
                                        "name" => "weight",
                                        "feed_name" => "weight",
                                        "format" => "required",
                                ),
                                "color" => array(
                                        "name" => "color",
                                        "feed_name" => "color",
                                        "format" => "required",
                                ),
			),
		);
		return $skroutz;
	}
}
?>
