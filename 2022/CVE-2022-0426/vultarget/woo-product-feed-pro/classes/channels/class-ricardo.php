<?php
/**
 * Settings for Ricardo feeds
 */
class WooSEA_ricardo {
	public $ricardo;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$ricardo = array(
			"Feed fields" => array(
				"StartPrice" => array(
					"name" => "StartPrice",
					"feed_name" => "StartPrice",
					"format" => "required",
				),
				"BuyNowPrice" => array(
					"name" => "BuyNowPrice",
					"feed_name" => "BuyNowPrice",
					"format" => "required",
					"woo_suggest" => "price"
				),
				"AvailibilityId" => array(
					"name" => "AvailabilityId",
					"feed_name" => "AvailabilityId",
					"format" => "required",
				),
				"Duration" => array(
					"name" => "Duration",
					"feed_name" => "Duration",
					"format" => "required",
				),
				"FeaturedHomePage" => array(
					"name" => "FeaturedHomepage",
					"feed_name" => "FeaturedHomepage",
					"format" => "required",
				),
				"Shipping" => array(
					"name" => "Shipping",
					"feed_name" => "Shipping",
					"format" => "required",
				),
				"Warranty" => array(
					"name" => "Warranty",
					"feed_name" => "Warranty",
					"format" => "required",
				),
				"Quantity" => array(
					"name" => "Quantity",
					"feed_name" => "Quantity",
					"format" => "required",
				),
				"Increment" => array(
					"name" => "Increment",
					"feed_name" => "Increment",
					"format" => "required",
				),
				"CategoryNr" => array(
					"name" => "CategoryNr",
					"feed_name" => "CategoryNr",
					"format" => "required",
				),
				"Condition" => array(
					"name" => "Condition",
					"feed_name" => "Condition",
					"format" => "required",
				),
				"ShippingCost" => array(
					"name" => "ShippingCost",
					"feed_name" => "ShippingCost",
					"format" => "required",
				),
				"ResellCount" => array(
					"name" => "ResellCount",
					"feed_name" => "ResellCount",
					"format" => "required",
				),
				"BuyNow" => array(
					"name" => "BuyNow",
					"feed_name" => "BuyNow",
					"format" => "required",
				),
				"BuyNowCost" => array(
					"name" => "BuyNowCost",
					"feed_name" => "BuyNowCost",
					"format" => "required",
				),
				"TemplateName" => array(
					"name" => "TemplateName",
					"feed_name" => "TemplateName",
					"format" => "required",
				),
				"StartDate" => array(
					"name" => "StartDate",
					"feed_name" => "StartDate",
					"format" => "required",
				),
				"StartImmediatly" => array(
					"name" => "StartImmediatly",
					"feed_name" => "StartImmediatly",
					"format" => "required",
				),
				"EndDate" => array(
					"name" => "EndDate",
					"feed_name" => "EndDate",
					"format" => "required",
				),
				"HasFixedEndDate" => array(
					"name" => "HasFixedEndDate",
					"feed_name" => "HasFixedEndDate",
					"format" => "required",
				),
        			"InternalReference" => array(
					"name" => "InternalReference",
					"feed_name" => "InternalReference",
					"format" => "required",
				),
                                "TemplateId" => array(
                                        "name" => "TemplateId",
                                        "feed_name" => "TemplateId",
                                        "format" => "required",
                                ),
                                "PackageSizeId" => array(
                                        "name" => "PackageSizeId",
                                        "feed_name" => "PackageSizeId",
                                        "format" => "required",
                                ),
                                "PromotionId" => array(
                                        "name" => "PromotionId",
                                        "feed_name" => "PromotionId",
                                        "format" => "required",
                                ),
                                "IsCarsBikesAccessoriesArticle" => array(
                                        "name" => "IsCarsBikesAccessoriesArticle",
                                        "feed_name" => "IsCarsBikesAccessoriesArticle",
                                        "format" => "required",
                                ),
                                "Descriptions[0].LanguageNr" => array(
                                        "name" => "Descriptions[0].LanguageNr",
                                        "feed_name" => "Descriptions[0].LanguageNr",
                                        "format" => "required",
                                ),
                                "Descriptions[0].ProductTitle" => array(
                                        "name" => "Descriptions[0].ProductTitle",
                                        "feed_name" => "Descriptions[0].ProductTitle",
                                        "format" => "required",
                                ),
                                "Descriptions[0].ProductDescription" => array(
                                        "name" => "Descriptions[0].ProductDescription",
                                        "feed_name" => "Descriptions[0].ProductDescription",
                                        "format" => "required",
                                ),
                                "Descriptions[0].ProductSubtitle" => array(
                                        "name" => "Descriptions[0].ProductSubtitle",
                                        "feed_name" => "Descriptions[0].ProductSubtitle",
                                        "format" => "required",
                                ),
                                "Descriptions[0].PaymentDescription" => array(
                                        "name" => "Descriptions[0].PaymentDescription",
                                        "feed_name" => "Descriptions[0].PaymentDescription",
                                        "format" => "required",
                                ),
                                "Descriptions[0].ShippingDescription" => array(
                                        "name" => "Descriptions[0].ShippingDescription",
                                        "feed_name" => "Descriptions[0].ShippingDescription",
                                        "format" => "required",
                                ),
                                "Descriptions[0].WarrantyDescription" => array(
                                        "name" => "Descriptions[0].WarrantyDescription",
                                        "feed_name" => "Descriptions[0].WarrantyDescription",
                                        "format" => "required",
                                ),
                                "Descriptions[1].ProductTitle" => array(
                                        "name" => "Descriptions[1].ProductTitle",
                                        "feed_name" => "Descriptions[1].ProductTitle",
                                        "format" => "required",
                                ),
                                "Descriptions[1].ProductDescription" => array(
                                        "name" => "Descriptions[1].ProductDescription",
                                        "feed_name" => "Descriptions[1].ProductDescription",
                                        "format" => "required",
                                ),
                                "Descriptions[1].ProductSubtitle" => array(
                                        "name" => "Descriptions[1].ProductSubtitle",
                                        "feed_name" => "Descriptions[1].ProductSubtitle",
                                        "format" => "required",
                                ),
                                "Descriptions[1].PaymentDescription" => array(
                                        "name" => "Descriptions[1].PaymentDescription",
                                        "feed_name" => "Descriptions[1].PaymentDescription",
                                        "format" => "required",
                                ),
                                "Descriptions[1].ShippingDescription" => array(
                                        "name" => "Descriptions[1].ShippingDescription",
                                        "feed_name" => "Descriptions[1].ShippingDescription",
                                        "format" => "required",
                                ),
                                "Descriptions[1].WarrantyDescription" => array(
                                        "name" => "Descriptions[1].WarrantyDescription",
                                        "feed_name" => "Descriptions[1].WarrantyDescription",
                                        "format" => "required",
                                ),
                                "DraftImages[0]" => array(
                                        "name" => "DraftImages[0]",
                                        "feed_name" => "DraftImages[0]",
                                        "format" => "required",
                                ),
                                "DraftImages[1]" => array(
                                        "name" => "DraftImages[1]",
                                        "feed_name" => "DraftImages[1]",
                                        "format" => "required",
                                ),
                                "DraftImages[2]" => array(
                                        "name" => "DraftImages[2]",
                                        "feed_name" => "DraftImages[2]",
                                        "format" => "required",
                                ),
                                "DraftImages[3]" => array(
                                        "name" => "DraftImages[3]",
                                        "feed_name" => "DraftImages[3]",
                                        "format" => "required",
                                ),
                                "DraftImages[4]" => array(
                                        "name" => "DraftImages[4]",
                                        "feed_name" => "DraftImages[4]",
                                        "format" => "required",
                                ),
                                "DraftImages[5]" => array(
                                        "name" => "DraftImages[5]",
                                        "feed_name" => "DraftImages[5]",
                                        "format" => "required",
                                ),
                                "DraftImages[6]" => array(
                                        "name" => "DraftImages[6]",
                                        "feed_name" => "DraftImages[6]",
                                        "format" => "required",
                                ),
                                "DraftImages[7]" => array(
                                        "name" => "DraftImages[7]",
                                        "feed_name" => "DraftImages[7]",
                                        "format" => "required",
                                ),
                                "DraftImages[8]" => array(
                                        "name" => "DraftImages[8]",
                                        "feed_name" => "DraftImages[8]",
                                        "format" => "required",
                                ),
                                "DraftImages[9]" => array(
                                        "name" => "DraftImages[9]",
                                        "feed_name" => "DraftImages[9]",
                                        "format" => "required",
                                ),
                                "IsFixedPrice" => array(
                                        "name" => "IsFixedPrice",
                                        "feed_name" => "IsFixedPrice",
                                        "format" => "required",
                                ),
                                "PaymentCode" => array(
                                        "name" => "PaymentCode",
                                        "feed_name" => "PaymentCode",
                                        "format" => "required",
                                ),
                                "IsCumulativeShipping" => array(
                                        "name" => "IsCumulativeShipping",
                                        "feed_name" => "IsCumulativeShipping",
                                        "format" => "required",
                                ),
			),
		);
		return $ricardo;
	}
}
?>
