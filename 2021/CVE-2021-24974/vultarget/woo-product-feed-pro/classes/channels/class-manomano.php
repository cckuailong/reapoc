<?php
/**
 * Settings for ManoMano.co.uk feeds
 */
class WooSEA_manomano {
	public $manomano;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$manomano = array(
			"Feed fields" => array(
				"SKU" => array(
					"name" => "sku",
					"feed_name" => "sku",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"SKU Manufacturer" => array(
					"name" => "sku manufacturer",
					"feed_name" => "sku_manufacturer",
					"format" => "required",
				),
				"EAN" => array(
					"name" => "ean",
					"feed_name" => "ean",
					"format" => "required",
				),
				"title" => array(
					"name" => "title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "mother_title",
				),
				"description" => array(
					"name" => "description",
					"feed_name" => "description",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"Product price vat inc" => array(
					"name" => "product price vat inc",
					"feed_name" => "product_price_vat_inc",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Shipping price vat inc" => array(
					"name" => "shipping price vat inc",
					"feed_name" => "shipping_price_vat_inc",
					"format" => "required",
				),
				"Quantity" => array(
					"name" => "quantity",
					"feed_name" => "quantity",
					"format" => "required",
				),
				"Brand" => array(
					"name" => "brand",
					"feed_name" => "brand",
					"format" => "required",
				),
				"Merchant category" => array(
					"name" => "merchant category",
					"feed_name" => "merchant_category",
					"format" => "required",
				),
				"Product URL" => array(
					"name" => "product url",
					"feed_name" => "product_url",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"Image 1" => array(
					"name" => "image 1",
					"feed_name" => "image_1",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Image 2" => array(
					"name" => "image 2",
					"feed_name" => "image_2",
					"format" => "optional",
				),
				"Image 3" => array(
					"name" => "image 3",
					"feed_name" => "image_3",
					"format" => "optional",
				),
				"Image 4" => array(
					"name" => "image 4",
					"feed_name" => "image_4",
					"format" => "optional",
				),
				"Image 5" => array(
					"name" => "image 5",
					"feed_name" => "image_5",
					"format" => "optional",
				),
				"Retail price vat inc" => array(
					"name" => "retail price vat inc",
					"feed_name" => "retail_price_vat_inc",
					"format" => "optional",
				),
				"Product vat rate" => array(
					"name" => "product vat rate",
					"feed_name" => "product_vat_rate",
					"format" => "optional",
				),
				"Shipping vat rate" => array(
					"name" => "shipping vat rate",
					"feed_name" => "shipping_vat_rate",
					"format" => "optional",
				),
				"Manufacturer PDF" => array(
					"name" => "manufacturer pdf",
					"feed_name" => "manufacturer_pdf",
					"format" => "optional",
				),
        			"ParentSKU" => array(
					"name" => "parentSKU",
					"feed_name" => "ParentSKU",
					"format" => "optional",
				),
                                "Cross Sell SKU" => array(
                                        "name" => "Cross Sell SKU",
                                        "feed_name" => "Cross_Sell_SKU",
                                        "format" => "optional",
                                ),
                                "ManufacturerWarrantyTime" => array(
                                        "name" => "ManufacturerWarrantyTime",
                                        "feed_name" => "ManufacturerWarrantyTime",
                                        "format" => "optional",
                                ),
                                "Carrier" => array(
                                        "name" => "Carrier",
                                        "feed_name" => "carrier",
                                        "format" => "required",
                                ),
                                "Shipping Time" => array(
                                        "name" => "Shipping Time",
                                        "feed_name" => "shipping_time",
                                        "format" => "required",
                                ),
                                "Use Grid" => array(
                                        "name" => "Use Grid",
                                        "feed_name" => "use_grid",
                                        "format" => "required",
                                ),
                                "Carrier Grid 1" => array(
                                        "name" => "Carrier Grid 1",
                                        "feed_name" => "carrier_grid_1",
                                        "format" => "required",
                                ),
                                "Shipping time carrier grid 1" => array(
                                        "name" => "Shipping time carrier grid 1",
                                        "feed_name" => "shipping_time_carrier_grid_1",
                                        "format" => "required",
                                ),
                                "DisplayWeight" => array(
                                        "name" => "DisplayWeight",
                                        "feed_name" => "DisplayWeight",
                                        "format" => "required",
                                ),
                                "Carrier Grid 2" => array(
                                        "name" => "Carrier Grid 2",
                                        "feed_name" => "carrier_grid_2",
                                        "format" => "optional",
                                ),
                                "Shipping time carrier grid 2" => array(
                                        "name" => "Shipping time carrier grid 2",
                                        "feed_name" => "shipping_time_carrier_grid_2",
                                        "format" => "optional",
                                ),
                                "Carrier Grid 3" => array(
                                        "name" => "Carrier Grid 3",
                                        "feed_name" => "carrier_grid_3",
                                        "format" => "optional",
                                ),
                                "Shipping time carrier grid 3" => array(
                                        "name" => "Shipping time carrier grid 3",
                                        "feed_name" => "shipping_time_carrier_grid_3",
                                        "format" => "optional",
                                ),
                                "Carrier Grid 4" => array(
                                        "name" => "Carrier Grid 4",
                                        "feed_name" => "carrier_grid_4",
                                        "format" => "optional",
                                ),
                                "Shipping time carrier grid 4" => array(
                                        "name" => "Shipping time carrier grid 4",
                                        "feed_name" => "shipping_time_carrier_grid_4",
                                        "format" => "optional",
                                ),
                                "Free Return" => array(
                                        "name" => "Free Return",
                                        "feed_name" => "free_return",
                                        "format" => "optional",
                                ),
                                "Min quantity" => array(
                                        "name" => "Min quantity",
                                        "feed_name" => "min_quantity",
                                        "format" => "optional",
                                ),
                                "Increment" => array(
                                        "name" => "Increment",
                                        "feed_name" => "increment",
                                        "format" => "optional",
                                ),
                                "Sales" => array(
                                        "name" => "Sales",
                                        "feed_name" => "sales",
                                        "format" => "optional",
                                ),
                                "Eco participation" => array(
                                        "name" => "Eco participation",
                                        "feed_name" => "eco_participation",
                                        "format" => "optional",
                                ),
                                "Price per m2 vat inc" => array(
                                        "name" => "Price per m2 vat inc",
                                        "feed_name" => "Price_per_m2_vat_inc",
                                        "format" => "optional",
                                ),
                                "Shipping price supplement vat inc" => array(
                                        "name" => "Shipping price supplement vat inc",
                                        "feed_name" => "shipping_price_supplement_vat_inc",
                                        "format" => "optional",
                                ),
                                "Feature1" => array(
                                        "name" => "Feature1",
                                        "feed_name" => "feature1",
                                        "format" => "optional",
                                ),
                                "Color" => array(
                                        "name" => "Color",
                                        "feed_name" => "Color",
                                        "format" => "optional",
                                ),
                                "Special price type" => array(
                                        "name" => "Special price type",
                                        "feed_name" => "special_price_type",
                                        "format" => "optional",
                                ),
                                "Sample SKU" => array(
                                        "name" => "Sample SKU",
                                        "feed_name" => "Sample_SKU",
                                        "format" => "optional",
                                ),
                                "Style" => array(
                                        "name" => "Style",
                                        "feed_name" => "Style",
                                        "format" => "optional",
                                ),
                                "Unit count" => array(
                                        "name" => "Unit count",
                                        "feed_name" => "unit_count",
                                        "format" => "optional",
                                ),
               			"Unit count type" => array(
                                        "name" => "Unit count type",
                                        "feed_name" => "unit_count_type",
                                        "format" => "optional",
                                ),
			),
		);
		return $manomano;
	}
}
?>
