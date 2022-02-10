<?php
/**
 * Settings for Shareasale feeds
 */
class WooSEA_shareasale {
	public $shareasale;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$shareasale = array(
			"Feed fields" => array(
				"SKU" => array(
					"name" => "SKU",
					"feed_name" => "SKU",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Name" => array(
					"name" => "Name",
					"feed_name" => "Name",
					"format" => "required",
					"woo_suggest" => "title",
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
                      		"RetailPrice" => array(
                                        "name" => "RetailPrice",
                                        "feed_name" => "RetailPrice",
                                        "format" => "required",
                                ),
        			"FullImage" => array(
					"name" => "FullImage",
					"feed_name" => "FullImage",
					"format" => "required",
					"woo_suggest" => "image",
				),
        			"ThumbnailImage" => array(
					"name" => "ThumbnailImage",
					"feed_name" => "ThumbnailImage",
					"format" => "required",
				),
        			"Commission" => array(
					"name" => "Commission",
					"feed_name" => "Commission",
					"format" => "required",
				),
        			"Category" => array(
					"name" => "Category",
					"feed_name" => "Category",
					"format" => "required",
					"woo_suggest" => "categories",
				),
        			"Subcategory" => array(
					"name" => "Subcategory",
					"feed_name" => "Subcategory",
					"format" => "required",
				),
        			"Description" => array(
					"name" => "Description",
					"feed_name" => "Description",
					"format" => "required",
					"woo_suggest" => "description",
				),
        			"SearchTerms" => array(
					"name" => "SearchTerms",
					"feed_name" => "SearchTerms",
					"format" => "required",
				),
 				"Status" => array(
					"name" => "Status",
					"feed_name" => "Status",
					"format" => "required",
				),
 				"MerchantID" => array(
					"name" => "MerchantID",
					"feed_name" => "MerchantID",
					"format" => "required",
				),
 				"Custom1" => array(
					"name" => "Custom1",
					"feed_name" => "Custom1",
					"format" => "required",
				),
 				"Custom2" => array(
					"name" => "Custom2",
					"feed_name" => "Custom2",
					"format" => "required",
				),
 				"Custom3" => array(
					"name" => "Custom3",
					"feed_name" => "Custom3",
					"format" => "required",
				),
 				"Custom4" => array(
					"name" => "Custom4",
					"feed_name" => "Custom4",
					"format" => "required",
				),
 				"Custom5" => array(
					"name" => "Custom5",
					"feed_name" => "Custom5",
					"format" => "required",
				),
 				"Manufacturer" => array(
					"name" => "Manufacturer",
					"feed_name" => "Manufacturer",
					"format" => "required",
				),
 				"PartNumber" => array(
					"name" => "PartNumber",
					"feed_name" => "PartNumber",
					"format" => "required",
				),
 				"MerchantCategory" => array(
					"name" => "MerchantCategory",
					"feed_name" => "MerchantCategory",
					"format" => "required",
				),
 				"MerchantSubcategory" => array(
					"name" => "MerchantSubcategory",
					"feed_name" => "MerchantSubcategory",
					"format" => "required",
				),
 				"ShortDescription" => array(
					"name" => "ShortDescription",
					"feed_name" => "ShortDescription",
					"format" => "required",
				),
 				"ISBN" => array(
					"name" => "ISBN",
					"feed_name" => "ISBN",
					"format" => "required",
				),
 				"UPC" => array(
					"name" => "UPC",
					"feed_name" => "UPC",
					"format" => "required",
				),
 				"CrossSell" => array(
					"name" => "CrossSell",
					"feed_name" => "CrossSell",
					"format" => "required",
				),
 				"MerchantGroup" => array(
					"name" => "MerchantGroup",
					"feed_name" => "MerchantGroup",
					"format" => "required",
				),
 				"MerchantSubGroup" => array(
					"name" => "MerchantSubGroup",
					"feed_name" => "MerchantSubGroup",
					"format" => "required",
				),
 				"CompatibleWith" => array(
					"name" => "CompatibleWith",
					"feed_name" => "CompatibleWith",
					"format" => "required",
				),
				"CompareTo" => array(
					"name" => "CompareTo",
					"feed_name" => "CompareTo",
					"format" => "required",
				),
				"QuantityDiscount" => array(
					"name" => "QuantityDiscount",
					"feed_name" => "QuantityDiscount",
					"format" => "required",
				),
				"Bestseller" => array(
					"name" => "Bestseller",
					"feed_name" => "Bestseller",
					"format" => "required",
				),
				"AddToCartURL" => array(
					"name" => "AddToCartURL",
					"feed_name" => "AddToCartURL",
					"format" => "required",
				),
				"ReviewRSSURL" => array(
					"name" => "ReviewRSSURL",
					"feed_name" => "ReviewRSSURL",
					"format" => "required",
				),
				"Option1" => array(
					"name" => "Option1",
					"feed_name" => "Option1",
					"format" => "required",
				),
				"Option2" => array(
					"name" => "Option2",
					"feed_name" => "Option2",
					"format" => "required",
				),
				"Option3" => array(
					"name" => "Option3",
					"feed_name" => "Option3",
					"format" => "required",
				),
				"Option4" => array(
					"name" => "Option4",
					"feed_name" => "Option4",
					"format" => "required",
				),
				"Option5" => array(
					"name" => "Option5",
					"feed_name" => "Option5",
					"format" => "required",
				),
				"customCommissions" => array(
					"name" => "customCommissions",
					"feed_name" => "customCommissions",
					"format" => "required",
				),
				"customCommissionIsFlatRate" => array(
					"name" => "customCommissionIsFlatRate",
					"feed_name" => "customCommissionIsFlatRate",
					"format" => "required",
				),
				"customCommissionNewCustomerMultiplier" => array(
					"name" => "customCommissionNewCustomerMultiplier",
					"feed_name" => "customCommissionNewCustomerMultiplier",
					"format" => "required",
				),
				"mobileURL" => array(
					"name" => "mobileURL",
					"feed_name" => "mobileURL",
					"format" => "required",
				),
				"mobileImage" => array(
					"name" => "mobileImage",
					"feed_name" => "mobileImage",
					"format" => "required",
				),
				"mobileThumbnail" => array(
					"name" => "mobileThumbnail",
					"feed_name" => "mobileThumbnail",
					"format" => "required",
				),
			),
		);
		return $shareasale;
	}
}
?>
