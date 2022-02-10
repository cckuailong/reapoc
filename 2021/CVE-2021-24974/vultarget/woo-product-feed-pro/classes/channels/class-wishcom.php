<?php
/**
 * Settings for Wish.com feeds
 */
class WooSEA_wishcom {
	public $wishcom;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$wishcom = array(
			"Feed fields" => array(
				"Parent Unique ID" => array(
					"name" => "Parent Unique ID",
					"feed_name" => "Parent Unique ID",
					"format" => "optional",
				),
				"Unique ID" => array(
					"name" => "Unique ID",
					"feed_name" => "Unique ID",
					"format" => "required",
					"woo_suggest" => "id"
				),
				"Product Name" => array(
					"name" => "Product Name",
					"feed_name" => "Product Name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Declared Name" => array(
					"name" => "Declared Name",
					"feed_name" => "Declared Name",
					"format" => "optional",
				),
				"Declared Local Name" => array(
					"name" => "Declared Local Name",
					"feed_name" => "Declared Local Name",
					"format" => "optional",
				),
				"Pieces" => array(
					"name" => "Pieces",
					"feed_name" => "Pieces",
					"format" => "optional",
				),
				"Color" => array(
					"name" => "Color",
					"feed_name" => "Color",
					"format" => "optional",
				),
				"Size" => array(
					"name" => "Size",
					"feed_name" => "Size",
					"format" => "optional",
				),
				"Quantity" => array(
					"name" => "Quantity",
					"feed_name" => "Quantity",
					"format" => "required",
					"woo_suggest" => "quantity",
				),
				"Tags" => array(
					"name" => "Tags",
					"feed_name" => "Tags",
					"format" => "required",
				),
				"Description" => array(
					"name" => "Description",
					"feed_name" => "Description",
					"format" => "optional",
				),
				"Price" => array(
					"name" => "Price",
					"feed_name" => "Price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Shipping" => array(
					"name" => "Shipping",
					"feed_name" => "Shipping",
					"format" => "required",
				),
				"Shipping Time" => array(
					"name" => "Shipping Time",
					"feed_name" => "Shipping Time",
					"format" => "required",
				),
				"Main Image URL" => array(
					"name" => "Main Image URL",
					"feed_name" => "Main Image URL",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Extra Image URL 1" => array(
					"name" => "Extra Image URL 1",
					"feed_name" => "Extra Image URL 1",
					"format" => "optional",
				),
				"Extra Image URL 2" => array(
					"name" => "Extra Image URL 2",
					"feed_name" => "Extra Image URL 2",
					"format" => "optional",
				),
				"Extra Image URL 3" => array(
					"name" => "Extra Image URL 3",
					"feed_name" => "Extra Image URL 3",
					"format" => "optional",
				),
				"Extra Image URL 4" => array(
					"name" => "Extra Image URL 4",
					"feed_name" => "Extra Image URL 4",
					"format" => "optional",
				),
				"Extra Image URL 5" => array(
					"name" => "Extra Image URL 5",
					"feed_name" => "Extra Image URL 5",
					"format" => "optional",
				),
				"Extra Image URL 6" => array(
					"name" => "Extra Image URL 6",
					"feed_name" => "Extra Image URL 6",
					"format" => "optional",
				),
				"Extra Image URL 7" => array(
					"name" => "Extra Image URL 7",
					"feed_name" => "Extra Image URL 7",
					"format" => "optional",
				),
				"Extra Image URL 8" => array(
					"name" => "Extra Image URL 8",
					"feed_name" => "Extra Image URL 8",
					"format" => "optional",
				),
				"Extra Image URL 9" => array(
					"name" => "Extra Image URL 9",
					"feed_name" => "Extra Image URL 9",
					"format" => "optional",
				),
				"Clean Image URL" => array(
					"name" => "Clean Image URL",
					"feed_name" => "Clean Image URL",
					"format" => "optional",
				),
				"Package Length" => array(
					"name" => "Package Length",
					"feed_name" => "Package Length",
					"format" => "optional",
				),
				"Package Width" => array(
					"name" => "Package Width",
					"feed_name" => "Package Width",
					"format" => "optional",
				),
				"Package Height" => array(
					"name" => "Package Height",
					"feed_name" => "Package Height",
					"format" => "optional",
				),
				"Package Weight" => array(
					"name" => "Package Weight",
					"feed_name" => "Package Weight",
					"format" => "optional",
				),
				"Country Of Origin" => array(
					"name" => "Country Of Origin",
					"feed_name" => "Country Of Origin",
					"format" => "optional",
				),
        			"Contains Powder" => array(
					"name" => "Contains Powder",
					"feed_name" => "Contains Powder",
					"format" => "optional",
				),
                                "Contains Liquid" => array(
                                        "name" => "Contains Liquid",
                                        "feed_name" => "Contains Liquid",
                                        "format" => "optional",
                                ),
                                "Contains Battery" => array(
                                        "name" => "Contains Battery",
                                        "feed_name" => "Contains Battery",
                                        "format" => "optional",
                                ),
                                "Contains Metal" => array(
                                        "name" => "Contains Metal",
                                        "feed_name" => "Contains Metal",
                                        "format" => "optional",
                                ),
                                "Custom Declared Value" => array(
                                        "name" => "Custom Declared Value",
                                        "feed_name" => "Custom Declared Value",
                                        "format" => "optional",
                                ),
                                "Custom HS Code" => array(
                                        "name" => "Custom HS Code",
                                        "feed_name" => "Custom HS Code",
                                        "format" => "optional",
                                ),
			),
		);
		return $wishcom;
	}
}
?>
