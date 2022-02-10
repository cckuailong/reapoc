<?php
/**
 * Settings for Spartoo France feeds
 */
class WooSEA_spartoo_fr {
	public $spartoo_fr;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$spartoo_fr = array(
			"Feed fields" => array(
				"Reference partenaire" => array(
					"name" => "reference_partenaire",
					"feed_name" => "reference_partenaire",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Product name" => array(
					"name" => "product_name",
					"feed_name" => "product_name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Manufacturers name" => array(
					"name" => "manufacturers_name",
					"feed_name" => "manufacturers_name",
					"format" => "required",
				),
                                "Product description" => array(
                                        "name" => "product_description",
                                        "feed_name" => "product_description",
                                        "format" => "required",
					"woo_suggest" => "description",
                                ),
                                "Product price" => array(
                                        "name" => "product_price",
                                        "feed_name" => "product_price",
                                        "format" => "required",
					"woo_suggest" => "price",
                                ),
	                      	"Product sex" => array(
                                        "name" => "product_sex",
                                        "feed_name" => "product_sex",
                                        "format" => "required",
                                ),
		               	"Product quantity" => array(
                                        "name" => "product_quantity",
                                        "feed_name" => "product_quantity",
                                        "format" => "required",
					"woo_suggest" => "quantity",
                                ),
			      	"Product style" => array(
                                        "name" => "product_style",
                                        "feed_name" => "product_style",
                                        "format" => "required",
                                ),
			),
		);
		return $spartoo_fr;
	}
}
?>
