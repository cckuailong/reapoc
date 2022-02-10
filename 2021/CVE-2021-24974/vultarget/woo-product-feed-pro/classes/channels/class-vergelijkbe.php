<?php
/**
 * Settings for Vergelijk.be feeds
 */
class WooSEA_vergelijkbe {
	public $vergelijkbe;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$vergelijkbe = array(
			"Feed fields" => array(
				"shopReference" => array(
					"name" => "Shop reference",
					"feed_name" => "shopReference",
					"format" => "required",
				),
				"shopOfferId" => array(
					"name" => "Shop offer id",
					"feed_name" => "shopOfferId",
					"format" => "optional",
				),
				"shopCategory" => array(
					"name" => "Shop category",
					"feed_name" => "shopCategory",
					"format" => "required",
					"woo_suggest" => "categories",
				),
				"Brand" => array(
					"name" => "Brand",
					"feed_name" => "brand",
					"format" => "required",
				),
				"Description" => array(
					"name" => "Description",
					"feed_name" => "description",
					"format" => "optional",
					"woo_suggest" => "description",
				),
				"Name" => array(
					"name" => "Product name",
					"feed_name" => "name",
					"format" => "required",
					"woo_suggest" => "name",
				),
				"IdentifierType" => array(
					"name" => "Identifier type",
					"feed_name" => "type",
					"format" => "optional",
				),
				"IdentifierValue" => array(
					"name" => "Identifier value",
					"feed_name" => "value",
					"format" => "optional",
				),
				"FeatureName" => array(
					"name" => "Feature name",
					"feed_name" => "name",
					"format" => "optional",
				),
				"FeatureValue" => array(
					"name" => "Feature value",
					"feed_name" => "value",
					"format" => "optional",
				),
				"basePrice" => array(
					"name" => "Selling price",
					"feed_name" => "basePrice",
					"format" => "required",
				),
				"promotionText" => array(
					"name" => "Promotional text",
					"feed_name" => "promotionText",
					"format" => "optional",
				),
				"Price" => array(
					"name" => "Delivery price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Deeplink" => array(
					"name" => "Deeplink",
					"feed_name" => "deepLink",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"mediaType" => array(
					"name" => "Media type",
					"feed_name" => "type",
					"format" => "optional",
				),
				"mediaURL" => array(
					"name" => "Media url",
					"feed_name" => "url",
					"format" => "optional",
				),
				"stockStatus" => array(
					"name" => "Stock status",
					"feed_name" => "inStock",
					"format" => "optional",
				),
				"nrInStock" => array(
					"name" => "Nr. products on stock",
					"feed_name" => "nrInStock",
					"format" => "optional",
				),
				"countryCode" => array(
					"name" => "Shipping country code",
					"feed_name" => "countryCode",
					"format" => "optional",
				),
				"deliveryTime" => array(
					"name" => "Delivery time",
					"feed_name" => "deliveryTime",
					"format" => "required",
				),
				"shippingDescription" => array(
					"name" => "Shipping description",
					"feed_name" => "method",
					"format" => "optional",
				),
				"method" => array(
					"name" => "Shipping method",
					"feed_name" => "method",
					"format" => "required",
				),
				"ServicecountryCode" => array(
					"name" => "Service country code",
					"feed_name" => "countryCode",
					"format" => "required",
				),
				"ServiceName" => array(
					"name" => "Service name",
					"feed_name" => "name",
					"format" => "optional",
				),
				"ServicePrice" => array(
					"name" => "Service price",
					"feed_name" => "price",
					"format" => "optional",
				),
				"ServiceType" => array(
					"name" => "Service type",
					"feed_name" => "type",
					"format" => "optional",
				),
			),
		);
		return $vergelijkbe;
	}
}
?>
