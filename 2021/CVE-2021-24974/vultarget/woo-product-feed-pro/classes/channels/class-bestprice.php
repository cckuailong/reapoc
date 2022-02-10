<?php
/**
 * Settings for Bestprice feeds
 */
class WooSEA_bestprice {
	public $bestprice;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$bestprice = array(
			"Feed fields" => array(
				"productId" => array(
					"name" => "productId",
					"feed_name" => "productId",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"title" => array(
					"name" => "title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"productURL" => array(
					"name" => "productURL",
					"feed_name" => "productURL",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"imageURL" => array(
					"name" => "imageURL",
					"feed_name" => "imageURL",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"imagesURL" => array(
					"name" => "imagesURL",
					"feed_name" => "imagesURL",
					"format" => "optional",
				),
				"price" => array(
					"name" => "price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"category_path" => array(
					"name" => "category_path",
					"feed_name" => "category_path",
					"format" => "required",
					"woo_suggest" => "category_path_short",
				),
				"category_id" => array(
					"name" => "category_id",
					"feed_name" => "category_id",
					"format" => "optional",
				),
                                "availability" => array(
                                        "name" => "availability",
                                        "feed_name" => "availability",
                                        "format" => "required",
					"woo_suggest" => "availability",
                                ),
                                "stock" => array(
                                        "name" => "stock",
                                        "feed_name" => "stock",
                                        "format" => "optional",
                                ),
                                "brand" => array(
                                        "name" => "brand",
                                        "feed_name" => "brand",
                                        "format" => "required",
                                ),
                                "EAN" => array(
                                        "name" => "EAN",
                                        "feed_name" => "EAN",
                                        "format" => "required",
                                ),
                                "Barcode" => array(
                                        "name" => "Barcode",
                                        "feed_name" => "Barcode",
                                        "format" => "required",
                                ),
                                "MPN" => array(
                                        "name" => "MPN",
                                        "feed_name" => "MPN",
                                        "format" => "required",
                                ),
                                "SKU" => array(
                                        "name" => "SKU",
                                        "feed_name" => "SKU",
                                        "format" => "required",
                                ),
                                "size" => array(
                                        "name" => "size",
                                        "feed_name" => "size",
                                        "format" => "optional",
                                ),
                                "color" => array(
                                        "name" => "color",
                                        "feed_name" => "color",
                                        "format" => "optional",
                                ),
                                "warranty_provider" => array(
                                        "name" => "warranty_provider",
                                        "feed_name" => "warranty_provider",
                                        "format" => "optional",
                                ),
                                "warranty_duration" => array(
                                        "name" => "warranty_duration",
                                        "feed_name" => "warranty_duration",
                                        "format" => "optional",
                                ),
                                "isBundle" => array(
                                        "name" => "isBundle",
                                        "feed_name" => "isBundle",
                                        "format" => "optional",
                                ),
                                "features" => array(
                                        "name" => "features",
                                        "feed_name" => "features",
                                        "format" => "optional",
                                ),
                                "weight" => array(
                                        "name" => "weight",
                                        "feed_name" => "weight",
                                        "format" => "optional",
                                ),
                                "shipping" => array(
                                        "name" => "shipping",
                                        "feed_name" => "shipping",
                                        "format" => "optional",
                                ),
			),
		);
		return $bestprice;
	}
}
?>
