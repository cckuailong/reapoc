<?php
/**
 * Settings for Idealo feeds
 */
class WooSEA_idealo {
	public $idealo;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$idealo = array(
			"Feed fields" => array(
				"SKU" => array(
					"name" => "SKU",
					"feed_name" => "sku",
					"format" => "required",
					"woo_suggest" => "sku",
				),
				"Brand" => array(
					"name" => "brand",
					"feed_name" => "brand",
					"format" => "required",
				),
				"Title" => array(
					"name" => "title",
					"feed_name" => "title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"CategoryPath" => array(
					"name" => "categoryPath",
					"feed_name" => "categoryPath",
					"format" => "required",
					"woo_suggest" => "category_path",
				),
				"url" => array(
					"name" => "url",
					"feed_name" => "url",
					"format" => "required",
					"woo_suggest" => "link",
				),
				"hans" => array(
					"name" => "hans",
					"feed_name" => "hans",
					"format" => "optional",
				),
                                "Description" => array(
                                        "name" => "description",
                                        "feed_name" => "description",
                                        "format" => "required",
					"woo_suggest" => "description",
                                ),
        			"ImageUrls" => array(
					"name" => "imageUrls",
					"feed_name" => "imageUrls",
					"format" => "optional",
					"woo_suggest" => "image",
				),
                                "eec" => array(
                                        "name" => "eec",
                                        "feed_name" => "eec",
                                        "format" => "optional",
				),
                                "merchantName" => array(
                                        "name" => "merchantName",
                                        "feed_name" => "merchantName",
                                        "format" => "optional",
                                ),
                                "merchantId" => array(
                                        "name" => "merchantId",
                                        "feed_name" => "merchanId",
                                        "format" => "optional",
                                ),
                                "price" => array(
                                        "name" => "price",
                                        "feed_name" => "price",
					"format" => "required",
					"woo_suggest" => "price",
				),
                                "basePrice" => array(
                                        "name" => "basePrice",
                                        "feed_name" => "basePrice",
					"format" => "optional",
				),
                                "formerPrice" => array(
                                        "name" => "formerPrice",
                                        "feed_name" => "formerPrice",
					"format" => "optional",
				),
                                "voucherCode" => array(
                                        "name" => "voucherCode",
                                        "feed_name" => "voucherCode",
					"format" => "optional",
				),
                         	"deposit" => array(
                                        "name" => "deposit",
                                        "feed_name" => "deposit",
					"format" => "optional",
				),
                         	"deliveryTime" => array(
                                        "name" => "deliveryTime",
                                        "feed_name" => "deliveryTime",
					"format" => "optional",
				),
                         	"deliveryComment" => array(
                                        "name" => "deliveryComment",
                                        "feed_name" => "deliveryComment",
					"format" => "optional",
				),
                         	"maxOrderProcessingTime" => array(
                                        "name" => "maxOrderProcessingTime",
                                        "feed_name" => "maxOrderProcessingTime",
					"format" => "optional",
				),
                         	"freeReturnDays" => array(
                                        "name" => "freeReturnDays",
                                        "feed_name" => "freeReturnDays",
					"format" => "optional",
				),
                         	"checkout" => array(
                                        "name" => "checkout",
                                        "feed_name" => "checkout",
					"format" => "required",
				),
                         	"minimumPrice" => array(
                                        "name" => "minimumPrice",
                                        "feed_name" => "minimumPrice",
					"format" => "required",
				),
                         	"fullfillmentType" => array(
                                        "name" => "fulfillmentType",
                                        "feed_name" => "fulfillmentType",
					"format" => "required",
				),
                         	"checkoutLimitPerPeriod" => array(
                                        "name" => "checkoutLimitPerPeriod",
                                        "feed_name" => "checkoutLimitPerPeriod",
					"format" => "required",
				),
                   		"quantityPerOrder" => array(
                                        "name" => "quantityPerOrder",
                                        "feed_name" => "quantityPerOrder",
					"format" => "optional",
				),
                   		"twoManHandlingFee" => array(
                                        "name" => "twoManHandlingFee",
                                        "feed_name" => "twoManHandlingFee",
					"format" => "optional",
				),
                   		"disposalFee" => array(
                                        "name" => "disposalFee",
                                        "feed_name" => "disposalFee",
					"format" => "optional",
				),
                   		"eans" => array(
                                        "name" => "eans",
                                        "feed_name" => "eans",
					"format" => "required",
				),
                   		"packagingUnit" => array(
                                        "name" => "packagingUnit",
                                        "feed_name" => "packagingUnit",
					"format" => "optional",
				),
                   		"deliveryCost_ups" => array(
                                        "name" => "deliveryCost_ups",
                                        "feed_name" => "deliveryCost_ups",
					"format" => "optional",
				),
                   		"deliveryCost_fedex" => array(
                                        "name" => "deliveryCost_fedex",
                                        "feed_name" => "deliveryCost_fedex",
					"format" => "optional",
				),
                   		"deliveryCost_deutsche_post" => array(
                                        "name" => "deliveryCost_deutsche_post",
                                        "feed_name" => "deliveryCost_deutsche_post",
					"format" => "optional",
				),
                   		"deliveryCost_dhl" => array(
                                        "name" => "deliveryCost_dhl",
                                        "feed_name" => "deliveryCost_dhl",
					"format" => "optional",
				),
                   		"deliveryCost_dhl_go_green" => array(
                                        "name" => "deliveryCost_dhl_go_green",
                                        "feed_name" => "deliveryCost_dhl_go_green",
					"format" => "optional",
				),
                   		"deliveryCost_download" => array(
                                        "name" => "deliveryCost_download",
                                        "feed_name" => "deliveryCost_download",
					"format" => "optional",
				),
                   		"deliveryCost_dpd" => array(
                                        "name" => "deliveryCost_dpd",
                                        "feed_name" => "deliveryCost_dpd",
					"format" => "optional",
				),
                   		"deliveryCost_german_express_logistics" => array(
                                        "name" => "deliveryCost_german_express_logistics",
                                        "feed_name" => "deliveryCost_german_express_logistics",
					"format" => "optional",
				),
                   		"deliveryCost_gls" => array(
                                        "name" => "deliveryCost_gls",
                                        "feed_name" => "deliveryCost_gls",
					"format" => "optional",
				),
                   		"deliveryCost_gls_think_green" => array(
                                        "name" => "deliveryCost_gls_think_green",
                                        "feed_name" => "deliveryCost_gls_think_green",
					"format" => "optional",
				),
                   		"deliveryCost_hermes" => array(
                                        "name" => "deliveryCost_hermes",
                                        "feed_name" => "deliveryCost_hermes",
					"format" => "optional",
				),
                   		"deliveryCost_pick_point" => array(
                                        "name" => "deliveryCost_pick_points",
                                        "feed_name" => "deliveryCost_pick_point",
					"format" => "optional",
				),
                   		"deliveryCost_spedition" => array(
                                        "name" => "deliveryCost_spedition",
                                        "feed_name" => "deliveryCost_spedition",
					"format" => "optional",
				),
                   		"deliveryCost_tnt" => array(
                                        "name" => "deliveryCost_tnt",
                                        "feed_name" => "deliveryCost_tnt",
					"format" => "optional",
				),
                   		"deliveryCost_trans_o_flex" => array(
                                        "name" => "deliveryCost_trans_o_flex",
                                        "feed_name" => "deliveryCost_trand_o_flex",
					"format" => "optional",
				),
                   		"paymentCosts_credit_card" => array(
                                        "name" => "paymentCosts_credit_card",
                                        "feed_name" => "paymentCosts_credit_card",
					"format" => "optional",
				),
                   		"paymentCosts_cash_in_advance" => array(
                                        "name" => "paymentCosts_cash_in_advance",
                                        "feed_name" => "paymentCosts_cash_in_advance",
					"format" => "optional",
				),
                   		"paymentCosts_cash_on_delivery" => array(
                                        "name" => "paymentCosts_cash_on_delivery",
                                        "feed_name" => "paymentCosts_cash_on_delivery",
					"format" => "optional",
				),
                   		"paymentCosts_paypal" => array(
                                        "name" => "paymentCosts_paypal",
                                        "feed_name" => "paymentCosts_paypal",
					"format" => "optional",
				),
                   		"paymentCosts_giropay" => array(
                                        "name" => "paymentCosts_giropay",
                                        "feed_name" => "paymentCosts_giropay",
					"format" => "optional",
				),
                   		"paymentCosts_direct_debit" => array(
                                        "name" => "paymentCosts_direct_debit",
                                        "feed_name" => "paymentCosts_direct_debit",
					"format" => "optional",
				),
                   		"paymentCosts_google_checkout" => array(
                                        "name" => "paymentCosts_google_checkout",
                                        "feed_name" => "paymentCosts_google_checkout",
					"format" => "optional",
				),
                   		"paymentCosts_invoice" => array(
                                        "name" => "paymentCosts_invoice",
                                        "feed_name" => "paymentCosts_invoice",
					"format" => "optional",
				),
                   		"paymentCosts_postal_order" => array(
                                        "name" => "paymentCosts_postal_order",
                                        "feed_name" => "paymentCosts_postal_order",
					"format" => "optional",
				),
                   		"paymentCosts_paysafecard" => array(
                                        "name" => "paymentCosts_paysafecard",
                                        "feed_name" => "paymentCosts_paysafecard",
					"format" => "optional",
				),
                   		"paymentCosts_sofortueberweisung" => array(
                                        "name" => "paymentCosts_sofortueberweisung",
                                        "feed_name" => "paymentCosts_sofortueberweisung",
					"format" => "optional",
				),
                   		"paymentCosts_amazon_payment" => array(
                                        "name" => "paymentCosts_amazon_payment",
                                        "feed_name" => "paymentCosts_amazon_payment",
					"format" => "optional",
				),
                   		"paymentCosts_electronical_payment_standard" => array(
                                        "name" => "paymentCosts_electronical_payment_standard",
                                        "feed_name" => "paymentCosts_electronical_payment_standard",
					"format" => "optional",
				),
                   		"paymentCosts_ecotax" => array(
                                        "name" => "paymentCosts_ecotax",
                                        "feed_name" => "paymentCosts_ecotax",
					"format" => "optional",
				),
                   		"used" => array(
                                        "name" => "used",
                                        "feed_name" => "used",
					"format" => "optional",
				),
                   		"download" => array(
                                        "name" => "download",
                                        "feed_name" => "download",
					"format" => "optional",
				),
                   		"replica" => array(
                                        "name" => "replica",
                                        "feed_name" => "replica",
					"format" => "optional",
				),
                   		"size" => array(
                                        "name" => "size",
                                        "feed_name" => "size",
					"format" => "optional",
				),
                   		"colour" => array(
                                        "name" => "colour",
                                        "feed_name" => "colour",
					"format" => "optional",
				),
                   		"gender" => array(
                                        "name" => "gender",
                                        "feed_name" => "gender",
					"format" => "optional",
				),
                   		"material" => array(
                                        "name" => "material",
                                        "feed_name" => "material",
					"format" => "optional",
				),
                   		"oens" => array(
                                        "name" => "oens",
                                        "feed_name" => "oens",
					"format" => "optional",
				),
                   		"kbas" => array(
                                        "name" => "kbas",
                                        "feed_name" => "kbas",
					"format" => "optional",
				),
                   		"diopter" => array(
                                        "name" => "diopter",
                                        "feed_name" => "diopter",
					"format" => "optional",
				),
                   		"baseCurve" => array(
                                        "name" => "baseCurve",
                                        "feed_name" => "baseCurve",
					"format" => "optional",
				),
                   		"diameter" => array(
                                        "name" => "diameter",
                                        "feed_name" => "diameter",
					"format" => "optional",
				),
                   		"cylinder" => array(
                                        "name" => "cylinder",
                                        "feed_name" => "cylinder",
					"format" => "optional",
				),
                   		"axis" => array(
                                        "name" => "axis",
                                        "feed_name" => "axis",
					"format" => "optional",
				),
                   		"addition" => array(
                                        "name" => "addition",
                                        "feed_name" => "addition",
					"format" => "optional",
				),
            			"pzns" => array(
                                        "name" => "pzns",
                                        "feed_name" => "pzns",
					"format" => "optional",
				),
            			"quantity" => array(
                                        "name" => "quantity",
                                        "feed_name" => "quantity",
					"format" => "optional",
				),
            			"fuelEfficiency" => array(
                                        "name" => "fuelEfficiency",
                                        "feed_name" => "fuelEfficiency",
					"format" => "optional",
				),
            			"wetGrip" => array(
                                        "name" => "wetGrip",
                                        "feed_name" => "wetGrip",
					"format" => "optional",
				),
            			"externalRollingNoise" => array(
                                        "name" => "externalRollingNoise",
                                        "feed_name" => "externalRollingNoise",
					"format" => "optional",
				),
            			"rollingNoiseClass" => array(
                                        "name" => "rollingNoiseClass",
                                        "feed_name" => "rollingNoiseClass",
					"format" => "optional",
				),
            			"alcoholicContent" => array(
                                        "name" => "alcoholicContent",
                                        "feed_name" => "alcoholicConent",
					"format" => "optional",
				),
            			"allergenInformation" => array(
                                        "name" => "allergenInformation",
                                        "feed_name" => "allergenInformation",
					"format" => "optional",
				),
            			"countryOfOrigin" => array(
                                        "name" => "countryOfOrigin",
                                        "feed_name" => "countryOfOrigin",
					"format" => "optional",
				),
            			"bottler" => array(
                                        "name" => "bottler",
                                        "feed_name" => "bottler",
					"format" => "optional",
				),
            			"importer" => array(
                                        "name" => "importer",
                                        "feed_name" => "importer",
					"format" => "optional",
				),
			),
		);
		return $idealo;
	}
}
?>
