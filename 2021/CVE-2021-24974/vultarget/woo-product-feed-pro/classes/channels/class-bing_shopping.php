<?php
/**
 * Settings for Bing Shopping product feeds
 */
class WooSEA_bing_shopping {
	public $bing_attributes;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$bing_attributes = array(
			"Required fields" => array(
				"id" => array(
					"name" => "id",
					"feed_name" => "id",
					"format" => "required",
					"woo_suggest" => "id",
				),
            			"title" => array(
					"name" => "title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"link" => array(
					"name" => "link",
					"feed_name" => "link",
					"format" => "required",
					"woo_suggest" => "link",
            			),
            			"price" => array(
					"name" => "price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"description" => array(
					"name" => "description",
					"feed_name" => "description",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"image_link" => array(
					"name" => "image_link",
					"feed_name" => "image_link", 
					"format" => "required",
					"woo_suggest" => "image",
				),
				"shipping" => array(
					"name" => "shipping",
					"feed_name" => "shipping", 
					"format" => "required",
				),
			),
			"Item identification" => array(
            			"mpn" => array(
					"name" => "mpn",
					"feed_name" => "mpn", 
					"format" => "optional",
            			),
				"gtin" => array(
					"name" => "gtin",
					"feed_name" => "gtin",
					"format" => "optional",
				),
				"brand" => array(
					"name" => "brand",
					"feed_name" => "brand",
					"format" => "optional",
				),
			),
			"Apparal products" => array(
				"gender" => array(
					"name" => "gender",
					"feed_name" => "gender",
					"format" => "optional",
				),
				"age_group" => array(
					"name" => "age_group",
					"feed_name" => "age_group",
					"format" => "optional",
				),
				"color" => array(
					"name" => "color",
					"feed_name" => "color",
					"format" => "optional",
				),
				"size" => array(
					"name" => "size",
					"feed_name" => "size",
					"format" => "optional",
				),
			),
			"Product variants" => array(
				"item_group_id" => array(
					"name" => "item_group_id",
					"feed_name" => "item_group_id",
					"format" => "optional",
					"woo_suggest" => "item_group_id",
				),
				"material" => array(
					"name" => "material",
					"feed_name" => "material",
					"format" => "optional",
				),
				"pattern" => array(
					"name" => "pattern",
					"feed_name" => "pattern",
					"format" => "optional",
				),
			),
			"Other" => array(
				"adult" => array(
					"name" => "adult",
					"feed_name" => "adult",
					"format" => "optional",
				),
				"availability" => array(
					"name" => "availability",
					"feed_name" => "availability",
					"format" => "optional",
				),
				"product_category" => array(
					"name" => "product_category",
					"feed_name" => "product_category",
					"format" => "optional",
				),
				"condition" => array(
					"name" => "condition",
					"feed_name" => "condition",
					"format" => "optional",
				),
				"expiration_date" => array(
					"name" => "expiration_date",
					"feed_name" => "expiration_date",
					"format" => "optional",
				),
				"multipack" => array(
					"name" => "multipack",
					"feed_name" => "multipack",
					"format" => "optional",
				),
				"product_type" => array(
					"name" => "product_type",
					"feed_name" => "product_type",
					"format" => "optional",
				),
				"mobile_link" => array(
					"name" => "mobile_link",
					"feed_name" => "mobile_link",
					"format" => "optional",
				),
			),
			"Bing attributes" => array(
				"seller_name" => array(
					"name" => "seller_name",
					"feed_name" => "seller_name",
					"format" => "optional",
				),
				"bingads_grouping" => array(
					"name" => "bingads_grouping",
					"feed_name" => "bingads_grouping",
					"format" => "optional",
				),
				"bingads_label" => array(
					"name" => "bingads_label",
					"feed_name" => "bingads_label",
					"format" => "optional",
				),
				"bingads_redirect" => array(
					"name" => "bingads_redirect",
					"feed_name" => "bingads_redirect",
					"format" => "optional",
				),
				"custom_label_0" => array(
					"name" => "custom_label_0",
					"feed_name" => "custom_label_0",
					"format" => "optional",
				),
				"custom_label_1" => array(
					"name" => "custom_label_1",
					"feed_name" => "custom_label_1",
					"format" => "optional",
				),
				"custom_label_2" => array(
					"name" => "custom_label_2",
					"feed_name" => "custom_label_2",
					"format" => "optional",
				),
				"custom_label_3" => array(
					"name" => "custom_label_3",
					"feed_name" => "custom_label_3",
					"format" => "optional",
				),
				"custom_label_4" => array(
					"name" => "custom_label_4",
					"feed_name" => "custom_label_4",
					"format" => "optional",
				),
			),
			"Sales and promotions" => array(
				"sale_price" => array(
					"name" => "sale_price",
					"feed_name" => "sale_price",
					"format" => "optional",
				),
				"sale_price_effective_date" => array(
					"name" => "sale_price_effective_date",
					"feed_name" => "sale_price_effective_date",
					"format" => "optional",
				),
				"promotion_ID" => array(
					"name" => "promotion_ID",
					"feed_name" => "promotion_ID",
					"format" => "optional",
				),
			),
		);
		return $bing_attributes;
	}
}
?>
