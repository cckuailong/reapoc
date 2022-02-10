<?php
/**
 * PPOM Deprecated functions here
 * 
 * */
 
// check if product has meta Returns Meta ID if true otherwise null
// Deprecated, using PPOM_Meta::is_exist function
function ppom_has_product_meta( $product_id ) {
	
	$return = true;
	
	$ppom		= new PPOM_Meta( $product_id );
		
	if( ! $ppom->is_exists ) return false;
	
	return $return;
}