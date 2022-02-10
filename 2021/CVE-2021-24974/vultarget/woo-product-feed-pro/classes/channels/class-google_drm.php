<?php
/**
 * Settings for Google DRM product feeds
 */
class WooSEA_google_drm {
	public $google_drm;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$google_drm = array(
			"Remarketing fields" => array(
				"ID" => array(
					"name" => "ID",
					"feed_name" => "ID",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"ID2" => array(
					"name" => "ID2",
					"feed_name" => "ID2",
					"format" => "optional",
				),
				"Item title" => array(
					"name" => "Item title",
					"feed_name" => "Item title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Item subtitle" => array(
					"name" => "Item subtitle",
					"feed_name" => "Item subtitle",
					"format" => "optional",
				),
				"Final URL" => array(
					"name" => "Final URL",
					"feed_name" => "Final URL",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"Image URL" => array(
					"name" => "Image URL",
					"feed_name" => "Image URL",
					"format" => "optional",
					"woo_suggest" => "image_link",
				),
				"Item description" => array(
					"name" => "Item description",
					"feed_name" => "Item description",
					"format" => "optional",
					"woo_suggest" => "description",
				),
				"Item category" => array(
					"name" => "Item category",
					"feed_name" => "Item category",
					"format" => "optional",
					"woo_suggest" => "categories",
				),
				"Price" => array(
					"name" => "Price",
					"feed_name" => "Price",
					"format" => "optional",
					"woo_suggest" => "price",
				),
				"Sale price" => array(
					"name" => "Sale price",
					"feed_name" => "Sale price",
					"format" => "optional",
					"woo_suggest" => "sale_price",
				),
				"Contextual keywords" => array(
					"name" => "Contextual keywords",
					"feed_name" => "Contextual keywords",
					"format" => "optional",
				),
				"Item address" => array(
					"name" => "Item address",
					"feed_name" => "Item address",
					"format" => "optional",
				),
				"Tracking template" => array(
					"name" => "Tracking template",
					"feed_name" => "Tracking template",
					"format" => "optional",
				),
				"Custom parameter" => array(
					"name" => "Custom parameter",
					"feed_name" => "Custom parameter",
					"format" => "optional",
				),
                                "Item group ID" => array(
                                        "name" => "item_group_id",
                                        "feed_name" => "g:item_group_id",
                                        "format" => "optional",
                                ),
			),
		);
		return $google_drm;
	}
}
?>
