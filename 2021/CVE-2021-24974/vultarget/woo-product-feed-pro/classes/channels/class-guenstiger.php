<?php
/**
 * Settings for Guenstiger feeds
 */
class WooSEA_guenstiger {
	public $guenstiger;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$guenstiger = array(
			"Feed fields" => array(
				"Bestellnummer" => array(
					"name" => "bestellnummer",
					"feed_name" => "bestellnummer",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"HerstellerArtNr" => array(
					"name" => "HerstellerArtNr",
					"feed_name" => "HerstellerArtNr",
					"format" => "required",
				),
				"Hersteller" => array(
					"name" => "Hersteller",
					"feed_name" => "Hersteller",
					"format" => "required",
				),
				"ProductLink" => array(
					"name" => "ProductLink",
					"feed_name" => "ProductLink",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"FotoLink" => array(
					"name" => "FotoLink",
					"feed_name" => "FotoLink",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"ProducktBeschreibung" => array(
					"name" => "ProduktBeschreibung",
					"feed_name" => "ProduktBeschreibung",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"ProduktBezeichnung" => array(
					"name" => "ProduktBezeichnung",
					"feed_name" => "ProduktBezeichnung",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Preis" => array(
					"name" => "Preis",
					"feed_name" => "Preis",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Lieferzeit" => array(
					"name" => "Lieferzeit",
					"feed_name" => "Lieferzeit",
					"format" => "required",
				),
                                "EANCode" => array(
                                        "name" => "EANCode",
                                        "feed_name" => "EANCode",
                                        "format" => "required",
                                ),
                                "Kategorie" => array(
                                        "name" => "Kategorie",
                                        "feed_name" => "Kategorie",
                                        "format" => "required",
					"woo_suggest" => "category",
                                ),
                                "VersandVorkasse" => array(
                                        "name" => "VersandVorkasse",
                                        "feed_name" => "VersandVorkasse",
                                        "format" => "required",
                                ),
                                "VersandPayPal" => array(
                                        "name" => "VersandPayPal",
                                        "feed_name" => "VersandPaypal",
                                        "format" => "required",
                                ),
                                "VersandKreditkarte" => array(
                                        "name" => "VersandKreditkarte",
                                        "feed_name" => "VersandKreditkarte",
                                        "format" => "required",
                                ),
                                "VersandLastschrift" => array(
                                        "name" => "VersandLastschrift",
                                        "feed_name" => "VersandLandschrift",
                                        "format" => "required",
                                ),
                                "VersandRechnung" => array(
                                        "name" => "VersandRechnung",
                                        "feed_name" => "VersandRechnung",
                                        "format" => "required",
                                ),
                                "VersandNachnahme" => array(
                                        "name" => "VersandNachnahme",
                                        "feed_name" => "VersandNachnahme",
                                        "format" => "required",
                                ),
                                "Grundpreis komplett" => array(
                                        "name" => "Grundpreis komplett",
                                        "feed_name" => "Grundpres komplett",
                                        "format" => "optional",
                                ),
                                 "Energieeffizienzklasse" => array(
                                        "name" => "Energieeffizienzklasse",
                                        "feed_name" => "Energieeffizienzklasse",
                                        "format" => "optional",
                                ),
                                 "Keyword" => array(
                                        "name" => "Keyword",
                                        "feed_name" => "Keyword",
                                        "format" => "optional",
                                ),
                                "Gewicht" => array(
                                        "name" => "Gewicht",
                                        "feed_name" => "Gewicht",
                                        "format" => "optional",
                                ),
                                "Groesse" => array(
                                        "name" => "Groesse",
                                        "feed_name" => "Groesse",
                                        "format" => "optional",
                                ),
                                "Farbe" => array(
                                        "name" => "Farbe",
                                        "feed_name" => "Farbe",
                                        "format" => "optional",
                                ),
                                "Geschlecht" => array(
                                        "name" => "Geschlecht",
                                        "feed_name" => "Geschlecht",
                                        "format" => "optional",
                                ),
                                "Erwachsene / Kind" => array(
                                        "name" => "Erwachsene / Kind",
                                        "feed_name" => "Erwachsene / Kind",
                                        "format" => "optional",
                                ),
                                "PZN" => array(
                                        "name" => "PZN",
                                        "feed_name" => "PZN",
                                        "format" => "optional",
                                ),
                                "Reifentyp" => array(
                                        "name" => "Reifentyp",
                                        "feed_name" => "Reifentyp",
                                        "format" => "optional",
                                ),
                                "Reifensaison" => array(
                                        "name" => "Reifensaison",
                                        "feed_name" => "Reifensaison",
                                        "format" => "optional",
                                ),
                                "Reifenmass" => array(
                                        "name" => "Reifenmass",
                                        "feed_name" => "Reifenmass",
                                        "format" => "optional",
                                ),
			),
		);
		return $guenstiger;
	}
}
?>
