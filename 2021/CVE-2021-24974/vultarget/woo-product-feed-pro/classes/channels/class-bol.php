<?php
/**
 * Settings for Bol.com product feeds
 */
class WooSEA_bol {
	public $bol;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$bol = array(
			"Feed fields" => array(
				"Reference" => array(
					"name" => "Reference",
					"feed_name" => "Reference",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"EAN" => array(
					"name" => "EAN",
					"feed_name" => "EAN",
					"format" => "required",
				),
				"Condition" => array(
					"name" => "Condition",
					"feed_name" => "Condition",
					"format" => "required",
					"woo_suggest" => "condition",
				),
				"Stock" => array(
					"name" => "Stock",
					"feed_name" => "Stock",
					"format" => "required",
					"woo_suggest" => "availability",
				),
				"Price" => array(
					"name" => "Price",
					"feed_name" => "Price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Fullfillment by" => array(
					"name" => "Fullfillment by",
					"feed_name" => "Fullfillment by",
					"format" => "required",
				),
				"Offer description" => array(
					"name" => "Offer description",
					"feed_name" => "Offer description",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"For sale" => array(
					"name" => "For sale",
					"feed_name" => "For sale",
					"format" => "required",
				),
				"Title" => array(
					"name" => "Title",
					"feed_name" => "Title",
					"format" => "required",
					"woo_suggest" => "title",
				),
			),
		);
		return $bol;
	}
}
?>
