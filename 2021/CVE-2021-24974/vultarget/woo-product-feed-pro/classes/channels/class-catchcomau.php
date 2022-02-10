<?php
/**
 * Settings for Catch.com.au feeds
 */
class WooSEA_catchcomau {
	public $catchcomau;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$catchcomau = array(
			"Feed fields" => array(
				"Product ID" => array(
					"name" => "Product ID",
					"feed_name" => "product-id",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Product ID Type" => array(
					"name" => "Product ID Type",
					"feed_name" => "product-id-type",
					"format" => "optional",
				),
				"Category" => array(
					"name" => "Category",
					"feed_name" => "category",
					"format" => "required",
					"woo_suggest" => "category",
				),
				"Internal SKU" => array(
					"name" => "Internal SKU",
					"feed_name" => "internal-sku",
					"format" => "required",
					"woo_suggest" => "SKU",
				),
				"Product title" => array(
					"name" => "Product title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "title",
				),
                                "Product reference type" => array(
                                        "name" => "Product reference type",
                                        "feed_name" => "product-reference-type",
                                        "format" => "required",
                                ),
                                "Product reference value" => array(
                                        "name" => "Product reference value",
                                        "feed_name" => "product-reference-value",
                                        "format" => "required",
                                ),
                                "Variant ID" => array(
                                        "name" => "Variant ID",
                                        "feed_name" => "variant-id",
                                        "format" => "optional",
                                ),
                                "Variant Size Value" => array(
                                        "name" => "Variant Size Value",
                                        "feed_name" => "variant-size-value",
                                        "format" => "optional",
                                ),
				"Image 1" => array(
					"name" => "Image 1",
					"feed_name" => "image-1",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Image 2" => array(
					"name" => "Image 2",
					"feed_name" => "image-2",
					"format" => "optional",
				),
				"Image 3" => array(
					"name" => "Image 3",
					"feed_name" => "image-3",
					"format" => "optional",
				),
				"Image 4" => array(
					"name" => "Image 4",
					"feed_name" => "image-4",
					"format" => "optional",
				),
				"Image 5" => array(
					"name" => "Image 5",
					"feed_name" => "image-5",
					"format" => "optional",
				),
				"Image 6" => array(
					"name" => "Image 6",
					"feed_name" => "image-6",
					"format" => "optional",
				),
				"Image Size Chart" => array(
					"name" => "Image Size Chart",
					"feed_name" => "image-size-chart",
					"format" => "optional",
				),
				"Description" => array(
					"name" => "Description",
					"feed_name" => "product-description",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"Brand" => array(
					"name" => "Brand",
					"feed_name" => "brand",
					"format" => "required",
				),
				"Adult" => array(
					"name" => "Adult",
					"feed_name" => "adult",
					"format" => "required",
				),
                                "Keywords" => array(
                                        "name" => "Keywords",
                                        "feed_name" => "keywords",
                                        "format" => "optional",
                                ),
                                "Offer SKU" => array(
                                        "name" => "Offer SKU",
                                        "feed_name" => "sku",
                                        "format" => "required",
                                ),
                                "Offer Price" => array(
                                        "name" => "Offer Price",
                                        "feed_name" => "price",
                                        "format" => "required",
					"woo_suggest" => "price",
                                ),
                                "Offer Quantity" => array(
                                        "name" => "Offer Quantity",
                                        "feed_name" => "quantity",
                                        "format" => "required",
					"woo_suggest" => "quantity",
                                ),
                                "Minimum Quantity Alert" => array(
                                        "name" => "Minimum Quantity Alert",
                                        "feed_name" => "min-quantity-alert",
                                        "format" => "optional",
                                ),
                                "Offer State" => array(
                                        "name" => "Offer State",
                                        "feed_name" => "state",
                                        "format" => "required",
                                        "woo_suggest" => "condition",
                                ),
                                "Logistic Class" => array(
                                        "name" => "Logistic Class",
                                        "feed_name" => "logistic-class",
                                        "format" => "required",
                                ),
                                "Discount Price" => array(
                                        "name" => "Discount Price",
                                        "feed_name" => "discount-price",
                                        "format" => "optional",
					"woo_suggest" => "sale_price",
                                ),
                                "Lead Time to Ship" => array(
                                        "name" => "Lead Time to Ship",
                                        "feed_name" => "leadtime-to-ship",
                                        "format" => "required",
                                ),
                                "Club Catch eligible" => array(
                                        "name" => "Club Catch eligible",
                                        "feed_name" => "club-catch-eligible",
                                        "format" => "required",
                                ),
                                "GST %" => array(
                                        "name" => "GST %",
                                        "feed_name" => "tax-au",
                                        "format" => "required",
                                ),
			),
		);
		return $catchcomau;
	}
}
?>
