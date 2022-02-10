<?php
/**
 * Settings for Daisycon huis & tuin feeds
 */
class WooSEA_daisyconhuisentuin {
	public $daisyconhuisentuin;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$daisyconhuisentuin = array(
			"Feed fields" => array(
				"description" => array(
					"name" => "description",
					"feed_name" => "description",
					"format" => "required",
					"woo_suggest" => "description",
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
				"sku" => array(
					"name" => "sku",
					"feed_name" => "sku",
					"format" => "required",
					"woo_suggest" => "sku",
				),
				"title" => array(
					"name" => "title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"additional_costs" => array(
					"name" => "additional_costs",
					"feed_name" => "additional_costs",
					"format" => "optional",
				),
				"brand" => array(
					"name" => "brand",
					"feed_name" => "brand",
					"format" => "optional",
				),
				"brand_logo" => array(
					"name" => "brand_logo",
					"feed_name" => "brand_log",
					"format" => "optional",
				),
				"category" => array(
					"name" => "category",
					"feed_name" => "category",
					"format" => "optional",
				),
				"category_path" => array(
					"name" => "category_path",
					"feed_name" => "category_path",
					"format" => "optional",
					"woo_suggest" => "category_path",
				),
				"color_primary" => array(
					"name" => "color_primary",
					"feed_name" => "color_primary",
					"format" => "optional",
				),
				"condition" => array(
					"name" => "condition",
					"feed_name" => "condition",
					"format" => "optional",
					"woo_suggest" => "condition",
				),
				"delivery_description" => array(
					"name" => "delivery_description",
					"feed_name" => "delivery_description",
					"format" => "optional",
				),
				"delivery_time" => array(
					"name" => "delivery_time",
					"feed_name" => "delivery_time",
					"format" => "optional",
				),
				"designer" => array(
					"name" => "designer",
					"feed_name" => "designer",
					"format" => "optional",
				),
				"ean" => array(
					"name" => "ean",
					"feed_name" => "ean",
					"format" => "optional",
				),
				"gender_target" => array(
					"name" => "gender_target",
					"feed_name" => "gender_target",
					"format" => "optional",
				),
				"google_category_id" => array(
					"name" => "google_category_id",
					"feed_name" => "google_category_id",
					"format" => "optional",
					"woo_suggest" => "category",
				),
				"in_stock" => array(
					"name" => "in_stock",
					"feed_name" => "in_stock",
					"format" => "optional",
					"woo_suggest" => "stock",
				),
				"in_stock_amount" => array(
					"name" => "in_stock_amount",
					"feed_name" => "in_stock_amount",
					"format" => "optional",
				),
				"keywords" => array(
					"name" => "keywords",
					"feed_name" => "keywords",
					"format" => "optional",
				),
				"made_in_country" => array(
					"name" => "made_in_country",
					"feed_name" => "made_in_country",
					"format" => "optional",
				),
				"material_1" => array(
					"name" => "material_1",
					"feed_name" => "material_1",
					"format" => "optional",
				),
				"material_2" => array(
					"name" => "material_2",
					"feed_name" => "material_2",
					"format" => "optional",
				),
				"material_3" => array(
					"name" => "material_3",
					"feed_name" => "material_3",
					"format" => "optional",
				),
				"model" => array(
					"name" => "model",
					"feed_name" => "model",
					"format" => "optional",
				),
				"price_old" => array(
					"name" => "price_old",
					"feed_name" => "price_old",
					"format" => "optional",
				),
				"price_shipping" => array(
					"name" => "price_shipping",
					"feed_name" => "price_shipping",
					"format" => "optional",
				),
				"priority" => array(
					"name" => "priority",
					"feed_name" => "priority",
					"format" => "optional",
				),
				"size" => array(
					"name" => "size",
					"feed_name" => "size",
					"format" => "optional",
				),
				"size_description" => array(
					"name" => "size_description",
					"feed_name" => "size_description",
					"format" => "optional",
				),
				"size_length" => array(
					"name" => "size_length",
					"feed_name" => "size_length",
					"format" => "optional",
				),
				"size_width" => array(
					"name" => "size_width",
					"feed_name" => "size_width",
					"format" => "optional",
				),
				"terms_condition" => array(
					"name" => "terms_condition",
					"feed_name" => "terms_condition",
					"format" => "optional",
				),
				"weight" => array(
					"name" => "weight",
					"feed_name" => "weight",
					"format" => "optional",
				),
				"image_link_1" => array(
					"name" => "image_link_1",
					"feed_name" => "image_link_1",
					"format" => "optional",
				),
				"image_link_2" => array(
					"name" => "image_link_2",
					"feed_name" => "image_link_2",
					"format" => "optional",
				),
				"image_link_3" => array(
					"name" => "image_link_3",
					"feed_name" => "image_link_3",
					"format" => "optional",
				),
				"image_link_4" => array(
					"name" => "image_link_4",
					"feed_name" => "image_link_4",
					"format" => "optional",
				),
				"image_link_5" => array(
					"name" => "image_link_5",
					"feed_name" => "image_link_5",
					"format" => "optional",
				),
				"image_link_6" => array(
					"name" => "image_link_6",
					"feed_name" => "image_link_6",
					"format" => "optional",
				),
				"image_link_7" => array(
					"name" => "image_link_7",
					"feed_name" => "image_link_7",
					"format" => "optional",
				),
				"image_link_8" => array(
					"name" => "image_link_8",
					"feed_name" => "image_link_8",
					"format" => "optional",
				),
				"image_link_9" => array(
					"name" => "image_link_9",
					"feed_name" => "image_link_9",
					"format" => "optional",
				),
			),
		);
		return $daisyconhuisentuin;
	}
}
?>
