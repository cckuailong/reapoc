<?php
/**
 * Settings for Mall feeds
 */
class WooSEA_mall {
	public $mall;

        public static function get_channel_attributes() {
                $sitename = get_option('blogname');

        	$mall = array(
			"Feed fields" => array(
				"ID" => array(
					"name" => "ID",
					"feed_name" => "ID",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"STAGE" => array(
					"name" => "STAGE",
					"feed_name" => "STAGE",
					"format" => "required",
				),
				"ITEMGROUP_ID" => array(
					"name" => "ITEMGROUP_ID",
					"feed_name" => "ITEMGROUP_ID",
					"format" => "required",
					"woo_suggest" => "item_group_id",
				),
				"ITEMGROUP_TITLE" => array(
					"name" => "ITEMGROUP_TITLE",
					"feed_name" => "ITEMGROUP_TITLE",
					"format" => "required",
					"woo_suggest" => "mother_title",
				),
				"CATEGORY_ID" => array(
					"name" => "CATEGORY_ID",
					"feed_name" => "CATEGORY_ID",
					"format" => "required",
				),
                                "BRAND_ID" => array(
                                        "name" => "BRAND_ID",
                                        "feed_name" => "BRAND_ID",
                                        "format" => "required",
                                ),
                                "TITLE" => array(
                                        "name" => "TITLE",
                                        "feed_name" => "TITLE",
					"format" => "required",
					"woo_suggest" => "title",
                                ),
                                "SHORTDESC" => array(
                                        "name" => "SHORTDESC",
                                        "feed_name" => "SHORTDESC",
                                        "format" => "required",
                                        "woo_suggest" => "short_description",
                                ),
                                "LONGDESC" => array(
                                        "name" => "LONGDESC",
                                        "feed_name" => "LONGDESC",
                                        "format" => "optional",
                                        "woo_suggest" => "description",
                                ),
				"PRIORITY" => array(
					"name" => "PRIORITY",
					"feed_name" => "PRIORITY",
					"format" => "required",
				),
                                "PACKAGE_SIZE" => array(
                                        "name" => "PACKAGE_SIZE",
                                        "feed_name" => "PACKAGE_SIZE",
                                        "format" => "optional",
                                ),
                                "BARCODE" => array(
                                        "name" => "BARCODE",
                                        "feed_name" => "BARCODE",
                                        "format" => "required",
                               ),
                               "PRICE" => array(
                                        "name" => "PRICE",
                                        "feed_name" => "PRICE",
					"format" => "required",
					"woo_suggest" => "price",
                                ),
                                "VAT" => array(
                                        "name" => "VAT",
                                        "feed_name" => "VAT",
                                        "format" => "optional",
                                ),
                                "RRP" => array(
                                        "name" => "RRP",
                                        "feed_name" => "RRP",
                                        "format" => "optional",
                                ),
                                "PARAM" => array(
                                        "name" => "PARAM",
                                        "feed_name" => "PARAM",
                                        "format" => "optional",
                                ),
                                "MEDIA" => array(
                                        "name" => "MEDIA",
                                        "feed_name" => "MEDIA",
                                        "format" => "optional",
                                ),
                                "DELIVERY_DELAY" => array(
                                        "name" => "DELIVERY_DELAY",
                                        "feed_name" => "DELIVERY_DELAY",
                                        "format" => "optional",
                                ),
                                "FREE_DELIVERY" => array(
                                        "name" => "FREE_DELIVERY",
                                        "feed_name" => "FREE_DELIVERY",
                                        "format" => "optional",
                                ),
			),
		);
		return $mall;
	}
}
?>
