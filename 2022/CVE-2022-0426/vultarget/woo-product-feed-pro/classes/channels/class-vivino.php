<?php
/**
 * Settings for Vivino feeds
 */
class WooSEA_vivino {
	public $vivino;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$vivino = array(
			"Feed fields" => array(
				"Product ID" => array(
					"name" => "product-id",
					"feed_name" => "product-id",
					"format" => "required",
					"woo_suggest" => "id"
				),
				"Product Name" => array(
					"name" => "product-name",
					"feed_name" => "product-name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Price" => array(
					"name" => "price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "vivino_price",
				),
				"Bottle Size" => array(
					"name" => "bottle_size",
					"feed_name" => "bottle_size",
					"format" => "required",
				),
				"Bottle Quantity" => array(
					"name" => "bottle_quantity",
					"feed_name" => "bottle_quantity",
					"format" => "required",
				),
				"Inventory Count" => array(
					"name" => "inventory_count",
					"feed_name" => "inventory_count",
					"format" => "required",
					"woo_suggest" => "quantity",
				),
				"Quantity is minimum" => array(
					"name" => "quantity-is-minimum",
					"feed_name" => "quantity-is-minimum",
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
				"Producer" => array(
					"name" => "producer",
					"feed_name" => "producer",
					"format" => "optional",
				),
				"Wine Name" => array(
					"name" => "wine-name",
					"feed_name" => "wine-name",
					"format" => "optional",
				),
				"Appellation" => array(
					"name" => "appellation",
					"feed_name" => "appellation",
					"format" => "optional",
				),
				"Vintage" => array(
					"name" => "vintage",
					"feed_name" => "vintage",
					"format" => "optional",
				),
				"Country" => array(
					"name" => "country",
					"feed_name" => "country",
					"format" => "optional",
				),
				"Color" => array(
					"name" => "color",
					"feed_name" => "color",
					"format" => "optional",
				),
				"EAN" => array(
					"name" => "ean",
					"feed_name" => "ean",
					"format" => "optional",
				),
				"UPC" => array(
					"name" => "upc",
					"feed_name" => "upc",
					"format" => "optional",
				),
				"JAN" => array(
					"name" => "jan",
					"feed_name" => "jan",
					"format" => "optional",
				),
				"Description" => array(
					"name" => "description",
					"feed_name" => "description",
					"format" => "optional",
				),
				"Alcohol" => array(
					"name" => "alcohol",
					"feed_name" => "alcohol",
					"format" => "optional",
				),
				"Producer Address" => array(
					"name" => "producer-address",
					"feed_name" => "producer-address",
					"format" => "optional",
				),
				"Importer Address" => array(
					"name" => "importer-address",
					"feed_name" => "importer-address",
					"format" => "optional",
				),
				"Varietal" => array(
					"name" => "varietal",
					"feed_name" => "varietal",
					"format" => "optional",
				),
				"Ageing" => array(
					"name" => "ageing",
					"feed_name" => "ageing",
					"format" => "optional",
				),
				"Closure" => array(
					"name" => "closure",
					"feed_name" => "closure",
					"format" => "optional",
				),
				"Production Size Unit" => array(
					"name" => "production-size",
					"feed_name" => "production-size",
					"format" => "optional",
				),
				"Residual Sugar Unit" => array(
					"name" => "residual-sugar",
					"feed_name" => "residual-sugar",
					"format" => "optional",
				),
				"Acidity Unit" => array(
					"name" => "acidity",
					"feed_name" => "acidity",
					"format" => "optional",
				),
				"Ph" => array(
					"name" => "ph",
					"feed_name" => "ph",
					"format" => "optional",
				),
				"Winemaker" => array(
					"name" => "winemaker",
					"feed_name" => "winemaker",
					"format" => "optional",
				),
				"Contains Milk Allergens" => array(
					"name" => "contains-milk-allergens",
					"feed_name" => "contains-milk-allergens",
					"format" => "optional",
				),
				"Contains Egg Allergens" => array(
					"name" => "contains-egg-allergens",
					"feed_name" => "contains-egg-allergens",
					"format" => "optional",
				),
				"Non Alcoholic" => array(
					"name" => "non-alcoholic",
					"feed_name" => "non-alcoholic",
					"format" => "optional",
				),
			),
		);
		return $vivino;
	}
}
?>
