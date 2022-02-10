<?php
/**
 * Settings for Salidzini.lv Latvia feeds
 */
class WooSEA_salidzini {
	public $salidzini;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$salidzini = array(
			"Feed fields" => array(
				"Manufacturer" => array(
					"name" => "Manufacturer",
					"feed_name" => "manufacturer",
					"format" => "required",
				),
				"Model" => array(
					"name" => "Model",
					"feed_name" => "model",
					"format" => "required",
				),
				"Color" => array(
					"name" => "Color",
					"feed_name" => "color",
					"format" => "required",
				),
				"Name" => array(
					"name" => "Name",
					"feed_name" => "name",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Link" => array(
					"name" => "Link",
					"feed_name" => "link",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"Price" => array(
					"name" => "Price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Image" => array(
					"name" => "Image",
					"feed_name" => "image",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Category full" => array(
					"name" => "Category full",
					"feed_name" => "category_full",
					"format" => "required",
					"woo_suggest" => "categories",
				),
				"Category link" => array(
					"name" => "Category link",
					"feed_name" => "category_link",
					"format" => "required",
					"woo_suggest" => "category_link",
				),
				"In Stock" => array(
					"name" => "In Stock",
					"feed_name" => "in_stock",
					"format" => "required",
				),
				"Delivery cost Riga" => array(
					"name" => "Delivery cost Riga",
					"feed_name" => "delivery_cost_riga",
					"format" => "required",
				),
				"Delivery latvija" => array(
					"name" => "Delivery latvija",
					"feed_name" => "delivery_latvija",
					"format" => "required",
				),
				"Delivery latvijas pasts" => array(
					"name" => "Delivery latvijas pasts",
					"feed_name" => "delivery_latvijas_pasts",
					"format" => "required",
				),
				"Delivery dpd paku bode" => array(
					"name" => "Delivery dpd paku bode",
					"feed_name" => "delivery_dpd_paku_bode",
					"format" => "required",
				),
				"Delivery pasta stacija" => array(
					"name" => "Delivery pasta stacija",
					"feed_name" => "delivery_pasta_stacija",
					"format" => "required",
				),
				"Delivery omniva" => array(
					"name" => "Delivery omniva",
					"feed_name" => "delivery_omniva",
					"format" => "required",
				),
				"Delivery circlek" => array(
					"name" => "Delivery circlek",
					"feed_name" => "delivery_circlek",
					"format" => "required",
				),
				"Delivery venipak" => array(
					"name" => "Delivery venipak",
					"feed_name" => "delivery_venipak",
					"format" => "required",
				),
				"Delivery days riga" => array(
					"name" => "Delivery days riga",
					"feed_name" => "delivery_days_riga",
					"format" => "required",
				),
				"Delivery days latvija" => array(
					"name" => "Delivery days latvija",
					"feed_name" => "delivery_days_latvija",
					"format" => "required",
				),
				"Used" => array(
					"name" => "Used",
					"feed_name" => "used",
					"format" => "required",
				),
			),
		);
		return $salidzini;
	}
}
?>
