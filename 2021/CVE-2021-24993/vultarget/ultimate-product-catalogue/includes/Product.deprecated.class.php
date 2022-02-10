<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'UPCP_Product' ) ) {

	/**
 	* Class to handle a references to pre-rebuild UPCP products
 	*
 	* @since 5.0.0
 	*/
    class UPCP_Product {

    	// a reference to the corresponding new product class object
    	public $product;
				
		function __construct($params = array()) {

			if ( isset( $params['ID'] ) ) {

				$args = array(
					'post_type'		=> EWD_UPCP_PRODUCT_POST_TYPE,
					'meta_query' 	=> array( 
						array(
							'key'		=> 'old_product_id',
							'value'		=> $params['ID']
						)
					)
				);
			}
			elseif ( isset( $params['Name'] ) ) {

				$post = get_page_by_title( $params['Name'], OBJECT, EWD_UPCP_PRODUCT_POST_TYPE );
				
				$args = array(
					'post_type'		=> EWD_UPCP_PRODUCT_POST_TYPE,
					'p'				=> $post->ID
				);
			}
			elseif ( get_query_var('single_product') != '' or $_GET['SingleProduct'] != '' ) {	
				
				if ( get_query_var('single_product') != '' ) {
					
					$args = array(
						'post_type'		=> EWD_UPCP_PRODUCT_POST_TYPE,
						'name' 			=> trim( get_query_var( 'single_product' ), '/? ' )
					);
				}
				else {

					$args = array(
						'post_type'		=> EWD_UPCP_PRODUCT_POST_TYPE,
						'p' 			=> intval( $_GET['SingleProduct'] )
					);
				}
			}

			if ( empty( $args ) ) { return false; }

			$query = new WP_Query( $args );

			$product_post = $query->posts[0];

			if ( $product_post ) {

				$product = new ewdupcpProduct();

				$product->load_post( $product_post );

				$this->product = $product;
			}
    	}
		
		function Get_Product_Name_For_ID( $id = null ) {

			if ( ! $id ) { return false; }

			$args = array(
				'post_type'		=> EWD_UPCP_PRODUCT_POST_TYPE,
				'p' 			=> intval( $id )
			);

			$query = new WP_Query( $args );

			$product_post = $query->posts[0];

			return $product_post->post_title;
		}

		function Get_Field_Value_For_ID( $field, $id ) {
			
			if ( ! $id or ! $field ) { return false; }

			$args = array(
				'post_type'		=> EWD_UPCP_PRODUCT_POST_TYPE,
				'p' 			=> intval( $id )
			);

			$query = new WP_Query( $args );

			$product_post = $query->posts[0];

			if ( ! $product_post ) { return false; }

			$product = new ewdupcpProduct();

			$product->load_post( $product_post );

			return ! empty( $product->$field ) ? $product->$field : false;
		}
				
		function Get_Item_ID() {

			return $this->product->id;
		}
				
		function Get_Product_Name() {

			return $this->product->name;
		}
				
		function Get_Field_Value( $field ) {

			return $this->product->$field;
		}

    	function Get_Custom_Fields() {

    		return $this->product->custom_fields;
    	}

    	function Get_Custom_Field_By_ID( $field_id ) {
    		
    		return $this->product->custom_fields[ $field_id ];
    	}

    	function Get_Product_Tag_String() {
    		
    		$tag_string = '';

    		foreach ( $this->product->tags as $tag ) {

    			$tag_string .= $tag->name . ',';
    		} 

    		return trim( $tag_string, ',' );
    	}

    	function Get_Permalink( $link_base = false ) {
    		
    		return get_permalink( $this->product->id );
    	}

    	function Get_Product_Price( $Return_Type = 'Int', $Sale = 'Implied' ) {

    		if ( $Sale == 'Implied' ) {

    			$price = $this->product->current_price;
    		}
    		elseif ( $Sale == 'Sale' ) {

    			$price = $this->product->sale_price;
    		}
    		else {

    			$price = $this->product->regular_price;
    		}

    		if ( $return_type == 'Int' ) { return $price; }

    		return $this->get_display_price();
    	}
	}
}

function UPCP_Get_All_Products() {
	
	$args = array(
		'post_type'			=> EWD_UPCP_PRODUCT_POST_TYPE,
		'posts_per_page' 	=> -1
	);

	$query = new WP_Query( $args );

	$products = array();

	foreach( $query->get_posts as $product_post ) {

		$product = new ewdupcpProduct();

		$product->load_post( $product_post );

		$products[] = $product;
	}

	return $products;
}

function UPCP_Get_All_Categories() {
	
	$args = array(
		'taxonomy'		=> EWD_UPCP_PRODUCT_CATEGORY_TAXONOMY,
		'hide_empty'	=> false
	);

	return get_terms( $args );
}

function UPCP_Get_Catalogues() {
	
	$args = array(
		'post_type'			=> EWD_UPCP_CATALOG_POST_TYPE,
		'posts_per_page' 	=> -1
	);

	$query = new WP_Query( $args );

	return $query->get_posts;
}	
?>