<?php
/**
 * Settings for Katoni feeds
 */
class WooSEA_katoni {
	public $katoni;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$katoni = array(
			"Feed fields" => array(
				"GTIN" => array(
					"name" => "gtin",
					"feed_name" => "gtin",
					"format" => "required",
				),
				"Item Group ID" => array(
					"name" => "item_group_id",
					"feed_name" => "item_group_id",
					"format" => "required",
					"woo_suggest" => "item_group_id",
				),
                                "Product ID" => array(
                                        "name" => "id",
                                        "feed_name" => "g:id",
                                        "format" => "required",
                                        "woo_suggest" => "id",
                                ),
				"Brand" => array(
					"name" => "brand",
					"feed_name" => "brand",
					"format" => "required",
				),
				"Title" => array(
					"name" => "title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Product Type" => array(
					"name" => "product_type",
					"feed_name" => "product_type",
					"format" => "required",
				),
				"Gender" => array(
					"name" => "gender",
					"feed_name" => "gender",
					"format" => "required",
				),
				"Size" => array(
					"name" => "size",
					"feed_name" => "size",
					"format" => "required",
				),
        			"Image link" => array(
					"name" => "image_link",
					"feed_name" => "image_link",
					"format" => "required",
					"woo_suggest" => "image",
				),
                                "Additional image link" => array(
                                        "name" => "additional_image_link",
                                        "feed_name" => "additional_image_link",
                                        "format" => "optional",
                                ),
                                "Availability" => array(
                                        "name" => "availability",
                                        "feed_name" => "availability",
                                        "format" => "optional",
					"woo_suggest" => "availability",
				),
                                "Stock level" => array(
                                        "name" => "stock_level",
                                        "feed_name" => "stock_level",
                                        "format" => "optional",
                                ),
                                "Season" => array(
                                        "name" => "season",
                                        "feed_name" => "season",
                                        "format" => "optional",
                                ),
                                "Description" => array(
                                        "name" => "description",
                                        "feed_name" => "description",
                                        "format" => "required",
					"woo_suggest" => "description",
                                ),
                                "Material" => array(
                                        "name" => "material",
                                        "feed_name" => "material",
                                        "format" => "optional",
                                ),
                                "Washing" => array(
                                        "name" => "washing",
                                        "feed_name" => "washing",
                                        "format" => "optional",
                                ),
                           	"Discount retail price" => array(
                                        "name" => "discount_retail_price",
                                        "feed_name" => "discount_retail_price",
                                        "format" => "optional",
                                ),
                     		"Retail price" => array(
                                        "name" => "retail_price",
                                        "feed_name" => "retail_price",
                                        "format" => "required",
					"woo_suggest" => "price",
                                ),
                     		"Wholsesale price" => array(
                                        "name" => "wholesale_price",
                                        "feed_name" => "wholesale_price",
                                        "format" => "optional",
                                ),
			),
		);
		return $katoni;
	}
}
?>
