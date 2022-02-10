<?php
/**
 * Settings for Google Local Products feeds
 */
class WooSEA_google_local_products {
	public $google_local_products;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$google_local_products = array(
			"Local products fields" => array(
				"Itemid" => array(
					"name" => "Id",
					"feed_name" => "g:id",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Title" => array(
					"name" => "Title",
					"feed_name" => "g:title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Description" => array(
					"name" => "Description",
					"feed_name" => "g:description",
					"format" => "optional",
					"woo_suggest" => "description",
				),
                                "Image link" => array(
                                        "name" => "image_link",
                                        "feed_name" => "g:image_link",
                                        "format" => "optional",
                                        "woo_suggest" => "image",
                                ),
                                "Condition" => array(
                                        "name" => "condition",
                                        "feed_name" => "g:condition",
                                        "format" => "optional",
                                        "woo_suggest" => "condition",
                                ),
                                "Gtin" => array(
                                        "name" => "gtin",
                                        "feed_name" => "g:gtin",
                                        "format" => "optional",
                                ),
                                "MPN" => array(
                                        "name" => "MPN",
                                        "feed_name" => "g:mpn",
                                        "format" => "optional",
                                ),
                                "Brand" => array(
                                        "name" => "brand",
                                        "feed_name" => "g:brand",
                                        "format" => "optional",
                                ),
                                "Google product category" => array(
                                        "name" => "google_product_category",
                                        "feed_name" => "g:google_product_category",
                                        "format" => "optional",
                                        "woo_suggest" => "categories",
                                ),
                                "Energy efficiency class" => array(
                                        "name" => "energy_efficiency_class",
                                        "feed_name" => "g:energy_efficiency_class",
                                        "format" => "optional",
                                ),
                                "Energy efficiency class min" => array(
                                        "name" => "energy_efficiency_class_min",
                                        "feed_name" => "g:energy_efficiency_class_min",
                                        "format" => "optional",
                                ),
                                "Energy efficiency class max" => array(
                                        "name" => "energy_efficiency_class_max",
                                        "feed_name" => "g:energy_efficiency_class_max",
                                        "format" => "optional",
                                ),
                                "Webitemid" => array(
                                        "name" => "webitemid",
                                        "feed_name" => "g:webitemid",
                                        "format" => "optional",
                                ),
				"Price" => array(
					"name" => "Price",
					"feed_name" => "g:price",
					"format" => "optional",
					"woo_suggest" => "price",
				),
				"Sale price" => array(
					"name" => "Sale price",
					"feed_name" => "g:sale_price",
					"format" => "optional",
					"woo_suggest" => "sale_price",
				),
                                "Sale price effective date" => array(
                                        "name" => "Sale price effective date",
                                        "feed_name" => "g:sale_price_effective_date",
                                        "format" => "optional",
                                        "woo_suggest" => "sale_price_effective_date",
                                ),
                                "Unit pricing measure" => array(
                                        "name" => "unit_pricing_measure",
                                        "feed_name" => "g:unit_pricing_measure",
                                        "format" => "optional",
                                ),
                                "Unit pricing base measure" => array(
                                        "name" => "unit_pricing_base_measure",
                                        "feed_name" => "g:unit_pricing_base_measure",
                                        "format" => "optional",
                                ),
                                "Pickup method" => array(
                                        "name" => "Pickup method",
                                        "feed_name" => "g:pickup_method",
                                        "format" => "optional",
                                ),
                                "Pickup SLA" => array(
                                        "name" => "Pickup SLA",
                                        "feed_name" => "g:pickup_sla",
                                        "format" => "optional",
                                ),
                                "Pickup link template" => array(
                                        "name" => "Pickup link template",
                                        "feed_name" => "g:pickup_link_template",
                                        "format" => "optional",
                                ),
                                "Mobile pickup link template" => array(
                                        "name" => "Mobile pickup link template",
                                        "feed_name" => "g:mobile_pickup_link_template",
                                        "format" => "optional",
                                ),
                                "Link template" => array(
                                        "name" => "Link template",
                                        "feed_name" => "g:link_template",
                                        "format" => "optional",
                                ),
                                "Mobile link template" => array(
                                        "name" => "Mobile link template",
                                        "feed_name" => "g:mobile_link_template",
                                        "format" => "optional",
                                ),
                                "Ads redirect" => array(
                                        "name" => "Ads redirect",
                                        "feed_name" => "g:ads_redirect",
                                        "format" => "optional",
                                ),
                               "Age group" => array(
                                        "name" => "age_group",
                                        "feed_name" => "g:age_group",
                                        "format" => "optional",
                                ),
                                "Color" => array(
                                        "name" => "color",
                                        "feed_name" => "g:color",
                                        "format" => "optional",
                                ),
                                "Gender" => array(
                                        "name" => "gender",
                                        "feed_name" => "g:gender",
                                        "format" => "optional",
                                ),
                                "Size" => array(
                                        "name" => "size",
                                        "feed_name" => "g:size",
                                        "format" => "optional",
                                ),
                                "Item group ID" => array(
                                        "name" => "item_group_id",
                                        "feed_name" => "g:item_group_id",
                                        "format" => "optional",
                                ),
			),
		);
		return $google_local_products;
	}
}
?>
