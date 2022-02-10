<?php
/**
 * Settings for Mall availability feeds
 */
class WooSEA_mall_availability {
	public $mall_availability;

        public static function get_channel_attributes() {
                $sitename = get_option('blogname');

        	$mall_availability = array(
			"Feed fields" => array(
				"ID" => array(
					"name" => "ID",
					"feed_name" => "ID",
					"format" => "required",
					"woo_suggest" => "id",
				),
                                "IN_STOCK" => array(
                                        "name" => "IN_STOCK",
                                        "feed_name" => "IN_STOCK",
					"format" => "required",
					"woo_suggest" => "quantity",
				),
                                "ACTIVE" => array(
                                        "name" => "ACTIVE",
                                        "feed_name" => "ACTIVE",
					"format" => "required",
				),
			),
		);
		return $mall_availability;
	}
}
?>
