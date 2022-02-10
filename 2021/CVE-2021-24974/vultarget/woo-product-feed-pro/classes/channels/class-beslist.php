<?php
/**
 * Settings for Beslist feeds
 */
class WooSEA_beslist {
	public $beslist;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$beslist = array(
			"Feed fields" => array(
				"Title" => array(
					"name" => "title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Price" => array(
					"name" => "price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Unique code" => array(
					"name" => "unique_code",
					"feed_name" => "unique_code",
					"format" => "required",
					"woo_suggest" => "id",
				),
                                "Product URL" => array(
                                        "name" => "product_url",
                                        "feed_name" => "product_url",
                                        "format" => "required",
                                        "woo_suggest" => "link",
                                ),
				"Image URL" => array(
					"name" => "image_url",
					"feed_name" => "image_url",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Extra image 1" => array(
					"name" => "extra_image_1",
					"feed_name" => "extra_image_1",
					"format" => "optional",
				),
				"Extra image 2" => array(
					"name" => "extra_image_2",
					"feed_name" => "extra_image_2",
					"format" => "optional",
				),
				"Extra image 3" => array(
					"name" => "extra_image_3",
					"feed_name" => "extra_image_3",
					"format" => "optional",
				),
				"Extra image 4" => array(
					"name" => "extra_image_4",
					"feed_name" => "extra_image_4",
					"format" => "optional",
				),
				"Extra image 5" => array(
					"name" => "extra_image_5",
					"feed_name" => "extra_image_5",
					"format" => "optional",
				),
				"Extra image 6" => array(
					"name" => "extra_image_6",
					"feed_name" => "extra_image_6",
					"format" => "optional",
				),
				"Extra image 7" => array(
					"name" => "extra_image_7",
					"feed_name" => "extra_image_7",
					"format" => "optional",
				),
				"Extra image 8" => array(
					"name" => "extra_image_8",
					"feed_name" => "extra_image_8",
					"format" => "optional",
				),
				"Extra image 9" => array(
					"name" => "extra_image_9",
					"feed_name" => "extra_image_9",
					"format" => "optional",
				),
                                "Category" => array(
                                        "name" => "category",
                                        "feed_name" => "category",
                                        "format" => "required",
					"woo_suggest" => "category_path_short",
                                ),
                                "Delivery period" => array(
                                        "name" => "delivery_period",
                                        "feed_name" => "delivery_period",
                                        "format" => "required",
					"woo_suggest" => "",
                                ),
                                "Delivery charges" => array(
                                        "name" => "delivery_charges",
                                        "feed_name" => "delivery_charges",
                                        "format" => "required",
                                ),
                                "EAN" => array(
                                        "name" => "EAN",
                                        "feed_name" => "EAN",
                                        "format" => "required",
                                ),
                                "Product description" => array(
                                        "name" => "description",
                                        "feed_name" => "description",
                                        "format" => "required",
                                        "woo_suggest" => "description",
                                ),
                                "Display" => array(
                                        "name" => "display",
                                        "feed_name" => "display",
                                        "format" => "optional",
                                ),
                                "SKU" => array(
                                        "name" => "SKU",
                                        "feed_name" => "SKU",
                                        "format" => "optional",
                                ),
                                "Brand" => array(
                                        "name" => "Brand",
                                        "feed_name" => "brand",
                                        "format" => "optional",
                                ),
                                "Size" => array(
                                        "name" => "size",
                                        "feed_name" => "size",
                                        "format" => "optional",
                                ),
                                "Condition" => array(
                                        "name" => "condition",
                                        "feed_name" => "condition",
                                        "format" => "optional",
                                        "woo_suggest" => "condition",
                                ),
                                "Variant code" => array(
                                        "name" => "variant_code",
                                        "feed_name" => "variant_code",
                                        "format" => "optional",
                                ),
                                "Model code" => array(
                                        "name" => "model_code",
                                        "feed_name" => "model_code",
                                        "format" => "optional",
                                ),
                                "Old price" => array(
                                        "name" => "old_price",
                                        "feed_name" => "old_price",
                                        "format" => "optional",
                                ),
			),
		);
		return $beslist;
	}
}
?>
