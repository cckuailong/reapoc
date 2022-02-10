<?php
/**
 * Settings for Yandex feeds
 */
class WooSEA_yandex {
	public $yandex;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$yandex = array(
			"Feed fields" => array(
				"id" => array(
					"name" => "id",
					"feed_name" => "id",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"type" => array(
					"name" => "type",
					"feed_name" => "type",
					"format" => "optional",
				),
				"available" => array(
					"name" => "available",
					"feed_name" => "available",
					"format" => "required",
					"woo_suggest" => "availability",
				),
				"bid" => array(
					"name" => "bid",
					"feed_name" => "bid",
					"format" => "optional",
				),
				"cbid" => array(
					"name" => "cbid",
					"feed_name" => "cbid",
					"format" => "optional",
				),
				"url" => array(
					"name" => "url",
					"feed_name" => "url",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"price" => array(
					"name" => "price",
					"feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"currencyId" => array(
					"name" => "currencyId",
					"feed_name" => "currencyId",
					"format" => "required",
				),
        			"categoryId" => array(
					"name" => "categoryId",
					"feed_name" => "categoryId",
					"format" => "required",
					"woo_suggest" => "categories",
				),
                                "picture" => array(
                                        "name" => "picture",
                                        "feed_name" => "picture",
                                        "format" => "optional",
					"woo_suggest" => "image",
                                ),
                                "typePrefix" => array(
                                        "name" => "typePrefix",
                                        "feed_name" => "typePrefix",
                                        "format" => "optional",
                                ),
                                "store" => array(
                                        "name" => "store",
                                        "feed_name" => "store",
                                        "format" => "optional",
                                ),
                                "pickup" => array(
                                        "name" => "pickup",
                                        "feed_name" => "pickup",
                                        "format" => "optional",
                                ),
                                "delivery" => array(
                                        "name" => "delivery",
                                        "feed_name" => "delivery",
                                        "format" => "optional",
                                ),
                                "name" => array(
                                        "name" => "name",
                                        "feed_name" => "name",
                                        "format" => "required",
					"woo_suggest" => "title",
                                ),
                                "model" => array(
                                        "name" => "model",
                                        "feed_name" => "model",
                                        "format" => "required",
                                ),
                                "description" => array(
                                        "name" => "description",
                                        "feed_name" => "description",
                                        "format" => "optional",
					"woo_suggest" => "description",
                                ),
                                "vendor" => array(
                                        "name" => "vendor",
                                        "feed_name" => "vendor",
                                        "format" => "optional",
                                ),
                                "vendorCode" => array(
                                        "name" => "vendorCode",
                                        "feed_name" => "vendorCode",
                                        "format" => "optional",
                                ),
                                "local_delivery_cost" => array(
                                        "name" => "local_delivery_cost",
                                        "feed_name" => "local_delivery_cost",
                                        "format" => "optional",
                                ),
                                "sales_notes" => array(
                                        "name" => "sales_notes",
                                        "feed_name" => "sales_notes",
                                        "format" => "optional",
                                ),
                                "manufacturer_warranty" => array(
                                        "name" => "manufacturer_warranty",
                                        "feed_name" => "manufacturer_warranty",
                                        "format" => "optional",
                                ),
                                "country_of_origin" => array(
                                        "name" => "country_of_origin",
                                        "feed_name" => "country_of_origin",
                                        "format" => "optional",
                                ),
                                "downloadable" => array(
                                        "name" => "downloadable",
                                        "feed_name" => "downloadable",
                                        "format" => "optional",
                                ),
                                "adult" => array(
                                        "name" => "adult",
                                        "feed_name" => "adult",
                                        "format" => "optional",
                                ),
                                "age" => array(
                                        "name" => "age",
                                        "feed_name" => "age",
                                        "format" => "optional",
                                ),
                                "barcode" => array(
                                        "name" => "barcode",
                                        "feed_name" => "barcode",
                                        "format" => "optional",
                                ),
                                "author" => array(
                                        "name" => "author",
                                        "feed_name" => "author",
                                        "format" => "optional",
                                ),
                                "artist" => array(
                                        "name" => "artist",
                                        "feed_name" => "artist",
                                        "format" => "optional",
                                ),
                                "publisher" => array(
                                        "name" => "publisher",
                                        "feed_name" => "publisher",
                                        "format" => "optional",
                                ),
                                "series" => array(
                                        "name" => "series",
                                        "feed_name" => "series",
                                        "format" => "optional",
                                ),
                                "year" => array(
                                        "name" => "year",
                                        "feed_name" => "year",
                                        "format" => "optional",
                                ),
                                "ISBN" => array(
                                        "name" => "ISBN",
                                        "feed_name" => "ISBN",
                                        "format" => "optional",
                                ),
                                "volume" => array(
                                        "name" => "volume",
                                        "feed_name" => "volume",
                                        "format" => "optional",
                                ),
                                "part" => array(
                                        "name" => "part",
                                        "feed_name" => "part",
                                        "format" => "optional",
                                ),
                                "language" => array(
                                        "name" => "language",
                                        "feed_name" => "language",
                                        "format" => "optional",
                                ),
                                "binding" => array(
                                        "name" => "binding",
                                        "feed_name" => "binding",
                                        "format" => "optional",
                                ),
                                "page_extent" => array(
                                        "name" => "page_extent",
                                        "feed_name" => "page_extent",
                                        "format" => "optional",
                                ),
                                "table_of_contents" => array(
                                        "name" => "table_of_contents",
                                        "feed_name" => "table_of_contents",
                                        "format" => "optional",
                                ),
                                "performed_by" => array(
                                        "name" => "performed_by",
                                        "feed_name" => "performed_by",
                                        "format" => "optional",
                                ),
                                "performance_type" => array(
                                        "name" => "performance_type",
                                        "feed_name" => "performance_type",
                                        "format" => "optional",
                                ),
                                "format" => array(
                                        "name" => "format",
                                        "feed_name" => "format",
                                        "format" => "optional",
                                ),
                                "storage" => array(
                                        "name" => "storage",
                                        "feed_name" => "storage",
                                        "format" => "optional",
                                ),
                                "recording_length" => array(
                                        "name" => "recording_length",
                                        "feed_name" => "recording_length",
                                        "format" => "optional",
                                ),
                                "media" => array(
                                        "name" => "media",
                                        "feed_name" => "media",
                                        "format" => "optional",
                                ),
                                "starring" => array(
                                        "name" => "starring",
                                        "feed_name" => "starring",
                                        "format" => "optional",
                                ),
                                "director" => array(
                                        "name" => "director",
                                        "feed_name" => "director",
                                        "format" => "optional",
                                ),
                                "originalName" => array(
                                        "name" => "originalName",
                                        "feed_name" => "originalName",
                                        "format" => "optional",
                                ),
                                "worldRegion" => array(
                                        "name" => "worldRegion",
                                        "feed_name" => "worldRegion",
                                        "format" => "optional",
                                ),
   				"country" => array(
                                        "name" => "country",
                                        "feed_name" => "country",
                                        "format" => "optional",
                                ),
   				"region" => array(
                                        "name" => "region",
                                        "feed_name" => "region",
                                        "format" => "optional",
                                ),
   				"days" => array(
                                        "name" => "days",
                                        "feed_name" => "days",
                                        "format" => "optional",
                                ),
   				"dataTour" => array(
                                        "name" => "dataTour",
                                        "feed_name" => "dataTour",
                                        "format" => "optional",
                                ),
   				"hotel_stars" => array(
                                        "name" => "hotel_stars",
                                        "feed_name" => "hotel_stars",
                                        "format" => "optional",
                                ),
   				"room" => array(
                                        "name" => "room",
                                        "feed_name" => "room",
                                        "format" => "optional",
                                ),
   				"meal" => array(
                                        "name" => "meal",
                                        "feed_name" => "meal",
                                        "format" => "optional",
                                ),
   				"included" => array(
                                        "name" => "included",
                                        "feed_name" => "included",
                                        "format" => "optional",
                                ),
   				"transport" => array(
                                        "name" => "transport",
                                        "feed_name" => "transport",
                                        "format" => "optional",
                                ),
   				"place" => array(
                                        "name" => "place",
                                        "feed_name" => "place",
                                        "format" => "optional",
                                ),
   				"hall_plan" => array(
                                        "name" => "hall_plan",
                                        "feed_name" => "hall_plan",
                                        "format" => "optional",
                                ),
   				"date" => array(
                                        "name" => "date",
                                        "feed_name" => "date",
                                        "format" => "optional",
                                ),
   				"is_premiere" => array(
                                        "name" => "is_premiere",
                                        "feed_name" => "is_premiere",
                                        "format" => "optional",
                                ),
   				"is_kids" => array(
                                        "name" => "is_kids",
                                        "feed_name" => "is_kids",
                                        "format" => "optional",
                                ),
                                "Item group ID" => array(
                                        "name" => "item_group_id",
                                        "feed_name" => "item_group_id",
                                        "format" => "optional",
                                )
			),
		);
		return $yandex;
	}
}
?>
