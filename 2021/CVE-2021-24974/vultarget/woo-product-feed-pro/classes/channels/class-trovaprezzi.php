<?php
/**
 * Settings for Trovaprezzi feeds
 */
class WooSEA_trovaprezzi {
	public $trovaprezzi;

        public static function get_channel_attributes() {
                $sitename = get_option('blogname');

        	$trovaprezzi = array(
			"Feed fields" => array(
				"Product ID" => array(
					"name" => "Code",
					"feed_name" => "Code",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Product SKU" => array(
					"name" => "SKU",
					"feed_name" => "SKU",
					"format" => "optional",
				),
				"Product name" => array(
					"name" => "Name",
					"feed_name" => "Name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Brand" => array(
					"name" => "Brand",
					"feed_name" => "Brand",
					"format" => "optional",
				),
				"Product URL" => array(
					"name" => "Link",
					"feed_name" => "Link",
					"format" => "required",
					"woo_suggest" => "link",
				),
                                "Product price" => array(
                                        "name" => "Price",
                                        "feed_name" => "Price",
                                        "format" => "required",
                                        "woo_suggest" => "price",
                                ),
                                "Original price" => array(
                                        "name" => "OriginalPrice",
                                        "feed_name" => "OriginalPrice",
                                        "format" => "optional",
                                        "woo_suggest" => "price",
                                ),
                                "Product category" => array(
                                        "name" => "Categories",
                                        "feed_name" => "Categories",
                                        "format" => "required",
                                        "woo_suggest" => "categories",
                                ),
				"Product description" => array(
					"name" => "Product description",
					"feed_name" => "Description",
					"format" => "optional",
					"woo_suggest" => "description",
				),
                                "Product image 1" => array(
                                        "name" => "Image1",
                                        "feed_name" => "Image",
                                        "format" => "required",
					"woo_suggest" => "image"
                                ),
                                "Product image 2" => array(
                                        "name" => "Image2",
                                        "feed_name" => "Image2",
                                        "format" => "optional",
                               ),
                                "Product image 3" => array(
                                        "name" => "Image3",
                                        "feed_name" => "Image3",
                                        "format" => "optional",
                               ),
                                "Product image 4" => array(
                                        "name" => "Image4",
                                        "feed_name" => "Image4",
                                        "format" => "optional",
                               ),
                                "Product image 5" => array(
                                        "name" => "Image5",
                                        "feed_name" => "Image5",
                                        "format" => "optional",
                               ),
                                "Stock" => array(
                                        "name" => "Stock",
                                        "feed_name" => "Stock",
                                        "format" => "optional",
                               ),
                               "EAN" => array(
                                        "name" => "EanCode",
                                        "feed_name" => "EanCode",
                                        "format" => "optional",
                                ),
                                "MPN" => array(
                                        "name" => "MpnCode",
                                        "feed_name" => "MpnCode",
                                        "format" => "optional",
                                ),
                                "PartNumber" => array(
                                        "name" => "PartNumber",
                                        "feed_name" => "PartNumber",
                                        "format" => "optional",
                                ),
                                "Descrizione" => array(
                                        "name" => "Descrizione",
                                        "feed_name" => "Taglia",
                                        "format" => "optional",
                                ),
                                "Taglia" => array(
                                        "name" => "Taglia",
                                        "feed_name" => "Taglia",
                                        "format" => "optional",
                                ),
                                "Colore" => array(
                                        "name" => "Colore",
                                        "feed_name" => "Colore",
                                        "format" => "optional",
                                ),
                              	"Materiale" => array(
                                        "name" => "Materiale",
                                        "feed_name" => "Materiale",
                                        "format" => "optional",
                                ),
                                "Shipping Cost" => array(
                                        "name" => "ShippingCost",
                                        "feed_name" => "ShippingCost",
                                        "format" => "required",
                                ),
			),
		);
		return $trovaprezzi;
	}
}
?>
