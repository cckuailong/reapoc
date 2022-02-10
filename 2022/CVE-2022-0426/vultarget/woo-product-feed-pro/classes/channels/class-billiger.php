<?php
/**
 * Settings for Billiger feeds
 */
class WooSEA_billiger {
	public $billiger;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$billiger = array(
			"Feed fields" => array(
				"AID" => array(
					"name" => "aid / sku",
					"feed_name" => "aid",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Name" => array(
					"name" => "name",
					"feed_name" => "name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Brand" => array(
					"name" => "brand",
					"feed_name" => "brand",
					"format" => "required",
				),
				"Link" => array(
					"name" => "link",
					"feed_name" => "link",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"Image" => array(
					"name" => "image",
					"feed_name" => "image",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Product description" => array(
					"name" => "desc",
					"feed_name" => "desc",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"Shop category" => array(
					"name" => "shop_cat",
					"feed_name" => "shop_cat",
					"format" => "required",
					"woo_suggest" => "categories",
				),
				"Price" => array(
					"name" => "price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Base price" => array(
					"name" => "base_price",
					"feed_name" => "base_price",
					"format" => "optional",
				),
        			"Old price" => array(
					"name" => "old_price",
					"feed_name" => "old_price",
					"format" => "optional",
				),
                                "EAN" => array(
                                        "name" => "ean / gtin",
                                        "feed_name" => "ean",
                                        "format" => "required",
                                ),
                                "MPNR" => array(
                                        "name" => "mpn(r)",
                                        "feed_name" => "mpnr",
                                        "format" => "required",
                                ),
                                "Delivery time" => array(
                                        "name" => "dlv_time",
                                        "feed_name" => "dlv_time",
                                        "format" => "required",
                                ),
                                "Delivery cost" => array(
                                        "name" => "dlv_cost",
                                        "feed_name" => "dlv_cost",
                                        "format" => "required",
                                ),
                                "Delivery cost Austria" => array(
                                        "name" => "dlv_cost_at",
                                        "feed_name" => "dlv_cost_at",
                                        "format" => "optional",
                                ),
                                "Promotional text" => array(
                                        "name" => "promo_text",
                                        "feed_name" => "promo_text",
                                        "format" => "optional",
                                ),
                                "Voucher text" => array(
                                        "name" => "voucher_text",
                                        "feed_name" => "voucher_text",
                                        "format" => "optional",
                                ),
                                "Size" => array(
                                        "name" => "size",
                                        "feed_name" => "size",
                                        "format" => "optional",
                                ),
                                "Color" => array(
                                        "name" => "color",
                                        "feed_name" => "color",
                                        "format" => "optional",
                                ),
                                "Gender" => array(
                                        "name" => "gender",
                                        "feed_name" => "gender",
                                        "format" => "optional",
                                ),
                                "Material" => array(
                                        "name" => "material",
                                        "feed_name" => "material",
                                        "format" => "optional",
                                ),
                                "Class" => array(
                                        "name" => "class",
                                        "feed_name" => "class",
                                        "format" => "optional",
                                ),
                                "Features" => array(
                                        "name" => "features",
                                        "feed_name" => "features",
                                        "format" => "optional",
                                ),
                                "Style" => array(
                                        "name" => "style",
                                        "feed_name" => "style",
                                        "format" => "optional",
                                ),
                                "EEK" => array(
                                        "name" => "eek",
                                        "feed_name" => "eek",
                                        "format" => "optional",
                                ),
                                "Light socket" => array(
                                        "name" => "light_socket",
                                        "feed_name" => "light_socket",
                                        "format" => "optional",
                                ),
                                "Wet adhesion" => array(
                                        "name" => "wet_adhesion",
                                        "feed_name" => "wet_adhesion",
                                        "format" => "optional",
                                ),
                                "Fuel" => array(
                                        "name" => "fuel",
                                        "feed_name" => "fuel",
                                        "format" => "optional",
                                ),
                                "External rolling noise" => array(
                                        "name" => "rollgeraeusch",
                                        "feed_name" => "rollgeraeusch",
                                        "format" => "optional",
                                ),
                                "HSN and TSN" => array(
                                        "name" => "hsn_tsn",
                                        "feed_name" => "hsn_tsn",
                                        "format" => "optional",
                                ),
                                "Slide" => array(
                                        "name" => "diameter",
                                        "feed_name" => "slide",
                                        "format" => "optional",
                                ),
                                "Base Curve" => array(
                                        "name" => "bc",
                                        "feed_name" => "bc",
                                        "format" => "optional",
                                ),
                                "Diopters" => array(
                                        "name" => "sph_pwr",
                                        "feed_name" => "sph_pwr",
                                        "format" => "optional",
                                ),
                                "Cylinder" => array(
                                        "name" => "cyl",
                                        "feed_name" => "cyl",
                                        "format" => "optional",
                                ),
                                "Axis" => array(
                                        "name" => "axis",
                                        "feed_name" => "axis",
                                        "format" => "optional",
                                ),
			),
		);
		return $billiger;
	}
}
?>
