<?php
/**
 * Settings for Fruugo.nl feeds
 */
class WooSEA_fruugonl {
	public $fruugonl;

        public static function get_channel_attributes() {

                $sitename = get_option('blogname');

        	$fruugonl = array(
			"Feed fields" => array(
				"Product Id" => array(
					"name" => "ProductId",
					"feed_name" => "ProductId",
					"format" => "required",
					"woo_suggest" => "id",
				),
				"Sku Id" => array(
					"name" => "SkuId",
					"feed_name" => "SkuId",
					"format" => "required",
					"woo_suggest" => "sku",
				),
				"GTINs" => array(
					"name" => "EAN",
					"feed_name" => "EAN",
					"format" => "required",
				),
				"Brand" => array(
					"name" => "Brand",
					"feed_name" => "Brand",
					"format" => "required",
				),
				"Category" => array(
					"name" => "Category",
					"feed_name" => "Category",
					"format" => "required",
					"woo_suggest" => "category",
				),
				"Image URL 1" => array(
					"name" => "Imageurl1",
					"feed_name" => "Imageurl1",
					"format" => "required",
					"woo_suggest" => "image",
				),
				"Stock Status" => array(
					"name" => "StockStatus",
					"feed_name" => "StockStatus",
					"format" => "required",
					"woo_suggest" => "availability",
				),
				"Quantity in Stock" => array(
					"name" => "StockQuantity",
					"feed_name" => "StockQuantity",
					"format" => "required",
				),
				"Title" => array(
					"name" => "Title",
					"feed_name" => "Title",
					"format" => "required",
					"woo_suggest" => "title",
				),
				"Description" => array(
					"name" => "Description",
					"feed_name" => "Description",
					"format" => "required",
					"woo_suggest" => "description",
				),
				"Normal Price With VAT" => array(
					"name" => "NormalPriceWithVAT",
					"feed_name" => "NormalPriceWithVAT",
					"format" => "required",
					"woo_suggest" => "price",
				),
				"Normal Price Without VAT" => array(
					"name" => "NormalPriceWithoutVAT",
					"feed_name" => "NormalPriceWithoutVAT",
					"format" => "optional",
				),
				"VAT Rate" => array(
					"name" => "VATRate",
					"feed_name" => "VATRate",
					"format" => "required",
				),
				"Image URL 2" => array(
					"name" => "Imageurl2",
					"feed_name" => "Imageurl2",
					"format" => "optional",
				),
				"Image URL 3" => array(
					"name" => "Imageurl3",
					"feed_name" => "Imageurl3",
					"format" => "optional",
				),
				"Image URL 4" => array(
					"name" => "Imageurl4",
					"feed_name" => "Imageurl4",
					"format" => "optional",
				),
				"Image URL 5" => array(
					"name" => "Imageurl5",
					"feed_name" => "Imageurl5",
					"format" => "optional",
				),
				"Language" => array(
					"name" => "Language",
					"feed_name" => "Language",
					"format" => "optional",
				),
				"Attribute Size" => array(
					"name" => "AttributeSize",
					"feed_name" => "AttributeSize",
					"format" => "optional",
				),
				"Attribute Color" => array(
					"name" => "AttributeColor",
					"feed_name" => "AttributeColor",
					"format" => "optional",
				),
				"Currency" => array(
					"name" => "Currency",
					"feed_name" => "Currency",
					"format" => "optional",
				),
				"Discount Price Without VAT" => array(
					"name" => "DiscountPriceWithoutVAT",
					"feed_name" => "DiscountPriceWithoutVAT",
					"format" => "optional",
				),
				"Discount Price With VAT" => array(
					"name" => "DiscountPriceWithVAT",
					"feed_name" => "DiscountPriceWithVAT",
					"format" => "optional",
				),
				"ISBN" => array(
					"name" => "ISBN",
					"feed_name" => "ISBN",
					"format" => "optional",
				),
				"Manufacturer" => array(
					"name" => "Manufacturer",
					"feed_name" => "Manufacturer",
					"format" => "optional",
				),
				"Restock Date" => array(
					"name" => "RestockDate",
					"feed_name" => "RestockDate",
					"format" => "optional",
				),
				"Lead Time" => array(
					"name" => "LeadTime",
					"feed_name" => "LeadTime",
					"format" => "optional",
				),
				"Package Weight" => array(
					"name" => "PackageWeight",
					"feed_name" => "PackageWeight",
					"format" => "optional",
				),
				"Attribute 1" => array(
					"name" => "Attribute1",
					"feed_name" => "Attribute1",
					"format" => "optional",
				),
				"Attribute 2" => array(
					"name" => "Attribute2",
					"feed_name" => "Attribute2",
					"format" => "optional",
				),
				"Attribute 3" => array(
					"name" => "Attribute3",
					"feed_name" => "Attribute3",
					"format" => "optional",
				),
				"Attribute 4" => array(
					"name" => "Attribute4",
					"feed_name" => "Attribute4",
					"format" => "optional",
				),
				"Attribute 5" => array(
					"name" => "Attribute5",
					"feed_name" => "Attribute5",
					"format" => "optional",
				),
				"Attribute 6" => array(
					"name" => "Attribute6",
					"feed_name" => "Attribute6",
					"format" => "optional",
				),
				"Attribute 7" => array(
					"name" => "Attribute7",
					"feed_name" => "Attribute7",
					"format" => "optional",
				),
				"Attribute 8" => array(
					"name" => "Attribute8",
					"feed_name" => "Attribute8",
					"format" => "optional",
				),
				"Attribute 9" => array(
					"name" => "Attribute9",
					"feed_name" => "Attribute9",
					"format" => "optional",
				),
				"Attribute 10" => array(
					"name" => "Attribute10",
					"feed_name" => "Attribute10",
					"format" => "optional",
				),
				"Country" => array(
					"name" => "Country",
					"feed_name" => "Country",
					"format" => "optional",
				),
				"Discount Price Start Date" => array(
					"name" => "DiscountPriceStartDate",
					"feed_name" => "DiscountPriceStartDate",
					"format" => "optional",
				),
				"Discount Price End Date" => array(
					"name" => "DiscountPriceEndDate",
					"feed_name" => "DiscountPriceEndDate",
					"format" => "optional",
				),
			),
		);
		return $fruugonl;
	}
}
?>
