<?php
/**
 * Settings for Moebel feeds
 */
class WooSEA_moebel {
	public $moebel;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$moebel = array(
			"Feed fields" => array(
				"Art nr" => array(
					"name" => "art_nr",
					"feed_name" => "art_nr",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Art name" => array(
					"name" => "art_name",
					"feed_name" => "art_name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Art beschreibung" => array(
					"name" => "art_beschreibung",
					"feed_name" => "art_beschreibung",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"Art URL" => array(
					"name" => "art_url",
					"feed_name" => "art_url",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"Art img URL" => array(
					"name" => "art_img_url",
					"feed_name" => "art_img_url",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Art waehrung" => array(
					"name" => "art_waehrung",
					"feed_name" => "art_waehrung",
					"format" => "required",
				),
                                "Art preis" => array(
                                        "name" => "art_price",
                                        "feed_name" => "art_price",
                                        "format" => "required",
                                        "woo_suggest" => "price",
                                ),
                                "Art lieferkosten" => array(
                                        "name" => "art_lieferkosten",
                                        "feed_name" => "art_lieferkosten",
                                        "format" => "required",
                                ),
                                "Art stamm" => array(
                                        "name" => "art_stamm",
                                        "feed_name" => "art_stamm",
                                        "format" => "optional",
                                ),
                                "Art ean" => array(
                                        "name" => "art_ean",
                                        "feed_name" => "art_ean",
                                        "format" => "optional",
                                ),
                                "Art plz" => array(
                                        "name" => "art_plz",
                                        "feed_name" => "art_plz",
                                        "format" => "optional",
                                ),
                              	"Art bidding bidDesktop" => array(
                                        "name" => "art_bidding.bidDesktop",
                                        "feed_name" => "art_bidding.biedDesktop",
                                        "format" => "optional",
                                ),
                              	"Art bidding factorMobile" => array(
                                        "name" => "art_bidding.factorMobile",
                                        "feed_name" => "art_bidding.factorMobile",
                                        "format" => "optional",
                                ),
                                "Art Google Shopping Target URL" => array(
                                        "name" => "art_Google_Shopping_Target_URL",
                                        "feed_name" => "art_Google_Shopping_Target_URL",
                                        "format" => "optional",
                                ),
                                "Art Clear Product URL" => array(
                                        "name" => "art_Clear_Product_URL",
                                        "feed_name" => "art_Clear_Product_URL",
                                        "format" => "optional",
                                ),
                                "Top Mid Low" => array(
                                        "name" => "top-mid-low",
                                        "feed_name" => "top-mid-low",
                                        "format" => "optional",
                                ),
                                "Art streichpreis" => array(
                                        "name" => "art_streichpreis",
                                        "feed_name" => "art_streichpreis",
                                        "format" => "optional",
                                ),
                                "Art lieferoptionen" => array(
                                        "name" => "art_lieferoptionen",
                                        "feed_name" => "art_lieferoptionen",
                                        "format" => "optional",
                                ),
                                "Partner Payment Methods" => array(
                                        "name" => "partner.paymentMethods",
                                        "feed_name" => "partner.paymentMethods",
                                        "format" => "optional",
                                ),
                                "Art services" => array(
                                        "name" => "art_services",
                                        "feed_name" => "art_services",
                                        "format" => "optional",
                                ),
                                "Art grundpreis" => array(
                                        "name" => "art_grundpreis",
                                        "feed_name" => "art_grundpreis",
                                        "format" => "optional",
                                ),
                                "Art grundpreis einheit" => array(
                                        "name" => "art_grundpreis_einheit",
                                        "feed_name" => "art_grundpreis_einheit",
                                        "format" => "optional",
                                ),
                                "Art finanzierung" => array(
                                        "name" => "art_finanzierung",
                                        "feed_name" => "art_finanzierung",
                                        "format" => "optional",
                                ),
                                "Art lieferzeit" => array(
                                        "name" => "art_lieferzeit",
                                        "feed_name" => "art_lieferzeit",
                                        "format" => "optional",
                                ),
                                "Art lieferzeit wert" => array(
                                        "name" => "art_lieferzeit_wert",
                                        "feed_name" => "art_lieferzeit_wert",
                                        "format" => "optional",
                                ),
                                "Art lieferkosten text" => array(
                                        "name" => "art_lieferkosten_text",
                                        "feed_name" => "art_lieferkosten_text",
                                        "format" => "optional",
                                ),
                                "Art versand at" => array(
                                        "name" => "art_versand_at",
                                        "feed_name" => "art_versand_at",
                                        "format" => "optional",
                                ),
                                "Art versand at preis" => array(
                                        "name" => "art_versand_at_preis",
                                        "feed_name" => "art_versand_at_preis",
                                        "format" => "optional",
                                ),
                                "Art versand ch" => array(
                                        "name" => "art_versand_ch",
                                        "feed_name" => "art_versand_ch",
                                        "format" => "optional",
                                ),
                                "Art versand ch preis" => array(
                                        "name" => "art_versand_ch_preis",
                                        "feed_name" => "art_versand_ch_preis",
                                        "format" => "optional",
                                ),
                                "Art versand sonstlaender" => array(
                                        "name" => "art_versand_sonstlaender",
                                        "feed_name" => "art_versand_sonstlaender",
                                        "format" => "optional",
                                ),
                                "Art montage" => array(
                                        "name" => "art_montage",
                                        "feed_name" => "art_montage",
                                        "format" => "optional",
                                ),
                                "Art montagepreis" => array(
                                        "name" => "art_montagepreis",
                                        "feed_name" => "art_montagepreis",
                                        "format" => "optional",
                                ),
                                "Art express" => array(
                                        "name" => "art_express",
                                        "feed_name" => "art_express",
                                        "format" => "optional",
                                ),
                                "Art verfuegbarkeit" => array(
                                        "name" => "art_verfuegbarkeit",
                                        "feed_name" => "art_verfuegbarkeit",
                                        "format" => "optional",
                                ),
                                "Art verfuegbarkeit" => array(
                                        "name" => "art_verfuegbarkeit",
                                        "feed_name" => "art_verfuegbarkeit",
                                        "format" => "optional",
                                ),
                                "Art farbe" => array(
                                        "name" => "art_farbe",
                                        "feed_name" => "art_farbe",
                                        "format" => "optional",
                                ),
                                "Art hauptfarbe" => array(
                                        "name" => "art_hauptfarbe",
                                        "feed_name" => "art_hauptfarbe",
                                        "format" => "optional",
                                ),
                                "Art material" => array(
                                        "name" => "art_material",
                                        "feed_name" => "art_material",
                                        "format" => "optional",
                                ),
                                "Art hauptmaterial" => array(
                                        "name" => "art_hauptmaterial",
                                        "feed_name" => "art_hauptmaterial",
                                        "format" => "optional",
                                ),
                                "Art holzart" => array(
                                        "name" => "art_holzart",
                                        "feed_name" => "art_holzart",
                                        "format" => "optional",
                                ),
                                "Art stil" => array(
                                        "name" => "art_stil",
                                        "feed_name" => "art_stil",
                                        "format" => "optional",
                                ),
                                "Art marke" => array(
                                        "name" => "art_marke",
                                        "feed_name" => "art_marke",
                                        "format" => "optional",
                                ),
                                "Art kategorie" => array(
                                        "name" => "art_kategorie",
                                        "feed_name" => "art_kategorie",
                                        "format" => "optional",
                                ),
                                "Art bewertung" => array(
                                        "name" => "art_bewertung",
                                        "feed_name" => "art_bewertung",
                                        "format" => "optional",
                                ),
                                "Art bewertungsanzahl" => array(
                                        "name" => "art_bewertungsanzahl",
                                        "feed_name" => "art_bewertungsanzahl",
                                        "format" => "optional",
                                ),
                                "Art extras" => array(
                                        "name" => "art_extras",
                                        "feed_name" => "art_extras",
                                        "format" => "optional",
                                ),
                                "Art geschlecht" => array(
                                        "name" => "art_geschlecht",
                                        "feed_name" => "art_geschlecht",
                                        "format" => "optional",
                                ),
                                "Art oberflaeche" => array(
                                        "name" => "art_oberflaeche",
                                        "feed_name" => "art_oberflaeche",
                                        "format" => "optional",
                                ),
                                "Art sets" => array(
                                        "name" => "art_sets",
                                        "feed_name" => "art_sets",
                                        "format" => "optional",
                                ),
                                "Art ausrichting" => array(
                                        "name" => "art_ausrichtung",
                                        "feed_name" => "art_ausrichting",
                                        "format" => "optional",
                                ),
                                "Art verwendungsort" => array(
                                        "name" => "art_verwendungsort",
                                        "feed_name" => "art_verwendungsort",
                                        "format" => "optional",
                                ),
                                "Art sitzplatze" => array(
                                        "name" => "art_sitzplatze",
                                        "feed_name" => "art_sitzplatze",
                                        "format" => "optional",
                                ),
                                "Art muster" => array(
                                        "name" => "art_muster",
                                        "feed_name" => "art_muster",
                                        "format" => "optional",
                                ),
                                "Art energiequelle" => array(
                                        "name" => "art_energiequelle",
                                        "feed_name" => "art_energiequelle",
                                        "format" => "optional",
                                ),
                                "Art siegel" => array(
                                        "name" => "art_siegel",
                                        "feed_name" => "art_siegel",
                                        "format" => "optional",
                                ),
                                "Art hersteller" => array(
                                        "name" => "art_hersteller",
                                        "feed_name" => "art_hersteller",
                                        "format" => "optional",
                                ),
                                "Art img url2" => array(
                                        "name" => "art_img_url2",
                                        "feed_name" => "art_img_url2",
                                        "format" => "optional",
                                ),
                                "Art img url3" => array(
                                        "name" => "art_img_url3",
                                        "feed_name" => "art_img_url3",
                                        "format" => "optional",
                                ),
                                "Art img url4" => array(
                                        "name" => "art_img_url4",
                                        "feed_name" => "art_img_url4",
                                        "format" => "optional",
                                ),
                                "Art img url5" => array(
                                        "name" => "art_img_url5",
                                        "feed_name" => "art_img_url5",
                                        "format" => "optional",
                                ),
                                "Art img url6" => array(
                                        "name" => "art_img_url6",
                                        "feed_name" => "art_img_url6",
                                        "format" => "optional",
                                ),
                                "Art img url7" => array(
                                        "name" => "art_img_url7",
                                        "feed_name" => "art_img_url7",
                                        "format" => "optional",
                                ),
                                "Art img url8" => array(
                                        "name" => "art_img_url8",
                                        "feed_name" => "art_img_url8",
                                        "format" => "optional",
                                ),
                                "Art img url9" => array(
                                        "name" => "art_img_url9",
                                        "feed_name" => "art_img_url9",
                                        "format" => "optional",
                                ),
                                "Art masse" => array(
                                        "name" => "art_masse",
                                        "feed_name" => "art_masse",
                                        "format" => "optional",
                                ),
                                "Art breite" => array(
                                        "name" => "art_breite",
                                        "feed_name" => "art_breite",
                                        "format" => "optional",
                                ),
                                "Art breite einheit" => array(
                                        "name" => "art_breite_einheit",
                                        "feed_name" => "art_breite_einheit",
                                        "format" => "optional",
                                ),
                                "Art tiefe" => array(
                                        "name" => "art_tiefe",
                                        "feed_name" => "art_tiefe",
                                        "format" => "optional",
                                ),
                                "Art tiefe einheit" => array(
                                        "name" => "art_tiefe_einheit",
                                        "feed_name" => "art_tiefe_einheit",
                                        "format" => "optional",
                                ),
                                "Art hoehe" => array(
                                        "name" => "art_hoehe",
                                        "feed_name" => "art_hoehe",
                                        "format" => "optional",
                                ),
                                "Art hoehe einheit" => array(
                                        "name" => "art_hoehe_einheit",
                                        "feed_name" => "art_hoehe_einheit",
                                        "format" => "optional",
                                ),
                                "Art form" => array(
                                        "name" => "art_form",
                                        "feed_name" => "art_form",
                                        "format" => "optional",
                                ),
                                "Art sitztiefe" => array(
                                        "name" => "art_sitztiefe",
                                        "feed_name" => "art_sitztiefe",
                                        "format" => "optional",
                                ),
                                "Art sitzhoehe" => array(
                                        "name" => "art_sitzhoehe",
                                        "feed_name" => "art_sitzhoehe",
                                        "format" => "optional",
                                ),
                                "Art sitzhoehe einheit" => array(
                                        "name" => "art_sitzhoehe_einheit",
                                        "feed_name" => "art_sitzhoehe_einheit",
                                        "format" => "optional",
                                ),
                                "Art sitztiefe einheit" => array(
                                        "name" => "art_sitztiefe_einheit",
                                        "feed_name" => "art_sitztiefe_einheit",
                                        "format" => "optional",
                                ),
                                "Art schenkelmass" => array(
                                        "name" => "art_schenkelmass",
                                        "feed_name" => "art_schenkelmass",
                                        "format" => "optional",
                                ),
                                "Art schenkelmass einheit" => array(
                                        "name" => "art_schenkelmass_einheit",
                                        "feed_name" => "art_schenkelmass_einheit",
                                        "format" => "optional",
                                ),
                                "Art durchmesser" => array(
                                        "name" => "art_durchmesser",
                                        "feed_name" => "art_durchmesser",
                                        "format" => "optional",
                                ),
                                "Art durchmesser einheit" => array(
                                        "name" => "art_durchmesser_einheit",
                                        "feed_name" => "art_durchmesser_einheit",
                                        "format" => "optional",
                                ),
                                "Art aufhaengung" => array(
                                        "name" => "art_aufhaengung",
                                        "feed_name" => "art_aufhaengung",
                                        "format" => "optional",
                                ),
                                "Art fuellmaterial" => array(
                                        "name" => "art_fuellmaterial",
                                        "feed_name" => "art_fuellmaterial",
                                        "format" => "optional",
                                ),
                                "Art funktion heimtex" => array(
                                        "name" => "art_funktion_heimtext",
                                        "feed_name" => "art_funtion_heimtext",
                                        "format" => "optional",
                                ),
                                "Art effizienzklasse" => array(
                                        "name" => "art_effizienzklasse",
                                        "feed_name" => "art_effizienzklasse",
                                        "format" => "optional",
                                ),
                                "Art lautstaerke" => array(
                                        "name" => "art_lautstaerke",
                                        "feed_name" => "art_lautstaerke",
                                        "format" => "optional",
                                ),
                                "Art helligkeit" => array(
                                        "name" => "art_helligkeit",
                                        "feed_name" => "art_helligkeit",
                                        "format" => "optional",
                                ),
                                "Art lichtfarbe" => array(
                                        "name" => "art_lichtfarbe",
                                        "feed_name" => "art_lichtfarbe",
                                        "format" => "optional",
                                ),
                                "Art kuechenelektro" => array(
                                        "name" => "art_kuechenelektro",
                                        "feed_name" => "art_kuechenelektro",
                                        "format" => "optional",
                                ),
                                "Art herdeigenschaften" => array(
                                        "name" => "art_herdeigenschaften",
                                        "feed_name" => "art_herdeigenschaften",
                                        "format" => "optional",
                                ),
                                "Art kochfeld" => array(
                                        "name" => "art_kochfeld",
                                        "feed_name" => "art_kochfeld",
                                        "format" => "optional",
                                ),
                                "Art material arbeitsplatte" => array(
                                        "name" => "art_material_arbeitsplatte",
                                        "feed_name" => "art_material_arbeitsplatte",
                                        "format" => "optional",
                                ),
                                "Art extras sessel" => array(
                                        "name" => "art_extras_sessel",
                                        "feed_name" => "art_extras_sessel",
                                        "format" => "optional",
                                ),
                                "Art schlaffunktion" => array(
                                        "name" => "art_schlaffunktion",
                                        "feed_name" => "art_schlaffunktion",
                                        "format" => "optional",
                                ),
                                "Art form sofa" => array(
                                        "name" => "art_form_sofa",
                                        "feed_name" => "art_form_sofa",
                                        "format" => "optional",
                                ),
                                "Art extras sofa" => array(
                                        "name" => "art_extras_sofa",
                                        "feed_name" => "art_extras_sofa",
                                        "format" => "optional",
                                ),
                                "Art material fuesse" => array(
                                        "name" => "art_material_fuesse",
                                        "feed_name" => "art_material_fuesse",
                                        "format" => "optional",
                                ),
                                "Art haertegrad matratze" => array(
                                        "name" => "art_haertegrad_matratze",
                                        "feed_name" => "art_haertegrad_matratze",
                                        "format" => "optional",
                                ),
                                "Art liegezonen" => array(
                                        "name" => "art_liegezonen",
                                        "feed_name" => "art_liegezonen",
                                        "format" => "optional",
                                ),
                                "Art allergiker matratze" => array(
                                        "name" => "art_allergiker_matratze",
                                        "feed_name" => "art_allergiker_matratze",
                                        "format" => "optional",
                                ),
                                "Art stauraum bett" => array(
                                        "name" => "art_stauraum_bett",
                                        "feed_name" => "art_stauraum_bett",
                                        "format" => "optional",
                                ),
                                "Art beleuchtung bett" => array(
                                        "name" => "art_beleuchtung_bett",
                                        "feed_name" => "art_beleuchtung_bett",
                                        "format" => "optional",
                                ),
                                "Art matratzenmasse" => array(
                                        "name" => "art_matratzenmasse",
                                        "feed_name" => "art_matratzenmasse",
                                        "format" => "optional",
                                ),
                                "Art matratzenart" => array(
                                        "name" => "art_matratzenart",
                                        "feed_name" => "art_matratzenart",
                                        "format" => "optional",
                                ),
                                "Art bezugwaschbar" => array(
                                        "name" => "art_bezugwaschbar",
                                        "feed_name" => "art_bezugwaschbar",
                                        "format" => "optional",
                                ),
                                "Art vitrine" => array(
                                        "name" => "art_vitrine",
                                        "feed_name" => "art_vitrine",
                                        "format" => "optional",
                                ),
                                "Art abschliessbar" => array(
                                        "name" => "art_abschliessbar",
                                        "feed_name" => "art_abschliessbar",
                                        "format" => "optional",
                                ),
                                "Art variable faecher" => array(
                                        "name" => "art_variable_faecher",
                                        "feed_name" => "art_variable_faecher",
                                        "format" => "optional",
                                ),
                                "Art tuerenzahl" => array(
                                        "name" => "art_tuerenzahl",
                                        "feed_name" => "art_tuerenzahl",
                                        "format" => "optional",
                                ),
                                "Art schubladenzahl" => array(
                                        "name" => "art_schubladenzahl",
                                        "feed_name" => "art_schubladenzahl",
                                        "format" => "optional",
                                ),
                                "Art faecherzahl" => array(
                                        "name" => "art_faecherzahl",
                                        "feed_name" => "art_faecherzahl",
                                        "format" => "optional",
                                ),
                                "Art tischform" => array(
                                        "name" => "art_tischform",
                                        "feed_name" => "art_tischform",
                                        "format" => "optional",
                                ),
                                "Art tischfunktion" => array(
                                        "name" => "art_tischfunktion",
                                        "feed_name" => "art_tischfunktion",
                                        "format" => "optional",
                                ),
                                "Art gestell material" => array(
                                        "name" => "art_gestell_material",
                                        "feed_name" => "art_gestell_material",
                                        "format" => "optional",
                                ),
			),
		);
		return $moebel;
	}
}
?>
