<?php
/**
 * Settings for Compari Romania feeds
 */
class WooSEA_compari_ro {
	public $compari_ro;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$compari_ro = array(
			"Feed fields" => array(
                                "productid" => array(
                                        "name" => "productid",
                                        "feed_name" => "productid",
                                        "format" => "required",
                                        "woo_suggest" => "id",
                                ),
				"manufacturer" => array(
					"name" => "manufacturer",
					"feed_name" => "manufacturer",
					"format" => "required",
				),
				"name" => array(
					"name" => "name",
					"feed_name" => "name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"category" => array(
					"name" => "category",
					"feed_name" => "category",
					"format" => "required",
					"woo_suggest" => "categories",
				),
				"product_url" => array(
					"name" => "product_url",
					"feed_name" => "product_url",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"price" => array(
					"name" => "price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"identifier" => array(
					"name" => "identifier",
					"feed_name" => "identifier",
					"format" => "required",
				),
        			"image_url" => array(
					"name" => "image_url",
					"feed_name" => "image_url",
					"format" => "optional",
					"woo_suggest" => "image",
				),
        			"image_url_2" => array(
					"name" => "image_url_2",
					"feed_name" => "image_url_2",
					"format" => "optional",
				),
        			"image_url_3" => array(
					"name" => "image_url_3",
					"feed_name" => "image_url_3",
					"format" => "optional",
				),
                                "description" => array(
                                        "name" => "description",
                                        "feed_name" => "description",
                                        "format" => "optional",
                                        "woo_suggest" => "description",
                                ),
                                "delivery_time" => array(
                                        "name" => "delivery_time",
                                        "feed_name" => "delivery_time",
                                        "format" => "optional",
                                ),
                                "delivery_cost" => array(
                                        "name" => "delivery_cost",
                                        "feed_name" => "delivery_cost",
                                        "format" => "optional",
                                ),
                                "EAN_code" => array(
                                        "name" => "EAN_code",
                                        "feed_name" => "EAN_code",
                                        "format" => "optional",
				),
                                "net_price" => array(
                                        "name" => "net_price",
                                        "feed_name" => "net_price",
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
                                "GroupId" => array(
                                        "name" => "GroupId",
                                        "feed_name" => "GroupId",
                                        "format" => "optional",
					"woo_suggest" => "item_group_id",
                                ),
			),
		);
		return $compari_ro;
	}
}
?>
