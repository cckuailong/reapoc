<?php
/**
 * Settings for Facebook DRM product feeds
 */
class WooSEA_facebook_drm {
	public $facebook_drm;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$facebook_drm = array(
			"Basic fields" => array(
				"id" => array(
					"name" => "id",
					"feed_name" => "g:id",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"override" => array(
					"name" => "override",
					"feed_name" => "g:override",
					"format" => "optional",
				),
				"availability" => array(
					"name" => "availability",
					"feed_name" => "g:availability",
					"format" => "required",
					"woo_suggest" => "availability",
				),
				"condition" => array(
					"name" => "condition",
					"feed_name" => "g:condition",
					"format" => "required",
					"woo_suggest" => "condition",
				),
				"description" => array(
					"name" => "description",
					"feed_name" => "g:description",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"image_link" => array(
					"name" => "image_link",
					"feed_name" => "g:image_link",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"link" => array(
					"name" => "link",
					"feed_name" => "g:link",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"title" => array(
					"name" => "title",
					"feed_name" => "g:title",
					"format" => "required",
					"woo_suggest" => "mother_title",
				),
				"price" => array(
					"name" => "price",
					"feed_name" => "g:price",
					"format" => "required",
					"woo_suggest" => "regular_price",
				),
				"gtin" => array(
					"name" => "gtin",
					"feed_name" => "g:gtin",
					"format" => "optional",
				),
				"mpn" => array(
					"name" => "mpn",
					"feed_name" => "g:mpn",
					"format" => "optional",
				),
				"brand" => array(
					"name" => "brand",
					"feed_name" => "g:brand",
					"format" => "required",
				),
                                "identifier exists" => array(
                                        "name" => "identifier_exists",
                                        "feed_name" => "g:identifier_exists",
                                        "woo_suggest" => "calculated",
                                        "format" => "required",
                                ),
				"additional_image_link" => array(
					"name" => "additional_image_link",
					"feed_name" => "g:additional_image_link",
					"format" => "optional",
				),
				"age_group" => array(
					"name" => "age_group",
					"feed_name" => "g:age_group",
					"format" => "optional",
				),
				"color" => array(
					"name" => "color",
					"feed_name" => "g:color",
					"format" => "optional",
				),
				"expiration_date" => array(
					"name" => "expiration_date",
					"feed_name" => "g:expiration_date",
					"format" => "optional",
				),
				"gender" => array(
					"name" => "gender",
					"feed_name" => "g:gender",
					"format" => "optional",
				),
				"item_group_id" => array(
					"name" => "item_group_id",
					"feed_name" => "g:item_group_id",
					"format" => "required",
					"woo_suggest" => "item_group_id",
				),
				"google_product_category" => array(
					"name" => "google_product_category",
					"feed_name" => "g:google_product_category",
					"format" => "required",
                                        "woo_suggest" => "categories",
				),
				"product_type" => array(
					"name" => "product_type",
					"feed_name" => "g:product_type",
					"format" => "required",
                                        "woo_suggest" => "category_path",
				),
				"fb_product_category" => array(
					"name" => "fb_product_category",
					"feed_name" => "g:fb_product_category",
					"format" => "optional",
				),
			),
			"Enhanced fields" => array(
				"material" => array(
					"name" => "material",
					"feed_name" => "g:material",
					"format" => "optional",
				),
				"pattern" => array(
					"name" => "pattern",
					"feed_name" => "g:pattern",
					"format" => "optional",
				),
				"thread_count" => array(
					"name" => "thread_count",
					"feed_name" => "g:thread_count",
					"format" => "optional",
				),
				"capacity" => array(
					"name" => "capacity",
					"feed_name" => "g:capacity",
					"format" => "optional",
				),
				"style" => array(
					"name" => "style",
					"feed_name" => "g:style",
					"format" => "optional",
				),
				"decor_style" => array(
					"name" => "decor_style",
					"feed_name" => "g:decor_style",
					"format" => "optional",
				),
				"finish" => array(
					"name" => "finish",
					"feed_name" => "g:finish",
					"format" => "optional",
				),
				"is_assembly_required" => array(
					"name" => "is_assembly_required",
					"feed_name" => "g:is_assembly_required",
					"format" => "optional",
				),
				"product_height" => array(
					"name" => "product_height",
					"feed_name" => "g:product_height",
					"format" => "optional",
				),
				"product_length" => array(
					"name" => "product_length",
					"feed_name" => "g:product_length",
					"format" => "optional",
				),
				"product_width" => array(
					"name" => "product_width",
					"feed_name" => "g:product_width",
					"format" => "optional",
				),
				"product_depth" => array(
					"name" => "product_depth",
					"feed_name" => "g:product_depth",
					"format" => "optional",
				),
				"shoe_width" => array(
					"name" => "shoe_width",
					"feed_name" => "g:shoe_width",
					"format" => "optional",
				),
				"product_type" => array(
					"name" => "product_type",
					"feed_name" => "g:product_type",
					"format" => "optional",
					"woo_suggest" => "categories",
				),
				"offer_price" => array(
					"name" => "offer_price",
					"feed_name" => "g:offer_price",
					"format" => "optional",
					"woo_suggest" => "offer_price",
				),
				"offer_price_effective_date" => array(
					"name" => "offer_price_effective_date",
					"feed_name" => "g:offer_price_effective_date",
					"format" => "optional",
				),
				"sale_price" => array(
					"name" => "sale_price",
					"feed_name" => "g:sale_price",
					"format" => "optional",
					"woo_suggest" => "sale_price",
				),
				"sale_price_effective_date" => array(
					"name" => "sale_price_effective_date",
					"feed_name" => "g:sale_price_effective_date",
					"format" => "optional",
				),
				"shipping" => array(
					"name" => "shipping",
					"feed_name" => "g:shipping",
					"format" => "optional",
				),
				"country" => array(
					"name" => "country",
					"feed_name" => "g:country",
					"format" => "optional",
				),
				"shipping_weight" => array(
					"name" => "shipping_weight",
					"feed_name" => "g:shipping_weight",
					"format" => "optional",
				),
				"size" => array(
					"name" => "size",
					"feed_name" => "g:size",
					"format" => "optional",
				),
				"shipping_size" => array(
					"name" => "shipping_size",
					"feed_name" => "g:shipping_size",
					"format" => "optional",
				),
				"custom_label_0" => array(
					"name" => "custom_label_0",
					"feed_name" => "g:custom_label_0",
					"format" => "optional",
				),
				"custom_label_1" => array(
					"name" => "custom_label_1",
					"feed_name" => "g:custom_label_1",
					"format" => "optional",
				),
				"custom_label_2" => array(
					"name" => "custom_label_2",
					"feed_name" => "g:custom_label_2",
					"format" => "optional",
				),
				"custom_label_3" => array(
					"name" => "custom_label_3",
					"feed_name" => "g:custom_label_3",
					"format" => "optional",
				),
				"custom_label_4" => array(
					"name" => "custom_label_4",
					"feed_name" => "g:custom_label_4",
					"format" => "optional",
				),
				"inventory" => array(
					"name" => "inventory",
					"feed_name" => "g:inventory",
					"format" => "optional",
				),
                                "quantity_to_sell_on_facebook" => array(
                                        "name" => "quantity_to_sell_on_facebook",
                                        "feed_name" => "g:quantity_to_sell_on_facebook",
                                        "format" => "optional",
                                ),
				"ingredients" => array(
					"name" => "ingredients",
					"feed_name" => "g:ingredients",
					"format" => "optional",
				),
				"product_form" => array(
					"name" => "product_form",
					"feed_name" => "g:product_form",
					"format" => "optional",
				),
				"recommended_use" => array(
					"name" => "recommended_use",
					"feed_name" => "g:recommended_use",
					"format" => "optional",
				),
				"scent" => array(
					"name" => "scent",
					"feed_name" => "g:scent",
					"format" => "optional",
				),
				"gemstone" => array(
					"name" => "gemstone",
					"feed_name" => "g:gemstone",
					"format" => "optional",
				),
				"health_concern" => array(
					"name" => "health_concern",
					"feed_name" => "g:health_concern",
					"format" => "optional",
				),
				"model" => array(
					"name" => "model",
					"feed_name" => "g:model",
					"format" => "optional",
				),
				"operating_system" => array(
					"name" => "operating_system",
					"feed_name" => "g:operating_system",
					"format" => "optional",
				),
				"screen_size" => array(
					"name" => "screen_size",
					"feed_name" => "g:screen_size",
					"format" => "optional",
				),
				"storage_capacity" => array(
					"name" => "storage_capacity",
					"feed_name" => "g:storage_capacity",
					"format" => "optional",
				),
				"compatible_devices" => array(
					"name" => "compatible_devices",
					"feed_name" => "g:compatible_devices",
					"format" => "optional",
				),
				"video_game_platform" => array(
					"name" => "video_game_platform",
					"feed_name" => "g:video_game_platform",
					"format" => "optional",
				),
				"number_of_licenses" => array(
					"name" => "number_of_licenses",
					"feed_name" => "g:number_of_licenses",
					"format" => "optional",
				),
				"software_system_requirements" => array(
					"name" => "software_system_requirements",
					"feed_name" => "g:software_system_requirements",
					"format" => "optional",
				),
				"resolution" => array(
					"name" => "resolution",
					"feed_name" => "g:resolution",
					"format" => "optional",
				),
				"display_technology" => array(
					"name" => "display_technology",
					"feed_name" => "g:display_technology",
					"format" => "optional",
				),
				"digital_zoom" => array(
					"name" => "digital_zoom",
					"feed_name" => "g:digital_zoom",
					"format" => "optional",
				),
				"optical_zoom" => array(
					"name" => "optical_zoom",
					"feed_name" => "g:optical_zoom",
					"format" => "optional",
				),
				"megapixels" => array(
					"name" => "megapixels",
					"feed_name" => "g:megapixels",
					"format" => "optional",
				),
				"maximum_weight" => array(
					"name" => "maximum_weight",
					"feed_name" => "g:maximum_weight",
					"format" => "optional",
				),
				"minimum_weight" => array(
					"name" => "minimum_weight",
					"feed_name" => "g:minimum_weight",
					"format" => "optional",
				),
				"age_range" => array(
					"name" => "age_range",
					"feed_name" => "g:age_range",
					"format" => "optional",
				),
				"baby_food_stage" => array(
					"name" => "baby_food_stage",
					"feed_name" => "g:baby_food_stage",
					"format" => "optional",
				),
				"package_quantity" => array(
					"name" => "package_quantity",
					"feed_name" => "g:package_quantity",
					"format" => "optional",
				),
				"additional_features" => array(
					"name" => "additional_features",
					"feed_name" => "g:additional_features",
					"format" => "optional",
				),
				"bra_band_size" => array(
					"name" => "bra_band_size",
					"feed_name" => "g:bra_band_size",
					"format" => "optional",
				),
				"bra_cup_size" => array(
					"name" => "bra_cup_size",
					"feed_name" => "g:bra_cup_size",
					"format" => "optional",
				),
				"character" => array(
					"name" => "character",
					"feed_name" => "g:character",
					"format" => "optional",
				),
				"chest_size" => array(
					"name" => "chest_size",
					"feed_name" => "g:chest_size",
					"format" => "optional",
				),
				"closure" => array(
					"name" => "closure",
					"feed_name" => "g:closure",
					"format" => "optional",
				),
				"clothing_size_type" => array(
					"name" => "clothing_size_type",
					"feed_name" => "g:clothing_size_type",
					"format" => "optional",
				),
				"collar_style" => array(
					"name" => "collar_style",
					"feed_name" => "g:collar_style",
					"format" => "optional",
				),
				"denim_features" => array(
					"name" => "denim_features",
					"feed_name" => "g:denim_features",
					"format" => "optional",
				),
				"fabric_care_instructions" => array(
					"name" => "fabric_care_instructions",
					"feed_name" => "g:fabric_care_instructions",
					"format" => "optional",
				),
				"inseam" => array(
					"name" => "inseam",
					"feed_name" => "g:inseam",
					"format" => "optional",
				),
				"is_costume" => array(
					"name" => "is_costume",
					"feed_name" => "g:is_costume",
					"format" => "optional",
				),
				"is_outfit_set" => array(
					"name" => "is_outfit_set",
					"feed_name" => "g:is_outfit_set",
					"format" => "optional",
				),
				"jean_wash" => array(
					"name" => "jean_wash",
					"feed_name" => "g:jean_wash",
					"format" => "optional",
				),
				"neckline" => array(
					"name" => "neckline",
					"feed_name" => "g:neckline",
					"format" => "optional",
				),
				"occasion" => array(
					"name" => "occasion",
					"feed_name" => "g:occasion",
					"format" => "optional",
				),
				"pant_fit" => array(
					"name" => "pant_fit",
					"feed_name" => "g:pant_fit",
					"format" => "optional",
				),
				"sheerness" => array(
					"name" => "sheerness",
					"feed_name" => "g:sheerness",
					"format" => "optional",
				),
				"size_system" => array(
					"name" => "size_system",
					"feed_name" => "g:size_system",
					"format" => "optional",
				),
				"skirt_length" => array(
					"name" => "skirt_length",
					"feed_name" => "g:skirt_length",
					"format" => "optional",
				),
				"sleeve_length" => array(
					"name" => "sleeve_length",
					"feed_name" => "g:sleeve_length",
					"format" => "optional",
				),
				"sleeve_length_style" => array(
					"name" => "sleeve_length_style",
					"feed_name" => "g:sleeve_length_style",
					"format" => "optional",
				),
				"sleeve_style" => array(
					"name" => "sleeve_style",
					"feed_name" => "g:sleeve_style",
					"format" => "optional",
				),
				"sock_rise" => array(
					"name" => "sock_rise",
					"feed_name" => "g:sock_rise",
					"format" => "optional",
				),
				"sport" => array(
					"name" => "sport",
					"feed_name" => "g:sport",
					"format" => "optional",
				),
				"sports_league" => array(
					"name" => "sports_league",
					"feed_name" => "g:sports_league",
					"format" => "optional",
				),
				"sports_team" => array(
					"name" => "sports_team",
					"feed_name" => "g:sports_team",
					"format" => "optional",
				),
				"standard_features" => array(
					"name" => "standard_features",
					"feed_name" => "g:standard_features",
					"format" => "optional",
				),
				"theme" => array(
					"name" => "theme",
					"feed_name" => "g:theme",
					"format" => "optional",
				),
				"upper_body_strap_configuration" => array(
					"name" => "upper_body_strap_configuration",
					"feed_name" => "g:upper_body_strap_configuration",
					"format" => "optional",
				),
				"waist_rise" => array(
					"name" => "waist_rise",
					"feed_name" => "g:waist_rise",
					"format" => "optional",
				),
				"waist_style" => array(
					"name" => "waist_style",
					"feed_name" => "g:waist_style",
					"format" => "optional",
				),
				"heel_height" => array(
					"name" => "heel_height",
					"feed_name" => "g:heel_height",
					"format" => "optional",
				),
				"heel_style" => array(
					"name" => "heel_style",
					"feed_name" => "g:heel_style",
					"format" => "optional",
				),
				"occasion" => array(
					"name" => "occasion",
					"feed_name" => "g:occasion",
					"format" => "optional",
				),
				"shoe_type" => array(
					"name" => "shoe_type",
					"feed_name" => "g:shoe_type",
					"format" => "optional",
				),
				"shoe_system" => array(
					"name" => "shoe_system",
					"feed_name" => "g:shoe_system",
					"format" => "optional",
				),
				"sunglasses_lens_color" => array(
					"name" => "sunglasses_lens_color",
					"feed_name" => "g:sunglasses_lens_color",
					"format" => "optional",
				),
				"sunglasses_lens_technology" => array(
					"name" => "sunglasses_lens_technology",
					"feed_name" => "g:sunglasses_lens_technology",
					"format" => "optional",
				),
				"sunglasses_width" => array(
					"name" => "sunglasses_width",
					"feed_name" => "g:sunglasses_width",
					"format" => "optional",
				),
				"tie_width" => array(
					"name" => "tie_width",
					"feed_name" => "g:tie_width",
					"format" => "optional",
				),
				"is_powered" => array(
					"name" => "is_powered",
					"feed_name" => "g:is_powered",
					"format" => "optional",
				),
				"light_bulb_type" => array(
					"name" => "light_bulb_type",
					"feed_name" => "g:light_bulb_type",
					"format" => "optional",
				),
				"mount_type" => array(
					"name" => "mount_type",
					"feed_name" => "g:mount_type",
					"format" => "optional",
				),
				"number_of_lights" => array(
					"name" => "number_of_lights",
					"feed_name" => "g:number_of_lights",
					"format" => "optional",
				),
				"power_type" => array(
					"name" => "power_type",
					"feed_name" => "g:power_type",
					"format" => "optional",
				),
				"product_weight" => array(
					"name" => "product_weight",
					"feed_name" => "g:product_weight",
					"format" => "optional",
				),
				"recommended_rooms" => array(
					"name" => "recommended_rooms",
					"feed_name" => "g:recommended_rooms",
					"format" => "optional",
				),
				"shape" => array(
					"name" => "shape",
					"feed_name" => "g:shape",
					"format" => "optional",
				),
				"bed_frame_type" => array(
					"name" => "bed_frame_type",
					"feed_name" => "g:bed_frame_type",
					"format" => "optional",
				),
				"comfort_level" => array(
					"name" => "comfort_level",
					"feed_name" => "g:comfort_level",
					"format" => "optional",
				),
				"fill_material" => array(
					"name" => "fill_material",
					"feed_name" => "g:fill_material",
					"format" => "optional",
				),
				"indoor_outdoor" => array(
					"name" => "indoor_outdoor",
					"feed_name" => "g:indoor_outdoor",
					"format" => "optional",
				),
				"mattress_thickness" => array(
					"name" => "mattress_thickness",
					"feed_name" => "g:mattress_thickness",
					"format" => "optional",
				),
				"mount_type" => array(
					"name" => "mount_type",
					"feed_name" => "g:mount_type",
					"format" => "optional",
				),
				"number_of_drawers" => array(
					"name" => "number_of_drawers",
					"feed_name" => "g:number_of_drawers",
					"format" => "optional",
				),
				"number_of_seats" => array(
					"name" => "number_of_seats",
					"feed_name" => "g:number_of_seats",
					"format" => "optional",
				),
				"number_of_shelves" => array(
					"name" => "number_of_shelves",
					"feed_name" => "g:number_of_shelves",
					"format" => "optional",
				),
				"recommended_rooms" => array(
					"name" => "recommended_rooms",
					"feed_name" => "g:recommended_rooms",
					"format" => "optional",
				),
				"seat_back_height" => array(
					"name" => "seat_back_height",
					"feed_name" => "g:seat_back_height",
					"format" => "optional",
				),
				"seat_height" => array(
					"name" => "seat_height",
					"feed_name" => "g:seat_height",
					"format" => "optional",
				),
				"seat_material" => array(
					"name" => "seat_material",
					"feed_name" => "g:seat_material",
					"format" => "optional",
				),
				"is_set" => array(
					"name" => "is_set",
					"feed_name" => "g:is_set",
					"format" => "optional",
				),
				"pieces_in_set" => array(
					"name" => "pieces_in_set",
					"feed_name" => "g:pieces_in_set",
					"format" => "optional",
				),
				"btu" => array(
					"name" => "btu",
					"feed_name" => "g:btu",
					"format" => "optional",
				),
				"fuel_type" => array(
					"name" => "fuel_type",
					"feed_name" => "g:fuel_type",
					"format" => "optional",
				),
				"load_position" => array(
					"name" => "load_position",
					"feed_name" => "g:load_position",
					"format" => "optional",
				),
				"smart_home_compatibility" => array(
					"name" => "smart_home_compatibility",
					"feed_name" => "g:smart_home_compatibility",
					"format" => "optional",
				),
				"sound_rating" => array(
					"name" => "sound_rating",
					"feed_name" => "g:sound_rating",
					"format" => "optional",
				),
				"volts" => array(
					"name" => "sound_rating",
					"feed_name" => "g:sound_rating",
					"format" => "optional",
				),
				"watts" => array(
					"name" => "watts",
					"feed_name" => "g:watts",
					"format" => "optional",
				),
				"bag_type" => array(
					"name" => "bag_type",
					"feed_name" => "g:bag_type",
					"format" => "optional",
				),
				"instructions" => array(
					"name" => "instructions",
					"feed_name" => "g:instructions",
					"format" => "optional",
				),
				"shelf_life" => array(
					"name" => "shelf_life",
					"feed_name" => "g:shelf_life",
					"format" => "optional",
				),
				"vacuum_type" => array(
					"name" => "vacuum_type",
					"feed_name" => "g:vacuum_type",
					"format" => "optional",
				),
				"warnings" => array(
					"name" => "warnings",
					"feed_name" => "g:warnings",
					"format" => "optional",
				),
				"chain_length" => array(
					"name" => "chain_length",
					"feed_name" => "g:chain_length",
					"format" => "optional",
				),
				"clasp_type" => array(
					"name" => "clasp_type",
					"feed_name" => "g:clasp_type",
					"format" => "optional",
				),
				"earring_back_finding" => array(
					"name" => "earring_back_finding",
					"feed_name" => "g:earring_back_finding",
					"format" => "optional",
				),
				"gemstone_clarity" => array(
					"name" => "gemstone_clarity",
					"feed_name" => "g:gemstone_clarity",
					"format" => "optional",
				),
				"gemstone_color" => array(
					"name" => "gemstone_color",
					"feed_name" => "g:gemstone_color",
					"format" => "optional",
				),
				"gemstone_creation_method" => array(
					"name" => "gemstone_creation_method",
					"feed_name" => "g:gemstone_creation_method",
					"format" => "optional",
				),
				"gemstone_cut" => array(
					"name" => "gemstone_cut",
					"feed_name" => "g:gemstone_cut",
					"format" => "optional",
				),
				"gemstone_height" => array(
					"name" => "gemstone_height",
					"feed_name" => "g:gemstone_height",
					"format" => "optional",
				),
				"gemstone_length" => array(
					"name" => "gemstone_length",
					"feed_name" => "g:gemstone_length",
					"format" => "optional",
				),
				"gemstone_treatment" => array(
					"name" => "gemstone_treatment",
					"feed_name" => "g:gemstone_treatment",
					"format" => "optional",
				),
				"gemstone_weight" => array(
					"name" => "gemstone_weight",
					"feed_name" => "g:gemstone_weight",
					"format" => "optional",
				),
				"gemstone_width" => array(
					"name" => "gemstone_width",
					"feed_name" => "g:gemstone_width",
					"format" => "optional",
				),
				"inscription" => array(
					"name" => "inscription",
					"feed_name" => "g:inscription",
					"format" => "optional",
				),
				"jewelry_setting_style" => array(
					"name" => "jewelry_setting_style",
					"feed_name" => "g:jewelry_setting_style",
					"format" => "optional",
				),
				"metal_stamp_or_purity" => array(
					"name" => "metal_stamp_or_purity",
					"feed_name" => "g:metal_stamp_or_purity",
					"format" => "optional",
				),
				"plating_material" => array(
					"name" => "plating_material",
					"feed_name" => "g:plating_material",
					"format" => "optional",
				),
				"total_gemstone_weight" => array(
					"name" => "total_gemstone_weight",
					"feed_name" => "g:total_gemstone_weight",
					"format" => "optional",
				),
				"activity" => array(
					"name" => "activity",
					"feed_name" => "g:activity",
					"format" => "optional",
				),
				"battery_life" => array(
					"name" => "battery_life",
					"feed_name" => "g:battery_life",
					"format" => "optional",
				),
				"watch_style" => array(
					"name" => "watch_style",
					"feed_name" => "g:watch_style",
					"format" => "optional",
				),
				"watch_band_length" => array(
					"name" => "watch_band_length",
					"feed_name" => "g:watch_band_length",
					"format" => "optional",
				),
				"watch_band_material" => array(
					"name" => "watch_band_material",
					"feed_name" => "g:watch_band_material",
					"format" => "optional",
				),
				"watch_band_width" => array(
					"name" => "watch_band_width",
					"feed_name" => "g:watch_band_width",
					"format" => "optional",
				),
				"watch_band_diameter" => array(
					"name" => "watch_band_diameter",
					"feed_name" => "g:watch_band_diameter",
					"format" => "optional",
				),
				"watch_case_thickness" => array(
					"name" => "watch_case_thickness",
					"feed_name" => "g:watch_case_thickness",
					"format" => "optional",
				),
				"watch_case_shape" => array(
					"name" => "watch_case_shape",
					"feed_name" => "g:watch_case_shape",
					"format" => "optional",
				),
				"watch_movement_type" => array(
					"name" => "watch_movement_type",
					"feed_name" => "g:watch_movement_type",
					"format" => "optional",
				),
				"absorbency" => array(
					"name" => "absorbency",
					"feed_name" => "g:absorbency",
					"format" => "optional",
				),
				"batteries_required" => array(
					"name" => "batteries_required",
					"feed_name" => "g:batteries_required",
					"format" => "optional",
				),
				"body_part" => array(
					"name" => "body_part",
					"feed_name" => "g:body_part",
					"format" => "optional",
				),
				"dosage" => array(
					"name" => "dosage",
					"feed_name" => "g:dosage",
					"format" => "optional",
				),
				"eyewear_rim" => array(
					"name" => "eyewear_rim",
					"feed_name" => "g:eyewear_rim",
					"format" => "optional",
				),
				"flavor" => array(
					"name" => "flavor",
					"feed_name" => "g:flavor",
					"format" => "optional",
				),
				"inactive_ingredients" => array(
					"name" => "inactive_ingredients",
					"feed_name" => "g:inactive_ingredients",
					"format" => "optional",
				),
				"ingredient_composition" => array(
					"name" => "ingredient_composition",
					"feed_name" => "g:ingredient_composition",
					"format" => "optional",
				),
				"keywords" => array(
					"name" => "keywords",
					"feed_name" => "g:keywords",
					"format" => "optional",
				),
				"lens_material" => array(
					"name" => "lens_material",
					"feed_name" => "g:lens_material",
					"format" => "optional",
				),
				"lens_tint" => array(
					"name" => "lens_tint",
					"feed_name" => "g:lens_tint",
					"format" => "optional",
				),
				"lens_type" => array(
					"name" => "lens_type",
					"feed_name" => "g:lens_type",
					"format" => "optional",
				),
				"nutrient_amount" => array(
					"name" => "nutrient_amount",
					"feed_name" => "g:nutrient_amount",
					"format" => "optional",
				),
				"nutrient_name" => array(
					"name" => "nutrient_name",
					"feed_name" => "g:nutrient_name",
					"format" => "optional",
				),
				"nutrient_percentage_daily_value" => array(
					"name" => "nutrient_percentage_daily_value",
					"feed_name" => "g:nutrient_percentage_daily_value",
					"format" => "optional",
				),
				"power_type" => array(
					"name" => "power_type",
					"feed_name" => "g:power_type",
					"format" => "optional",
				),
				"product_form" => array(
					"name" => "product_form",
					"feed_name" => "g:product_form",
					"format" => "optional",
				),
				"result_time" => array(
					"name" => "result_time",
					"feed_name" => "g:result_time",
					"format" => "optional",
				),
				"serving_size" => array(
					"name" => "serving_size",
					"feed_name" => "g:serving_size",
					"format" => "optional",
				),
				"skin_care_concerns" => array(
					"name" => "skin_care_concerns",
					"feed_name" => "g:skin_care_concerns",
					"format" => "optional",
				),
				"skin_type" => array(
					"name" => "skin_type",
					"feed_name" => "g:skin_type",
					"format" => "optional",
				),
				"skin_tone" => array(
					"name" => "skin_tone",
					"feed_name" => "g:skin_tone",
					"format" => "optional",
				),
				"spf_value" => array(
					"name" => "spf_value",
					"feed_name" => "g:spf_value",
					"format" => "optional",
				),
				"stop_use_indications" => array(
					"name" => "stop_use_indications",
					"feed_name" => "g:stop_use_indications",
					"format" => "optional",
				),
				"uv_rating" => array(
					"name" => "uv_rating",
					"feed_name" => "g:uv_rating",
					"format" => "optional",
				),
				"wig_cap_type" => array(
					"name" => "wig_cap_type",
					"feed_name" => "g:wig_cap_type",
					"format" => "optional",
				),
				"bluetooth_technology" => array(
					"name" => "bluetooth_technology",
					"feed_name" => "g:bluetooth_technology",
					"format" => "optional",
				),
				"cell_phone_service_provider" => array(
					"name" => "cell_phone_service_provider",
					"feed_name" => "g:cell_phone_service_provider",
					"format" => "optional",
				),
				"cellular_band" => array(
					"name" => "cellular_band",
					"feed_name" => "g:cellular_band",
					"format" => "optional",
				),
				"cellular_generation" => array(
					"name" => "cellular_generation",
					"feed_name" => "g:cellular_generation",
					"format" => "optional",
				),
				"connector_type" => array(
					"name" => "connector_type",
					"feed_name" => "g:connector_type",
					"format" => "optional",
				),
				"front_facing_camera_megapixel" => array(
					"name" => "front_facing_camera_megapixel",
					"feed_name" => "g:front_facing_camera_megapixel",
					"format" => "optional",
				),
				"rear_facing_camera_megapixel" => array(
					"name" => "rear_facing_camera_megapixel",
					"feed_name" => "g:rear_facing_camera_megapixel",
					"format" => "optional",
				),
				"number_of_sim_card_slots" => array(
					"name" => "number_of_sim_card_slots",
					"feed_name" => "g:number_of_sim_card_slots",
					"format" => "optional",
				),
				"resolution" => array(
					"name" => "resolution",
					"feed_name" => "g:resolution",
					"format" => "optional",
				),
				"sim_type_supported" => array(
					"name" => "sim_type_supported",
					"feed_name" => "g:sim_type_supported",
					"format" => "optional",
				),
				"stand-by_time" => array(
					"name" => "stand-by_time",
					"feed_name" => "g:stand-by_time",
					"format" => "optional",
				),
				"talk_time" => array(
					"name" => "talk_time",
					"feed_name" => "g:talk_time",
					"format" => "optional",
				),
				"usb_technology" => array(
					"name" => "usb_technology",
					"feed_name" => "g:usb_technology",
					"format" => "optional",
				),
				"usb_type" => array(
					"name" => "usb_type",
					"feed_name" => "g:usb_type",
					"format" => "optional",
				),
				"video_resolution" => array(
					"name" => "video_resolution",
					"feed_name" => "g:video_resolution",
					"format" => "optional",
				),
				"headphone_features" => array(
					"name" => "headphone_features",
					"feed_name" => "g:headphone_features",
					"format" => "optional",
				),
				"maximum_load_weight" => array(
					"name" => "maximum_load_weight",
					"feed_name" => "g:maximum_load_weight",
					"format" => "optional",
				),
				"maximum_screen_size" => array(
					"name" => "maximum_screen_size",
					"feed_name" => "g:maximum_screen_size",
					"format" => "optional",
				),
				"minimum_screen_size" => array(
					"name" => "minimum_screen_size",
					"feed_name" => "g:minimum_screen_size",
					"format" => "optional",
				),
				"wireless_technologies" => array(
					"name" => "wireless_technologies",
					"feed_name" => "g:wireless_technologies",
					"format" => "optional",
				),
				"computer_case_form_factor" => array(
					"name" => "computer_case_form_factor",
					"feed_name" => "g:computer_case_form_factor",
					"format" => "optional",
				),
				"cpu_socket_type" => array(
					"name" => "cpu_socket_type",
					"feed_name" => "g:cpu_socket_type",
					"format" => "optional",
				),
				"graphics_card_model" => array(
					"name" => "graphics_card_model",
					"feed_name" => "g:graphics_card_model",
					"format" => "optional",
				),
				"hard_drive_type" => array(
					"name" => "hard_drive_type",
					"feed_name" => "g:hard_drive_type",
					"format" => "optional",
				),
				"maximum_supported_ram" => array(
					"name" => "maximum_supported_ram",
					"feed_name" => "g:maximum_supported_ram",
					"format" => "optional",
				),
				"motherboard_form_factor" => array(
					"name" => "motherboard_form_factor",
					"feed_name" => "g:motherboard_form_factor",
					"format" => "optional",
				),
				"optical_drive" => array(
					"name" => "optical_drive",
					"feed_name" => "g:optical_drive",
					"format" => "optional",
				),
				"processor_speed" => array(
					"name" => "processor_speed",
					"feed_name" => "g:processor_speed",
					"format" => "optional",
				),
				"processor_type" => array(
					"name" => "processor_type",
					"feed_name" => "g:processor_type",
					"format" => "optional",
				),
				"raw_memory" => array(
					"name" => "raw_memory",
					"feed_name" => "g:raw_memory",
					"format" => "optional",
				),
				"physical_media_format" => array(
					"name" => "physical_media_format",
					"feed_name" => "g:physical_media_format",
					"format" => "optional",
				),
				"release_date" => array(
					"name" => "release_date",
					"feed_name" => "g:release_date",
					"format" => "optional",
				),
				"required_peripherals" => array(
					"name" => "required_peripherals",
					"feed_name" => "g:required_peripherals",
					"format" => "optional",
				),
				"video_game_genre" => array(
					"name" => "video_game_genre",
					"feed_name" => "g:video_game_genre",
					"format" => "optional",
				),
				"video_game_rating" => array(
					"name" => "video_game_rating",
					"feed_name" => "g:video_game_rating",
					"format" => "optional",
				),
				"video_game_series" => array(
					"name" => "video_game_series",
					"feed_name" => "g:video_game_series",
					"format" => "optional",
				),
				"educational_focus" => array(
					"name" => "educational_focus",
					"feed_name" => "g:educational_focus",
					"format" => "optional",
				),
				"physical_media_format" => array(
					"name" => "physical_media_format",
					"feed_name" => "g:physical_media_format",
					"format" => "optional",
				),
				"software_category" => array(
					"name" => "software_category",
					"feed_name" => "g:software_category",
					"format" => "optional",
				),
				"software_version" => array(
					"name" => "software_version",
					"feed_name" => "g:software_version",
					"format" => "optional",
				),
				"color_pages_per_minute" => array(
					"name" => "color_pages_per_minute",
					"feed_name" => "g:color_pages_per_minute",
					"format" => "optional",
				),
				"monochrome_pages_per_minute" => array(
					"name" => "monochrome_pages_per_minute",
					"feed_name" => "g:monochrome_pages_per_minute",
					"format" => "optional",
				),
				"monochrome_color" => array(
					"name" => "monochrome_color",
					"feed_name" => "g:monochrome_color",
					"format" => "optional",
				),
				"aspect_ratio" => array(
					"name" => "aspect_ratio",
					"feed_name" => "g:aspect_ratio",
					"format" => "optional",
				),
				"audio_features" => array(
					"name" => "audio_features",
					"feed_name" => "g:audio_features",
					"format" => "optional",
				),
				"audio_power_output" => array(
					"name" => "audio_power_output",
					"feed_name" => "g:audio_powe_output",
					"format" => "optional",
				),
				"backlight_technology" => array(
					"name" => "backlight_technology",
					"feed_name" => "g:backlight_technology",
					"format" => "optional",
				),
				"maximum_contrast_ratio" => array(
					"name" => "maximum_contrast_ratio",
					"feed_name" => "g:maximum_contrast_ratio",
					"format" => "optional",
				),
				"number_of_hdmi_ports" => array(
					"name" => "number_of_hdmi_ports",
					"feed_name" => "g:number_of_hdmi_ports",
					"format" => "optional",
				),
				"refresh_rate" => array(
					"name" => "refresh_rate",
					"feed_name" => "g:refresh_rate",
					"format" => "optional",
				),
				"response_time" => array(
					"name" => "response_time",
					"feed_name" => "g:refresh_rate",
					"format" => "optional",
				),
				"vesa_mounting_standard" => array(
					"name" => "vesa_mounting_standard",
					"feed_name" => "g:vesa_mounting_standard",
					"format" => "optional",
				),
				"brightness" => array(
					"name" => "brightness",
					"feed_name" => "g:brightness",
					"format" => "optional",
				),
				"lamp_life" => array(
					"name" => "lamp_life",
					"feed_name" => "g:lamp_life",
					"format" => "optional",
				),
				"throw_ratio" => array(
					"name" => "throw_ratio",
					"feed_name" => "g:throw_ratio",
					"format" => "optional",
				),
				"flash_type" => array(
					"name" => "flash_type",
					"feed_name" => "g:flash_type",
					"format" => "optional",
				),
				"focal_length" => array(
					"name" => "focal_length",
					"feed_name" => "g:focal_length",
					"format" => "optional",
				),
				"focal_ratio" => array(
					"name" => "focal_ratio",
					"feed_name" => "g:focal_ratio",
					"format" => "optional",
				),
				"lens_coating" => array(
					"name" => "lens_coating",
					"feed_name" => "g:lens_coating",
					"format" => "optional",
				),
				"lens_diameter" => array(
					"name" => "lens_diameter",
					"feed_name" => "g:lens_diameter",
					"format" => "optional",
				),
				"lens_filter" => array(
					"name" => "lens_filter",
					"feed_name" => "g:lens_filter",
					"format" => "optional",
				),
				"maximum_aperture" => array(
					"name" => "maximum_aperture",
					"feed_name" => "g:maximum_aperture",
					"format" => "optional",
				),
				"minimum_aperture" => array(
					"name" => "minimum_aperture",
					"feed_name" => "g:minimum_aperture",
					"format" => "optional",
				),
				"maximum_shutter_speed" => array(
					"name" => "maximum_shutter_speed",
					"feed_name" => "g:maximum_shutter_speed",
					"format" => "optional",
				),
				"minimum_shutter_speed" => array(
					"name" => "minimum_shutter_speed",
					"feed_name" => "g:minimum_shutter_speed",
					"format" => "optional",
				),
				"self-timer_delay" => array(
					"name" => "self-timer_delay",
					"feed_name" => "g:self-timer_delay",
					"format" => "optional",
				),
				"sensor_resolution" => array(
					"name" => "sensor_resolution",
					"feed_name" => "g:sensor_resolution",
					"format" => "optional",
				),
				"shooting_modes" => array(
					"name" => "shooting_modes",
					"feed_name" => "g:shooting_modes",
					"format" => "optional",
				),
				"allergens" => array(
					"name" => "allergens",
					"feed_name" => "g:allergens",
					"format" => "optional",
				),
				"life_stage" => array(
					"name" => "life_stage",
					"feed_name" => "g:life_stage",
					"format" => "optional",
				),
				"baby_carrier_position" => array(
					"name" => "baby_carrier_position",
					"feed_name" => "g:baby_carrier_position",
					"format" => "optional",
				),
				"baby_carrier_style" => array(
					"name" => "baby_carrier_style",
					"feed_name" => "g:baby_carrier_style",
					"format" => "optional",
				),
				"car_seat_facing_direction" => array(
					"name" => "car_seat_facing_directory",
					"feed_name" => "g:car_seat_facing_directory",
					"format" => "optional",
				),
				"car_seat_max_child_height" => array(
					"name" => "car_seat_max_child_height",
					"feed_name" => "g:car_seat_max_child_height",
					"format" => "optional",
				),
				"child_car_seat_style" => array(
					"name" => "child_car_set_style",
					"feed_name" => "g:child_car_seat_style",
					"format" => "optional",
				),
				"safety_harness_style" => array(
					"name" => "safety_harness_style",
					"feed_name" => "g:safety_harness_style",
					"format" => "optional",
				),
				"stroller_type" => array(
					"name" => "stroller_type",
					"feed_name" => "g:stroller_type",
					"format" => "optional",
				),
				"diaper_type" => array(
					"name" => "diaper_type",
					"feed_name" => "g:diaper_type",
					"format" => "optional",
				),
				"commerce_tax_category" => array(
					"name" => "commerce_tax_category",
					"feed_name" => "g:commerce_tax_category",
					"format" => "optional",
				),
				"rich_text_description" => array(
					"name" => "rich_text_description",
					"feed_name" => "g:rich_text_description",
					"format" => "optional",
				),
				"return_policy_info" => array(
					"name" => "return_policy_info",
					"feed_name" => "g:return_policy_info",
					"format" => "optional",
				),
				"launch_date" => array(
					"name" => "launch_date",
					"feed_name" => "g:launch_date",
					"format" => "optional",
				),
				"visibility" => array(
					"name" => "visibility",
					"feed_name" => "g:visibility",
					"format" => "optional",
				),
				"mobile_link" => array(
					"name" => "mobile_link",
					"feed_name" => "g:mobile_link",
					"format" => "optional",
				),
				"additional_variant_attribute" => array(
					"name" => "additional_variant_attribute",
					"feed_name" => "g:additional_variant_attribute",
					"format" => "optional",
				),
			),
		);
		return $facebook_drm;
	}
}
?>
