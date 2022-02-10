<?php
/**
 * Settings for Zbozi feeds
 */
class WooSEA_zbozi {
	public $zbozi;

        public static function get_channel_attributes() {
                $sitename = get_option('blogname');

        	$zbozi = array(
			"Feed fields" => array(
				"ITEM_ID" => array(
					"name" => "ITEM_ID",
					"feed_name" => "ITEM_ID",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"PRODUCTNAME" => array(
					"name" => "PRODUCTNAME",
					"feed_name" => "PRODUCTNAME",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"PRODUCT" => array(
					"name" => "PRODUCT",
					"feed_name" => "PRODUCT",
					"format" => "optional",
				),
				"DESCRIPTION" => array(
					"name" => "DESCRIPTION",
					"feed_name" => "DESCRIPTION",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"CATEGORYTEXT" => array(
					"name" => "CATEGORYTEXT",
					"feed_name" => "CATEGORYTEXT",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"EAN" => array(
					"name" => "EAN",
					"feed_name" => "EAN",
					"format" => "optional",
				),
				"ISBN" => array(
					"name" => "ISBN",
					"feed_name" => "ISBN",
					"format" => "optional",
				),
				"PRODUCTNO" => array(
					"name" => "PRODUCTNO",
					"feed_name" => "PRODUCTNO",
					"format" => "optional",
				),
				"MANUFACTURER" => array(
					"name" => "MANUFACTURER",
					"feed_name" => "MANUFACTURER",
					"format" => "optional",
				),
				"BRAND" => array(
					"name" => "BRAND",
					"feed_name" => "BRAND",
					"format" => "optional",
				),
				"URL" => array(
					"name" => "URL",
					"feed_name" => "URL",
					"format" => "required",
					"woo_suggest" => "link",
				),
                                "PRICE_VAT" => array(
                                        "name" => "PRICE_VAT",
                                        "feed_name" => "PRICE_VAT",
                                        "format" => "required",
                                        "woo_suggest" => "price",
                                ),
                                "DELIVERY_DATE" => array(
                                        "name" => "DELIVERY_DATE",
                                        "feed_name" => "DELIVERY_DATE",
                                        "format" => "required",
                                ),
                                "DELIVERY" => array(
                                        "name" => "DELIVERY",
                                        "feed_name" => "DELIVERY",
                                        "format" => "required",
					"woo_suggest" => "shipping",
                                ),
                                "SHOP_DEPOTS" => array(
                                        "name" => "SHOP_DEPOTS",
                                        "feed_name" => "SHOP_DEPOTS",
                                        "format" => "optional",
                                ),
                                "CATEGORYTEXT" => array(
                                        "name" => "CATEGORYTEXT",
                                        "feed_name" => "CATEGORYTEXT",
                                        "format" => "optional",
                                        "woo_suggest" => "categories",
                                ),
				"IMGURL" => array(
					"name" => "IMGURL",
					"feed_name" => "IMGURL",
					"format" => "optional",
					"woo_suggest" => "image",
				),
                                "EXTRA_MESSAGE" => array(
                                        "name" => "EXTRA_MESSAGE",
                                        "feed_name" => "EXTRA_MESSAGE",
                                        "format" => "optional",
                                ),
                                "FREE_GIFT_TEXT" => array(
                                        "name" => "FREE_GIFT_TEXT",
                                        "feed_name" => "FREE_GIFT_TEXT",
                                        "format" => "optional",
                                ),
                                "MAX_CPC" => array(
                                        "name" => "MAX_CPC",
                                        "feed_name" => "MAX_CPC",
                                        "format" => "optional",
                                ),
                                "MAX_CPC_SEARCH" => array(
                                        "name" => "MAX_CPC_SEARCH",
                                        "feed_name" => "MAX_CPC_SEARCH",
                                        "format" => "optional",
                                ),
                                "EROTIC" => array(
                                        "name" => "EROTIC",
                                        "feed_name" => "EROTIC",
                                        "format" => "optional",
                                ),
                                "ITEMGROUP_ID" => array(
                                        "name" => "ITEMGROUP_ID",
                                        "feed_name" => "ITEMGROUP_ID",
                                        "format" => "optional",
					"woo_suggest" => "item_group_id",
                                ),
                                "VISIBILITY" => array(
                                        "name" => "VISIBILITY",
                                        "feed_name" => "VISIBILITY",
                                        "format" => "optional",
                                ),
                                "CUSTOM_LABEL_0" => array(
                                        "name" => "CUSTOM_LABEL_0",
                                        "feed_name" => "CUSTOM_LABEL_0",
                                        "format" => "optional",
                                ),
                                "CUSTOM_LABEL_1" => array(
                                        "name" => "CUSTOM_LABEL_1",
                                        "feed_name" => "CUSTOM_LABEL_1",
                                        "format" => "optional",
                                ),
                                "CUSTOM_LABEL_2" => array(
                                        "name" => "CUSTOM_LABEL_2",
                                        "feed_name" => "CUSTOM_LABEL_2",
                                        "format" => "optional",
                                ),
                                "CUSTOM_LABEL_3" => array(
                                        "name" => "CUSTOM_LABEL_3",
                                        "feed_name" => "CUSTOM_LABEL_3",
                                        "format" => "optional",
                                ),
                                "CUSTOM_LABEL_4" => array(
                                        "name" => "CUSTOM_LABEL_4",
                                        "feed_name" => "CUSTOM_LABEL_4",
                                        "format" => "optional",
                                ),
                                "PRODUCT_LINE" => array(
                                        "name" => "PRODUCT_LINE",
                                        "feed_name" => "PRODUCT_LINE",
                                        "format" => "optional",
                                ),
                                "LIST_PRICE" => array(
                                        "name" => "LIST_PRICE",
                                        "feed_name" => "LIST_PRICE",
                                        "format" => "optional",
                                ),
                                "RELEASE_DATE" => array(
                                        "name" => "RELEASE_DATE",
                                        "feed_name" => "RELEASE_DATE",
                                        "format" => "optional",
                                ),
                                "LENGTH" => array(
                                        "name" => "LENGTH",
                                        "feed_name" => "LENGTH",
                                        "format" => "optional",
                                ),
                                "VOLUME" => array(
                                        "name" => "VOLUME",
                                        "feed_name" => "VOLUME",
                                        "format" => "optional",
                                ),
                                "SIZE" => array(
                                        "name" => "SIZE",
                                        "feed_name" => "SIZE",
                                        "format" => "optional",
                                ),
                                "COLOR" => array(
                                        "name" => "COLOR",
                                        "feed_name" => "COLOR",
                                        "format" => "optional",
                                ),
                                "PURPOSE" => array(
                                        "name" => "PURPOSE",
                                        "feed_name" => "PURPOSE",
                                        "format" => "optional",
                                ),
			),
		);
		return $zbozi;
	}
}
?>
