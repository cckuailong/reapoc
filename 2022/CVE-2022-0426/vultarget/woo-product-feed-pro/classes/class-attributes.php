<?php
/**
* This class is called to: retrieve product attributes 
* for populating the rules dropdowns and draggable boxes
*/

class WooSEA_Attributes {

public $attributes;
public $dropdown;
public $standard_attributes;

public function get_channel_countries(){
	$channel_countries = array();
	$channel_configs = get_option ('channel_statics');
	
	foreach ($channel_configs as $key=>$val){
		if (($key != "All countries") && ($key != "Custom Feed")){
			array_push ($channel_countries, $key);
		}
	}
	return $channel_countries;
}

public function get_channels($country){
	$channels = array();
	$channel_configs = get_option ('channel_statics');

	// Lets get the generic channels
	foreach ($channel_configs as $key=>$val){
		if($key == "Custom Feed" || $key == "All countries"){
			$channels = array_merge ($channels, $val);
		}
	}

	// Than get the relevant country channels
	foreach ($channel_configs as $key=>$val){
		if(preg_match("/-$country/i", $key)){
			$channels = array_merge($channels, $val);
		 } elseif ($country == "$key"){
			$channels = array_merge($channels, $val);
		}
	}
	return $channels;
}

private function get_dynamic_attributes(){
	global $wpdb;
	$list = array();

        $no_taxonomies = array("portfolio_category","portfolio_skills","portfolio_tags","nav_menu","post_format","slide-page","element_category","template_category","portfolio_category","portfolio_skills","portfolio_tags","faq_category","slide-page","category","post_tag","nav_menu","link_category","post_format","product_type","product_visibility","product_cat","product_shipping_class","product_tag");
     	$taxonomies = get_taxonomies();
     	$diff_taxonomies = array_diff($taxonomies, $no_taxonomies);

    	# get custom taxonomy values for a product
    	foreach($diff_taxonomies as $tax_diff){

		$taxonomy_details = get_taxonomy( $tax_diff );
		foreach($taxonomy_details as $kk => $vv){

			if($kk == "name"){
				$pa_short = $vv;
			}
			if($kk == "labels"){
				foreach($vv as $kw => $kv){
					if($kw == "singular_name"){
						$attr_name = $pa_short;
						$attr_name_clean = ucfirst($kv);
					}
				}
			}
		}
               	$list["$attr_name"] = $attr_name_clean;
	}
	return $list;
}

private function get_custom_attributes() {
	global $wpdb;
     	$list = array();
     	
      	//$sql = "SELECT meta.meta_id, meta.meta_key as name, meta.meta_value as type FROM " . $wpdb->prefix . "postmeta" . " AS meta, " . $wpdb->prefix . "posts" . " AS posts WHERE meta.post_id = posts.id AND posts.post_type LIKE '%product%' AND meta.meta_key NOT LIKE 'pyre%' AND meta.meta_key NOT LIKE 'sbg_%' AND meta.meta_key NOT LIKE 'rp_%' GROUP BY meta.meta_key ORDER BY meta.meta_key ASC;";
	//$data = $wpdb->get_results($sql);

	if ( ! function_exists( 'woosea_get_meta_keys_for_post_type' ) ) :

    		function woosea_get_meta_keys_for_post_type( $post_type, $sample_size = 5 ) {
        		$meta_keys = array();
        		$posts     = get_posts( array( 'post_type' => $post_type, 'limit' => $sample_size ) );

        		foreach ( $posts as $post ) {
            			$post_meta_keys = get_post_custom_keys( $post->ID );
            			$meta_keys      = array_merge( $meta_keys, $post_meta_keys );
        		}

        		// Use array_unique to remove duplicate meta_keys that we received from all posts
        		// Use array_values to reset the index of the array
        		return array_values( array_unique( $meta_keys ) );
    		}
	endif;

	$post_type = "product";
	$data = woosea_get_meta_keys_for_post_type($post_type);	
	
      	if (count($data)) {
     		foreach ($data as $key => $value) {
			if (!preg_match("/_product_attributes/i",$value)){
				$value_display = str_replace("_", " ",$value);
                    		$list["custom_attributes_" . $value] = ucfirst($value_display);
            		} else {
				$sql = "SELECT meta.meta_id, meta.meta_key as name, meta.meta_value as type FROM " . $wpdb->prefix . "postmeta" . " AS meta, " . $wpdb->prefix . "posts" . " AS posts WHERE meta.post_id = posts.id AND posts.post_type LIKE '%product%' AND meta.meta_key='_product_attributes';";
				$data = $wpdb->get_results($sql);
      				if (count($data)) {
     					foreach ($data as $key => $value) {
						$product_attr = unserialize($value->type);
						if(!empty($product_attr)){
							foreach ($product_attr as $key => $arr_value) {
								$value_display = str_replace("_", " ",$arr_value['name']);
               	     						$list["custom_attributes_" . $key] = ucfirst($value_display);
							}
						}
					}
				}
			}
             	}
              	return $list;
     	}
     	return false;
}

public function get_mapping_attributes_dropdown() {
	$sitename = get_option('blogname');
	
	$mapping_attributes = array(
      		"categories" => "Category",
      		"title" => "Product name",
	);

	/**
	 * Create dropdown with main attributes
	 */
	$dropdown = "<option></option>";
	$dropdown .= "<optgroup label='Main attributes'><strong>Main attributes</strong>";
		
	foreach ($mapping_attributes as $key=>$value) {
		if ($key == "categories"){
			$dropdown .= "<option value='$key' selected>" . $value . "</option>";
		} else {
			$dropdown .= "<option value='$key'>" . $value . "</option>";
		}
	}
		
	$dropdown .="</optgroup>";

	$other_attributes = array(
		"all_products" => "Map all products",
	);

	$dropdown .= "<optgroup label='Other options'><strong>Other options</strong>";
	
	foreach ($other_attributes as $key=>$value) {
		$dropdown .= "<option value='$key'>" . $value . "</option>";
	}
	


	return $dropdown;
}

	public function get_special_attributes_dropdown(){
		/**
     		 * Create dropdown with product attributes
     		 */
		$dropdown = "<option></option>";

                $custom_attributes = $this->get_custom_attributes();

		if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
			$custom_attributes['custom_attributes__aioseop_title'] = "All in one seo pack title";
			$custom_attributes['custom_attributes__aioseop_description'] = "All in one seo pack description";
		}

                if($custom_attributes){
                        $dropdown .= "<optgroup label='Custom attributes'><strong>Custom attributes</strong>";

                        foreach ($custom_attributes as $key => $value) {
                                if (strpos($value, 0, 1) !== "_") {
					// Exclude total sales
					if($key != "custom_attributes_total_sales"){
						$value = str_replace("attribute","",$value);
                         	               	$dropdown .= "<option value='$key'>" . ucfirst($value) . "</option>";
                               		}
				 }
                        }

                        $dropdown .="</optgroup>";
                }
		return $dropdown;
	}

	public function get_special_attributes_clean(){
                $custom_attributes = $this->get_custom_attributes();
		return $custom_attributes;
	}

        public function get_product_attributes_dropdown() {
		$sitename = get_option('blogname');

        	$attributes = array(
   			"id" => "Product Id",
            		"sku" => "SKU", 
			"sku_id" => "SKU_ID (Facebook)",
			"parent_sku" => "SKU parent variable product",
			"sku_item_group_id" => "SKU_ITEM_GROUP_ID (Facebook)",
			"wc_post_id_product_id" => "Wc_post_id_product_id (Facebook)",
			"title" => "Product name",
			"title_hyphen" => "Product name hyphen",
			"mother_title" => "Product name parent product",
			"mother_title_hyphen" => "Product name parent product hyphen",
			"title_lc" => "Product name lowercase",
			"title_lcw" => "Product name uppercase first characters",
			"description" => "Product description",
            		"short_description" => "Product short description",
            		"raw_description" => "Unfiltered product description",
            		"raw_short_description" => "Unfiltered product short description",
            		"mother_description" => "Product description parent product",
            		"mother_short_description" => "Product short description parent product",
			"price" => "Price",
            		"regular_price" => "Regular price",
			"sale_price" => "Sale price",
            		"net_price" => "Price excl. VAT",
            		"net_regular_price" => "Regular price excl. VAT",
			"net_sale_price" => "Sale price excl. VAT",
			"price_forced" => "Price incl. VAT front end",
			"regular_price_forced" => "Regular price incl. VAT front end",
			"sale_price_forced" => "Sale price incl. VAT front end",
	 		"sale_price_start_date" => "Sale start date",
            		"sale_price_end_date" => "Sale end date",
            		"sale_price_effective_date" => "Sale price effective date",
			"rounded_price" => "Price rounded",
			"rounded_regular_price" => "Regular price rounded",
			"rounded_sale_price" => "Sale price rounded",
			"system_price" => "System price",
			"system_net_price" => "System price excl. VAT",
			"system_net_sale_price" => "System sale price excl. VAT",
			"system_net_regular_price" => "System regular price excl. VAT",
			"system_regular_price" => "System regular price",
			"system_sale_price" => "System sale price",
			"vivino_price" => "Pinterest / TikTok / Vivino price",
			"vivino_sale_price" => "Pinterest / TikTok / Vivino sale price",
			"vivino_regular_price" => "Pinterest / TikTok / Vivino regular price",
			"non_geo_wcml_price" => "Non GEO WCML price",
			"mm_min_price" => "Mix & Match minimum price",
			"mm_min_regular_price" => "Mix & Match minimum regular price",
			"mm_max_price" => "Mix & Match maximum price",
			"mm_max_regular_price" => "Mix & Match maximum regular price",
			"discount_percentage" => "Discount percentage",
			"link" => "Link",
			"link_no_tracking" => "Link without parameters",
			"variable_link" => "Product variable link",
			"add_to_cart_link" => "Add to cart link",
			"product_creation_date" => "Product creation date",
			"days_back_created" => "Product days back created",
            		"currency" => "Currency",
			"categories" => "Category",
			"category_link" => "Category link",
			"category_path" => "Category path",
			"category_path_short" => "Category path short",
			"category_path_skroutz" => "Category path Skroutz",
			"one_category" => "Yoast / Rankmath primary category",
			"nr_variations" => "Number of variations",
			"nr_variations_stock" => "Number of variations on stock",
			"yoast_gtin8" => "Yoast WooCommerce GTIN8",
			"yoast_gtin12" => "Yoast WooCommerce GTIN12",
			"yoast_gtin13" => "Yoast WooCommerce GTIN13",
			"yoast_gtin14" => "Yoast WooCommerce GTIN14",
			"yoast_isbn" => "Yoast WooCommerce ISBN",
			"yoast_mpn" => "Yoast WooCommerce MPN",
			"condition" => "Condition",
			"purchase_note" => "Purchase note",
			"availability" => "Availability",
			"region_id" => "Region Id",
			"stock_status" => "Stock Status WooCommerce",
            		"quantity" => "Quantity [Stock]",
			"virtual" => "Virtual",
			"downloadable" => "Downloadable",
			"product_type" => "Product Type",
			"content_type" => "Content Type",
                        "exclude_from_catalog" => "Excluded from catalog",
                        "exclude_from_search" => "Excluded from search",
                        "exclude_from_all" => "Excluded from all (hidden)",
			"total_product_orders" => "Total product orders",
			"tax_status" => "Tax status",
			"tax_class" => "Tax class",
                        "featured" => "Featured",
			"item_group_id" => "Item group ID",
			"weight" => "Weight",
            		"width" => "Width",
            		"height" => "Height",
            		"length" => "Length",
			"shipping" => "Shipping",
         		"shipping_price" => "Shipping cost",   		
			"lowest_shipping_costs" => "Lowest shipping costs",
			"shipping_label" => "Shipping class slug",
         		"shipping_label_name" => "Shipping class name",
			"visibility" => "Visibility",
            		"rating_total" => "Total rating",
            		"rating_average" => "Average rating",
        	);

        	$images = array(
            		"image" => "Main image",
            		"image_all" => "Main image simple and variations",
            		"feature_image" => "Featured image",
            		"image_1" => "Additional image 1",
            		"image_2" => "Additional image 2",
            		"image_3" => "Additional image 3",
            		"image_4" => "Additional image 4",
            		"image_5" => "Additional image 5",
            		"image_6" => "Additional image 6",
            		"image_7" => "Additional image 7",
            		"image_8" => "Additional image 8",
            		"image_9" => "Additional image 9",
            		"image_10" => "Additional image 10",
			"all_images" => "All images (comma separated)",
			"all_gallery_images" => "All gallery images (comma separated)",
			"all_images_kogan" => "All images Kogan (pipe separated)",
        	);

		/**
		 * Create dropdown with main attributes
		 */
		$dropdown = "<option></option>";
		$dropdown .= "<optgroup label='Main attributes'><strong>Main attributes</strong>";
		
		foreach ($attributes as $key=>$value) {
			$dropdown .= "<option value='$key'>" . $value . "</option>";
		}
		
		$dropdown .="</optgroup>";

		/**
		 * Create dropdown with image attributes
		 */
		$dropdown .= "<optgroup label='Image attributes'><strong>Image attributes</strong>";
		
		foreach ($images as $key=>$value) {
			$dropdown .= "<option value='$key'>" . $value . "</option>";
		}
		$dropdown .="</optgroup>";

		/**
     		 * Create dropdown with dynamic attributes
     		 */
        	$dynamic_attributes = $this->get_dynamic_attributes();

		if($dynamic_attributes){
			$dropdown .= "<optgroup label='Dynamic attributes'><strong>Dynamic attributes</strong>";

            		foreach ($dynamic_attributes as $key => $value) {
                	//	if (strpos($value, 0, 1) !== "_") {
                			$dropdown .= "<option value='$key'>" . ucfirst($value) . "</option>";  
                	//	}
            		}

			$dropdown .="</optgroup>";
    		}

		$dropdown .= "<optgroup label='Google category taxonomy'><strong>Google category taxonomy</strong>";
		$dropdown .= "<option value='google_category'>Google category</option>";              
		$dropdown .="</optgroup>";

                /**
                 * Create dropdown with custom attributes
                 */
                $custom_attributes = $this->get_custom_attributes();

                if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
                        $custom_attributes['custom_attributes__aioseop_title'] = "All in one seo pack title";
                        $custom_attributes['custom_attributes__aioseop_description'] = "All in one seo pack description";
                }

                if($custom_attributes){
                        $dropdown .= "<optgroup label='Custom field attributes'><strong>Custom field attributes</strong>";

                        foreach ($custom_attributes as $key => $value) {
                             	if (!preg_match("/pyre|sbg|fusion/i",$value)){
					$value = ltrim($value);
					if (!empty($value)){
                                        	$dropdown .= "<option value='$key'>" . ucfirst($value) . "</option>";
                              		}
				}
                        }

                        $dropdown .="</optgroup>";
                }

		/**
		 * Add the product tag field
		 */
		$dropdown .= "<optgroup label='Other fields'><strong>Other fields</strong>";
		$dropdown .= "<option value='product_tag'>Product tags</option>";              
		$dropdown .= "<option value='product_tag_space'>Product tags space</option>";              
		$dropdown .= "<option value='menu_order'>Menu order</option>";
		$dropdown .= "<option value='reviews'>Reviews</option>";
		$dropdown .= "<option value='author'>Author</option>";
		$dropdown .= "</optgroup>";

                // Did the user checked extra attributes
                if(get_option( 'woosea_extra_attributes' )){
                        $extra_attributes = get_option( 'woosea_extra_attributes' );
	                if($extra_attributes){
				array_walk($extra_attributes, function(&$value, $key) { $value .= ' (Added Custom attribute)';});
			 	$dropdown .= "<optgroup label='Added Custom Attributes'><strong>Added Custom Attributes</strong>";

                 	       foreach ($extra_attributes as $key => $value) {
                        	//        if (strpos($value, 0, 1) !== "_") {
                               	        	$dropdown .= "<option value='$key'>" . ucfirst($value) . "</option>";
                               	//	}
                        	}
                        	$dropdown .="</optgroup>";
                	}
                }
		return $dropdown;
	}

        public function get_product_attributes() {
                $sitename = get_option('blogname');

                $attributes = array(
                        "id" => "Product Id",
                        "sku" => "SKU", 
			"sku_id" => "SKU_ID (Facebook)",
                        "parent_sku" => "SKU parent variable product",
			"sku_item_group_id" => "SKU_ITEM_GROUP_ID (Facebook)",
			"wc_post_id_product_id" => "Wc_post_id_product_id (Facebook)",
                        "title" => "Product name",
                        "title_hyphen" => "Product name hyphen",
                        "mother_title" => "Product name parent product",
                        "mother_title_hyphen" => "Product name parent product hyphen",
			"title_lc" => "Product name lowercase",
			"title_lcw" => "Product name uppercase first characters",
			"description" => "Product description",
                        "short_description" => "Product short description",
                     	"raw_description" => "Unfiltered product description",
                        "raw_short_description" => "Unfiltered product short description",
		     	"mother_description" => "Product description parent product",
                        "mother_short_description" => "Product short description parent product",
			"link" => "Link",
			"link_no_tracking" => "Link without parameters",
                        "variable_link" => "Product variable link",
                        "add_to_cart_link" => "Add to cart link",
			"image" => "Main image",
                        "image_all" => "Main image simple and variations",
			"feature_image" => "Feature image",
                        "product_type" => "Product Type",
                        "content_type" => "Content Type",
			"exclude_from_catalog" => "Excluded from catalog",
                        "exclude_from_search" => "Excluded from search",
                        "exclude_from_all" => "Excluded from all (hidden)",
                        "total_product_orders" => "Total product orders",
			"featured" => "Featured",
                        "tax_status" => "Tax status",
                        "tax_class" => "Tax class",
			"currency" => "Currency",
    			"categories" => "Category",
			"raw_categories" => "Category (not used for mapping)",
			"google_category" => "Google category (for rules and filters only)",
			"category_link" => "Category link",
			"category_path" => "Category path",
			"category_path_short" => "Category path short",
                        "category_path_skroutz" => "Category path Skroutz",
			"one_category" => "Yoast / Rankmath primary category",
			"nr_variations" => "Number of variations",
                        "nr_variations_stock" => "Number of variations on stock",
			"yoast_gtin8" => "Yoast WooCommerce GTIN8",
                        "yoast_gtin12" => "Yoast WooCommerce GTIN12",
                        "yoast_gtin13" => "Yoast WooCommerce GTIN13",
                        "yoast_gtin14" => "Yoast WooCommerce GTIN14",
                        "yoast_isbn" => "Yoast WooCommerce ISBN",
                        "yoast_mpn" => "Yoast WooCommerce MPN",
			"condition" => "Condition",
			"purchase_note" => "Purchase note",
			"availability" => "Availability",
			"region_id" => "Region Id",
			"stock_status" => "Stock Status WooCommerce",
			"quantity" => "Quantity [Stock]",
			"virtual" => "Virtual",
                        "downloadable" => "Downloadable",
			"price" => "Price",
                        "regular_price" => "Regular price",
                        "sale_price" => "Sale price",
                        "net_price" => "Price excl. VAT",
                        "net_regular_price" => "Regular price excl. VAT",
                        "net_sale_price" => "Sale price excl. VAT",
                        "price_forced" => "Price incl. VAT front end",
                        "regular_price_forced" => "Regular price incl. VAT front end",
                        "sale_price_forced" => "Sale price incl. VAT front end",
                        "sale_price_start_date" => "Sale start date",
                        "sale_price_end_date" => "Sale end date",
			"sale_price_effective_date" => "Sale price effective date",
                        "rounded_price" => "Price rounded",
                        "rounded_regular_price" => "Regular price rounded",
                        "rounded_sale_price" => "Sale price rounded",
		        "system_price" => "System price",
			"system_net_price" => "System price excl. VAT",
			"system_net_sale_price" => "System sale price excl. VAT",
                        "system_net_regular_price" => "System regular price excl. VAT",
                        "system_regular_price" => "System regular price",
                        "system_sale_price" => "System sale price",	
			"vivino_price" => "Pinterest / TikTok / Vivino price",
			"vivino_sale_price" => "Pinterest / TikTok / Vivino sale price",
                        "vivino_regular_price" => "Pinterest / TikTok / Vivino regular price",
			"non_geo_wcml_price" => "Non GEO WCML price",
			"mm_min_price" => "Mix & Match minimum price",
                        "mm_min_regular_price" => "Mix & Match minimum regular price",
                        "mm_max_price" => "Mix & Match maximum price",
                        "mm_max_regular_price" => "Mix & Match maximum regular price",
			"discount_percentage" => "Discount percentage",
			"item_group_id" => "Item group ID",
                        "weight" => "Weight",
                        "width" => "Width",
                        "height" => "Height",
                        "length" => "Length",
                        "shipping" => "Shipping",
			"shipping_price" => "Shipping cost",
			"lowest_shipping_costs" => "Lowest shipping costs",
			"shipping_label" => "Shipping class slug",
                        "shipping_label_name" => "Shipping class name",
			"visibility" => "Visibility",
                        "rating_total" => "Total rating",
                        "rating_average" => "Average rating",
			"amount_sales" => "Amount of sales",
                        "product_creation_date" => "Product creation date",
                        "days_back_created" => "Product days back created",
                );

        	$images = array(
            		"image" => "Main image",
                        "image_all" => "Main image simple and variations",
            		"feature_image" => "Featured image",
            		"image_1" => "Additional image 1",
            		"image_2" => "Additional image 2",
            		"image_3" => "Additional image 3",
            		"image_4" => "Additional image 4",
            		"image_5" => "Additional image 5",
            		"image_6" => "Additional image 6",
            		"image_7" => "Additional image 7",
            		"image_8" => "Additional image 8",
            		"image_9" => "Additional image 9",
            		"image_10" => "Additional image 10",
                        "all_images" => "All images (comma separated)",
                        "all_gallery_images" => "All gallery images (comma separated)",
			"all_images_kogan" => "All images Kogan (pipe separated)",
        	);

		$attributes = array_merge($attributes, $images);

        	$static = array(
			"installment" => "Installment",
            		"static_value" => "Static value",
			"calculated" => "Plugin calculation",
			"product_tag" => "Product tags",
			"product_tag_space" => "Product tags space",
			"product_detail" => "Product detail",
			"product_highlight" => "Product highlight",
			"menu_order" => "Menu order",
			"reviews" => "Reviews",
			"author" => "Author",
        	);

		$attributes = array_merge($attributes, $static);

		if(is_array($this->get_dynamic_attributes())){
        		$dynamic_attributes = $this->get_dynamic_attributes();
			array_walk($dynamic_attributes, function(&$value, $key) { $value .= ' (Dynamic attribute)';});
			$attributes = array_merge($attributes, $dynamic_attributes);
		}

                if(is_array($this->get_custom_attributes())){
			$custom_attributes = $this->get_custom_attributes();

                	if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
                        	$custom_attributes['custom_attributes__aioseop_title'] = "All in one seo pack title";
                        	$custom_attributes['custom_attributes__aioseop_description'] = "All in one seo pack description";
                	}

			array_walk($custom_attributes, function(&$value, $key) { $value .= ' (Custom attribute)';});
			$attributes = array_merge($attributes, $custom_attributes);
                }

		$attributes = array_merge($attributes, $static);

		// Did the user checked extra attributes
		if(get_option( 'woosea_extra_attributes' )){
			$extra_attributes = get_option( 'woosea_extra_attributes' );
			array_walk($extra_attributes, function(&$value, $key) { $value .= ' (Added Custom attribute)';});
			$attributes = array_merge($attributes, $extra_attributes);
		}
		return $attributes;
	}

        public static function get_standard_attributes($project) {
		$sitename = get_option('blogname');

        	$standard_attributes = array(
   			"id" => "Product Id",
      		      	"title" => "Product name",
      		      	"categories" => "Category",
        	);

		if ($project['taxonomy'] == 'google_shopping'){
			$standard_attributes["google_product_category"] = "Google product category";
		} elseif ($project['taxonomy'] != 'none'){
			$standard_attributes["$project[name]_product_category"] = "$project[name] category";
		}

		return $standard_attributes;
	}
}
