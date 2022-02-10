<?php
/**
 * Settings for Kijiji Italy feeds
 */
class WooSEA_kijiji {
	public $kijiji;

        public static function get_channel_attributes() {
                $sitename = get_option('blogname');

        	$kijiji = array(
			"Feed fields" => array(
				"PartnerId" => array(
					"name" => "PartnerId",
					"feed_name" => "PartnerId",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Action" => array(
					"name" => "Action",
					"feed_name" => "Action",
					"format" => "required",
				),
				"Title" => array(
					"name" => "Title",
					"feed_name" => "Title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Description" => array(
					"name" => "Description",
					"feed_name" => "Description",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"E-mail" => array(
					"name" => "E-mail",
					"feed_name" => "Email",
					"format" => "required",
				),
				"URL" => array(
					"name" => "URL",
					"feed_name" => "URL",
					"format" => "required",
					"woo_suggest" => "link",
				),
                                "Price" => array(
                                        "name" => "Price",
                                        "feed_name" => "Price",
                                        "format" => "required",
                                        "woo_suggest" => "price",
                                ),
                                "Tipo Prezzo" => array(
                                        "name" => "Tipo Prezzo",
                                        "feed_name" => "Tipo Prezzo",
                                        "format" => "optional",
                                ),
                                "Municipality code" => array(
                                        "name" => "Municipality code",
                                        "feed_name" => "Municipality code",
                                        "format" => "optional",
                                ),
                                "Category" => array(
                                        "name" => "Category",
                                        "feed_name" => "Category",
                                        "format" => "required",
					"woo_suggest" => "categories",
                                ),
                                "Seller type" => array(
                                        "name" => "Seller type",
                                        "feed_name" => "Seller type",
                                        "format" => "optional",
                                ),
				"Publication date" => array(
					"name" => "Publication date",
					"feed_name" => "Publication date",
					"format" => "required",
					"woo_suggest" => "publication_date",
				),
                                "Pic 1" => array(
                                        "name" => "Pic 1",
                                        "feed_name" => "Pic 1",
                                        "format" => "optional",
                                ),
                                "Pic 2" => array(
                                        "name" => "Pic 2",
                                        "feed_name" => "Pic 2",
                                        "format" => "optional",
                               ),
                               "Pic 3" => array(
                                        "name" => "Pic 3",
                                        "feed_name" => "Pic 3",
                                        "format" => "optional",
                                ),
                                "Pic 4" => array(
                                        "name" => "Pic 4",
                                        "feed_name" => "Pic 4",
                                        "format" => "optional",
                                ),
                                "Pic 5" => array(
                                        "name" => "Pic 5",
                                        "feed_name" => "Pic 5",
                                        "format" => "optional",
                                ),
                                "Pic 6" => array(
                                        "name" => "Pic 6",
                                        "feed_name" => "Pic 6",
                                        "format" => "optional",
                                ),
                                "Pic 7" => array(
                                        "name" => "Pic 7",
                                        "feed_name" => "Pic 7",
                                        "format" => "optional",
                                ),
                                "Pic 8" => array(
                                        "name" => "Pic 8",
                                        "feed_name" => "Pic 8",
                                        "format" => "optional",
                                ),
                                "Pic 9" => array(
                                        "name" => "Pic 9",
                                        "feed_name" => "Pic 9",
                                        "format" => "optional",
                                ),
			),
		);
		return $kijiji;
	}
}
?>
