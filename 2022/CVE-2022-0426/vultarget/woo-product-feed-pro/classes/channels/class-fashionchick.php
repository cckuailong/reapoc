<?php
/**
 * Settings for Fashionchick feeds
 */
class WooSEA_fashionchick {
	public $fashionchick;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$fashionchick = array(
			"Feed fields" => array(
                                "Product ID" => array(
                                        "name" => "Product ID",
                                        "feed_name" => "Product ID",
                                        "format" => "required",
                                        "woo_suggest" => "id",
                                ),
				"Url" => array(
					"name" => "Url",
					"feed_name" => "Url",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"Titel" => array(
					"name" => "Titel",
					"feed_name" => "Titel",
					"format" => "required",
					"woo_suggest" => "title",
				),
        			"Image" => array(
					"name" => "Image",
					"feed_name" => "Image",
					"format" => "required",
					"woo_suggest" => "image",
				),
                                 "Omschrijving" => array(
                                        "name" => "Omschrijving",
                                        "feed_name" => "Omschrijving",
                                        "format" => "required",
					"woo_suggest" => "description",
                                ),
				"Category (pad)" => array(
					"name" => "Category (pad)",
					"feed_name" => "Category (pad)",
					"format" => "required",
					"woo_suggest" => "category_path",
				),
				"SubCategory" => array(
					"name" => "SubCategory",
					"feed_name" => "SubCategory",
					"format" => "required",
					"woo_suggest" => "one_category",
				),
				"Prijs" => array(
					"name" => "Prijs",
					"feed_name" => "Prijs",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Merk" => array(
					"name" => "Merk",
					"feed_name" => "Merk",
					"format" => "required",
				),
        			"Kleur" => array(
					"name" => "Kleur",
					"feed_name" => "Kleur",
					"format" => "required",
				),
                                "Cluster ID" => array(
                                        "name" => "Cluster ID",
                                        "feed_name" => "Cluster ID",
                                        "format" => "optional",
                                ),
                                "Levertijd" => array(
                                        "name" => "Levertijd",
                                        "feed_name" => "Levertijd",
                                        "format" => "optional",
				),
                                "Verzendkosten" => array(
                                        "name" => "Verzendkosten",
                                        "feed_name" => "Verzendkosten",
                                        "format" => "optional",
                                ),
                                "Oude prijs" => array(
                                        "name" => "Oude prijs",
                                        "feed_name" => "Oude prijs",
                                        "format" => "optional",
                                ),
                                "Product maten" => array(
                                        "name" => "Product maten",
                                        "feed_name" => "Product maten",
                                        "format" => "required",
					"woo_suggest" => "description",
                                ),
                                "Voorraad" => array(
                                        "name" => "Voorraad",
                                        "feed_name" => "Voorraad",
                                        "format" => "optional",
                                ),
                                "Voorrraad aantal" => array(
                                        "name" => "Voorraad aantal",
                                        "feed_name" => "Voorraad aantal",
                                        "format" => "required",
                                ),
                           	"Materiaal" => array(
                                        "name" => "Materiaal",
                                        "feed_name" => "Materiaal",
                                        "format" => "required",
                                ),
                     		"Geslacht" => array(
                                        "name" => "Geslacht",
                                        "feed_name" => "Geslacht",
                                        "format" => "required",
                                ),
			),
		);
		return $fashionchick;
	}
}
?>
