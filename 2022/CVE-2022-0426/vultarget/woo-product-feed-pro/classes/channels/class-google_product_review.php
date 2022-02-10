<?php
/**
 * Settings for Google Product Review feeds
 */
class WooSEA_google_product_review {
	public $google_product_review;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$google_product_review = array(
			"Product review fields" => array(
				"product_name" => array(
					"name" => "product_name",
					"feed_name" => "product_name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"product_url" => array(
					"name" => "product_url",
					"feed_name" => "product_url",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"gtin" => array(
					"name" => "gtin",
					"feed_name" => "gtin",
					"format" => "required",
				),
				"mpn" => array(
					"name" => "mpn",
					"feed_name" => "mpn",
					"format" => "required",
				),
				"sku" => array(
					"name" => "sku",
					"feed_name" => "sku",
					"format" => "required",
					"woo_suggest" => "sku",
				),
				"brand" => array(
					"name" => "brand",
					"feed_name" => "brand",
					"format" => "required",
				),
				"reviews" => array(
					"name" => "reviews",
					"feed_name" => "reviews",
					"format" => "required",
					"woo_suggest" => "reviews",
				),
				"review_url" => array(
					"name" => "review_url",
					"feed_name" => "review_url",
					"format" => "required",
					"woo_suggest" => "link",
				),
			),
		);
		return $google_product_review;
	}
}
?>
