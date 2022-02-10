<?php
/**
 * Settings for Pricecheck South Africa feeds
 */
class WooSEA_pricecheck {
	public $pricecheck;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$pricecheck = array(
			"Feed fields" => array(
				"Category" => array(
					"name" => "Category",
					"feed_name" => "Category",
					"format" => "required",
					"woo_suggest" => "categories",
				),
				"ProductName" => array(
					"name" => "ProductName",
					"feed_name" => "ProductName",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Manufacturer" => array(
					"name" => "Manufacturer",
					"feed_name" => "Manufacturer",
					"format" => "required",
				),
				"ShopSKU" => array(
					"name" => "ShopSKU",
					"feed_name" => "ShopSKU",
					"format" => "required",
					"woo_suggest" => "SKU",
				),
				"ModelNumber" => array(
					"name" => "ModelNumber",
					"feed_name" => "ModelNumber",
					"format" => "optional",
				),
				"EAN" => array(
					"name" => "EAN",
					"feed_name" => "EAN",
					"format" => "optional",
				),
				"SKU" => array(
					"name" => "SKU",
					"feed_name" => "SKU",
					"format" => "optional",
					"woo_suggest" => "SKU",
				),
				"UPC" => array(
					"name" => "UPC",
					"feed_name" => "UPC",
					"format" => "optional",
				),
                                "Description" => array(
                                        "name" => "Description",
                                        "feed_name" => "Description",
                                        "format" => "required",
                                        "woo_suggest" => "description",
                                ),
				"Price" => array(
					"name" => "Price",
					"feed_name" => "Price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"SalePrice" => array(
					"name" => "SalePrice",
					"feed_name" => "SalePrice",
					"format" => "optional",
				),
				"DeliveryCost" => array(
					"name" => "DeliveryCost",
					"feed_name" => "DeliveryCost",
					"format" => "optional",
				),
				"ProductURL" => array(
					"name" => "ProductURL",
					"feed_name" => "ProductURL",
					"format" => "required",
					"woo_suggest" => "link",
				),
        			"ImageURL" => array(
					"name" => "ImageURL",
					"feed_name" => "ImageURL",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Notes" => array(
					"name" => "Notes",
					"feed_name" => "Notes",
					"format" => "optional",
				),
				"StockAvailability" => array(
					"name" => "StockAvailability",
					"feed_name" => "StockAvailability",
					"format" => "optional",
				),
				"StockLevel" => array(
					"name" => "StockLevel",
					"feed_name" => "StockLevel",
					"format" => "optional",
				),
				"Is MP" => array(
					"name" => "is_mp",
					"feed_name" => "is_mp",
					"format" => "optional",
				),
				"IsBundle" => array(
					"name" => "IsBundle",
					"feed_name" => "IsBundle",
					"format" => "optional",
				),
				"GroupID" => array(
					"name" => "GroupID",
					"feed_name" => "GroupID",
					"format" => "optional",
				),
				"NoOfUnits" => array(
					"name" => "NoOfUnits",
					"feed_name" => "NoOfUnits",
					"format" => "optional",
				),
				"Format" => array(
					"name" => "Format",
					"feed_name" => "Format",
					"format" => "optional",
				),
				"Actor" => array(
					"name" => "Actor",
					"feed_name" => "Actor",
					"format" => "optional",
				),
				"Director" => array(
					"name" => "Director",
					"feed_name" => "Director",
					"format" => "optional",
				),
				"ReleaseDate" => array(
					"name" => "ReleaseDate",
					"feed_name" => "ReleaseDate",
					"format" => "optional",
				),
				"RunningTime" => array(
					"name" => "RunningTime",
					"feed_name" => "RunningTime",
					"format" => "optional",
				),
				"AgeGroup" => array(
					"name" => "AgeGroup",
					"feed_name" => "AgeGroup",
					"format" => "optional",
				),
				"Colour" => array(
					"name" => "Colour",
					"feed_name" => "Colour",
					"format" => "optional",
				),
				"Gender" => array(
					"name" => "Gender",
					"feed_name" => "Gender",
					"format" => "optional",
				),
				"Size" => array(
					"name" => "Size",
					"feed_name" => "Size",
					"format" => "optional",
				),
				"Material" => array(
					"name" => "Material",
					"feed_name" => "Material",
					"format" => "optional",
				),
				"Pattern" => array(
					"name" => "Pattern",
					"feed_name" => "Pattern",
					"format" => "optional",
				),
				"SizeType" => array(
					"name" => "SizeType",
					"feed_name" => "SizeType",
					"format" => "optional",
				),
				"Style" => array(
					"name" => "Style",
					"feed_name" => "Style",
					"format" => "optional",
				),
				"Region" => array(
					"name" => "Region",
					"feed_name" => "Region",
					"format" => "optional",
				),
				"Varietal" => array(
					"name" => "Varietal",
					"feed_name" => "Varietal",
					"format" => "optional",
				),
				"Vintage" => array(
					"name" => "Vintage",
					"feed_name" => "Vintage",
					"format" => "optional",
				),
				"Volume" => array(
					"name" => "Volume",
					"feed_name" => "Volume",
					"format" => "optional",
				),
				"Winery" => array(
					"name" => "Winery",
					"feed_name" => "Winery",
					"format" => "optional",
				),
				"Artist" => array(
					"name" => "Artist",
					"feed_name" => "Artist",
					"format" => "optional",
				),
				"Label" => array(
					"name" => "Label",
					"feed_name" => "Label",
					"format" => "optional",
				),
				"ReleaseDate" => array(
					"name" => "ReleaseDate",
					"feed_name" => "ReleaseDate",
					"format" => "optional",
				),
				"Make" => array(
					"name" => "Make",
					"feed_name" => "Make",
					"format" => "optional",
				),
				"Model" => array(
					"name" => "Model",
					"feed_name" => "Model",
					"format" => "optional",
				),
				"Year" => array(
					"name" => "Year",
					"feed_name" => "Year",
					"format" => "optional",
				),
				"Mileage" => array(
					"name" => "Mileage",
					"feed_name" => "Mileage",
					"format" => "optional",
				),
				"Transmission" => array(
					"name" => "Transmission",
					"feed_name" => "Transmission",
					"format" => "optional",
				),
				"Colour" => array(
					"name" => "Colour",
					"feed_name" => "Colour",
					"format" => "optional",
				),
			),
		);
		return $pricecheck;
	}
}
?>
