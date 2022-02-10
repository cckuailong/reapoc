<?php
/**
 * Settings for Google DSA product feeds
 */
class WooSEA_google_dsa {
	public $google_dsa;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$google_dsa = array(
			"DSA fields" => array(
				"Page URL" => array(
					"name" => "Page URL",
					"feed_name" => "Page URL",
					"format" => "required",
					"woo_suggest" => "link",
				),
                                "Custom label" => array(
                                        "name" => "Custom label",
                                        "feed_name" => "Custom label",
                                        "format" => "required",
                                ),
			),
		);
		return $google_dsa;
	}
}
?>
