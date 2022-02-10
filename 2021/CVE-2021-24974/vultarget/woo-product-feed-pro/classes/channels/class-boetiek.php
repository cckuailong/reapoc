<?php
/**
 * Settings for Boetiek.nl feeds
 */
class WooSEA_boetiek {
	public $boetiek;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$boetiek = array(
			"Feed fields" => array(
                                "Product ID" => array(
                                        "name" => "ProductID",
                                        "feed_name" => "ProductID",
                                        "format" => "required",
                                        "woo_suggest" => "id",
                                ),
				"Product Name" => array(
					"name" => "ProductName",
					"feed_name" => "ProductName",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Product URL" => array(
					"name" => "ProductURL",
					"feed_name" => "ProductURL",
					"format" => "required",
					"woo_suggest" => "link",
				),
        			"Product Image" => array(
					"name" => "ProductImage",
					"feed_name" => "ProductImage",
					"format" => "required",
					"woo_suggest" => "image",
				),
                                 "Product Description" => array(
                                        "name" => "ProductDescription",
                                        "feed_name" => "ProductDescription",
                                        "format" => "required",
					"woo_suggest" => "description",
                                ),
				"Category Path" => array(
					"name" => "CategoryPath",
					"feed_name" => "CategoryPath",
					"format" => "required",
					"woo_suggest" => "category_path",
				),
				"Brand Name" => array(
					"name" => "BrandName",
					"feed_name" => "BrandName",
					"format" => "required",
				),
				"Product Price" => array(
					"name" => "ProductPrice",
					"feed_name" => "ProductPrice",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Product Sale Price" => array(
					"name" => "ProductSalePrice",
					"feed_name" => "ProductSalePrice",
					"format" => "required",
					"woo_suggest" => "sale_price",
				),
				"In Stock" => array(
					"name" => "InStock",
					"feed_name" => "InStock",
					"format" => "required",
					"woo_suggest" => "availability",
				),
        			"Colour" => array(
					"name" => "Colour",
					"feed_name" => "Colour",
					"format" => "optional",
				),
                                "Material" => array(
                                        "name" => "Material",
                                        "feed_name" => "Material",
                                        "format" => "optional",
                                ),
                                "Gender" => array(
                                        "name" => "Gender",
                                        "feed_name" => "Gender",
                                        "format" => "optional",
                                ),
                                "Sustainable" => array(
                                        "name" => "Sustainable",
                                        "feed_name" => "Sustainable",
                                        "format" => "optional",
                                ),
                                "Sizes" => array(
                                        "name" => "Sizes",
                                        "feed_name" => "Sizes",
                                        "format" => "optional",
                                ),
                                "Parent/Group ID" => array(
                                        "name" => "ParentGroupID",
                                        "feed_name" => "ParentGroupID",
                                        "format" => "optional",
                                ),
                                "Stock Quantity" => array(
                                        "name" => "StockQuantity",
                                        "feed_name" => "StockQuantity",
                                        "format" => "optional",
                                ),
                                "Alternative Images" => array(
                                        "name" => "AlternativeImages",
                                        "feed_name" => "AlternativeImages",
                                        "format" => "optional",
                                ),
                                "Long Description" => array(
                                        "name" => "LongDescription",
                                        "feed_name" => "LongDescription",
                                        "format" => "optional",
                                ),
                                "Delivery Time" => array(
                                        "name" => "DeliveryTime",
                                        "feed_name" => "DeliveryTime",
                                        "format" => "optional",
				),
                                "Delivery Cost" => array(
                                        "name" => "DeliveryCost",
                                        "feed_name" => "DeliveryCost",
                                        "format" => "optional",
                                ),
			),
		);
		return $boetiek;
	}
}
?>
