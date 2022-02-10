<?php
/**
 * Settings for Miinto France feeds
 */
class WooSEA_miinto_fr {
	public $miinto_fr;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$miinto_fr = array(
			"Feed fields" => array(
				"GTIN" => array(
					"name" => "gtin",
					"feed_name" => "gtin",
					"format" => "required",
				),
				"Item Group ID" => array(
					"name" => "item_group_id",
					"feed_name" => "item_group_id",
					"format" => "required",
					"woo_suggest" => "item_group_id",
				),
				"C:style_id:string" => array(
					"name" => "c:style_id:string",
					"feed_name" => "c:style_id:string",
					"format" => "required",
				),
				"Brand" => array(
					"name" => "brand",
					"feed_name" => "brand",
					"format" => "required",
				),
				"Title" => array(
					"name" => "title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"C:title_PL:string" => array(
					"name" => "c:title_PL:string",
					"feed_name" => "c:title_PL:string",
					"format" => "optional",
				),
				"C:title_DK:string" => array(
					"name" => "c:title_DK:string",
					"feed_name" => "c:title_DK:string",
					"format" => "optional",
				),
				"C:title_NL:string" => array(
					"name" => "c:title_NL:string",
					"feed_name" => "c:title_NL:string",
					"format" => "optional",
				),
				"Product Type" => array(
					"name" => "product_type",
					"feed_name" => "product_type",
					"format" => "required",
				),
				"Gender" => array(
					"name" => "gender",
					"feed_name" => "gender",
					"format" => "required",
				),
                                "Color" => array(
                                        "name" => "color",
                                        "feed_name" => "color",
                                        "format" => "required",
                                ),
				"Size" => array(
					"name" => "size",
					"feed_name" => "size",
					"format" => "required",
				),
        			"Image link" => array(
					"name" => "image_link",
					"feed_name" => "image_link",
					"format" => "required",
					"woo_suggest" => "image",
				),
                                "Additional image link" => array(
                                        "name" => "additional_image_link",
                                        "feed_name" => "additional_image_link",
                                        "format" => "optional",
                                ),
                                "Availability" => array(
                                        "name" => "availability",
                                        "feed_name" => "availability",
                                        "format" => "optional",
					"woo_suggest" => "availability",
				),
                                "C:stock_level:integer" => array(
                                        "name" => "c:stock_level:integer",
                                        "feed_name" => "c:stock_level:integer",
                                        "format" => "required",
                                ),
                                "C:season_tag:string" => array(
                                        "name" => "c:season_tag:string",
                                        "feed_name" => "c:season_tag:string",
                                        "format" => "required",
                                ),
                                "Description" => array(
                                        "name" => "description",
                                        "feed_name" => "description",
                                        "format" => "required",
					"woo_suggest" => "description",
                                ),
                                "C:description_PL:string" => array(
                                        "name" => "c:description_PL:string",
                                        "feed_name" => "c:description_PL:string",
                                        "format" => "optional",
                                ),
                                "C:description_NL:string" => array(
                                        "name" => "c:description_NL:string",
                                        "feed_name" => "c:description_NL:string",
                                        "format" => "optional",
                                ),
                                "C:description_DK:string" => array(
                                        "name" => "c:description_DK:string",
                                        "feed_name" => "c:description_DK:string",
                                        "format" => "optional",
                                ),
                                "Material" => array(
                                        "name" => "material",
                                        "feed_name" => "material",
                                        "format" => "optional",
                                ),
                                "Washing" => array(
                                        "name" => "washing",
                                        "feed_name" => "washing",
                                        "format" => "optional",
                                ),
                                "C:discount_retail_price_PLN:integer" => array(
                                        "name" => "c:discount_retail_price_PLN:integer",
                                        "feed_name" => "c:discount_retail_price_PLN:integer",
                                        "format" => "optional",
                                ),
                           	"C:discount_retail_price_DKK:integer" => array(
                                        "name" => "c:discount_retail_price_DKK:integer",
                                        "feed_name" => "c:discount_retail_price_DKK:integer",
                                        "format" => "optional",
                                ),
                           	"C:discount_retail_price_EUR:integer" => array(
                                        "name" => "c:discount_retail_price_EUR:integer",
                                        "feed_name" => "c:discount_retail_price_EUR:integer",
                                        "format" => "optional",
                                ),
                     		"C:retail_price_PLN:integer" => array(
                                        "name" => "c:retail_price_PLN:integer",
                                        "feed_name" => "c:retail_price_PLN:integer",
                                        "format" => "required",
					"woo_suggest" => "price",
                                ),
                     		"C:retail_price_DKK:integer" => array(
                                        "name" => "c:retail_price_DKK:integer",
                                        "feed_name" => "c:retail_price_DKK:integer",
                                        "format" => "required",
					"woo_suggest" => "price",
                                ),
                     		"C:retail_price_EUR:integer" => array(
                                        "name" => "c:retail_price_EUR:integer",
                                        "feed_name" => "c:retail_price_EUR:integer",
                                        "format" => "required",
					"woo_suggest" => "price",
                                ),
                     		"C:wholsesale_price_PLN:integer" => array(
                                        "name" => "c:wholesale_price_PLN:integer",
                                        "feed_name" => "c:wholesale_price_PLN:integer",
                                        "format" => "optional",
                                ),
                     		"C:wholsesale_price_DKK:integer" => array(
                                        "name" => "c:wholesale_price_DKK:integer",
                                        "feed_name" => "c:wholesale_price_DKK:integer",
                                        "format" => "optional",
                                ),
                     		"C:wholsesale_price_EUR:integer" => array(
                                        "name" => "c:wholesale_price_EUR:integer",
                                        "feed_name" => "c:wholesale_price_EUR:integer",
                                        "format" => "optional",
                                ),
			),
		);
		return $miinto_fr;
	}
}
?>
