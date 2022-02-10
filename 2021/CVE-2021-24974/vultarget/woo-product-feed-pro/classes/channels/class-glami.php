<?php
/**
 * Settings for Glami feeds
 */
class WooSEA_glami {
	public $glami;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$glami = array(
			"Feed fields" => array(
				"ITEM_ID" => array(
					"name" => "ITEM_ID",
					"feed_name" => "ITEM_ID",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"ITEMGROUP_ID" => array(
					"name" => "ITEMGROUP_ID",
					"feed_name" => "ITEMGROUP_ID",
					"format" => "optional",
					"woo_suggest" => "item_group_id",
				),
				"PRODUCTNAME" => array(
					"name" => "PRODUCTNAME",
					"feed_name" => "PRODUCTNAME",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"DESCRIPTION" => array(
					"name" => "DESCRIPTION",
					"feed_name" => "DESCRIPTION",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"URL" => array(
					"name" => "URL",
					"feed_name" => "URL",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"URL_SIZE" => array(
					"name" => "URL_SIZE",
					"feed_name" => "URL_SIZE",
					"format" => "optional",
				),
				"IMGURL" => array(
					"name" => "IMGURL",
					"feed_name" => "IMGURL",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"IMGURL_ALTERNATIVE" => array(
					"name" => "IMGURL_ALTERNATIVE",
					"feed_name" => "IMGURL_ALTERNATIVE",
					"format" => "optional",
				),
				"PRICE_VAT" => array(
					"name" => "PRICE_VAT",
					"feed_name" => "PRICE_VAT",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"MANUFACTURER" => array(
					"name" => "MANUFACTURER",
					"feed_name" => "MANUFACTURER",
					"format" => "required",
				),
				"CATEGORYTEXT" => array(
					"name" => "CATEGORYTEXT",
					"feed_name" => "CATEGORYTEXT",
					"format" => "required",
					"woo_suggest" => "categories",
				),
				"CATEGORY_ID" => array(
					"name" => "CATEGORY_ID",
					"feed_name" => "CATEGORY_ID",
					"format" => "optional",
				),
				"GLAMI_CPC" => array(
					"name" => "GLAMI_CPC",
					"feed_name" => "GLAMI_CPC",
					"format" => "optional",
				),
        			"PROMOTION_ID" => array(
					"name" => "PROMOTION_ID",
					"feed_name" => "PROMOTION_ID",
					"format" => "optional",
				),
                                "DELIVERY_DATE" => array(
                                        "name" => "DELIVERY_DATE",
                                        "feed_name" => "DELIVERY_DATE",
                                        "format" => "required",
                                ),
                                "DELIVERY" => array(
                                        "name" => "DELIVERY",
                                        "feed_name" => "DELIVERY",
                                        "format" => "optional",
                                ),
			),
		);
		return $glami;
	}
}
?>
