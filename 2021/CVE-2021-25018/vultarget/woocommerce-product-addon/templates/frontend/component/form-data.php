<?php 
/**
 * Contains form data HTML
 * 
 * @template version 1.0
 */

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$ppom_id = is_array($ppom_id) ? implode(',', $ppom_id) : $ppom_id;

// Cart key if editing
$cart_key = isset($_GET['_cart_key']) ? sanitize_key($_GET['_cart_key']) : '';

?>

<input type="hidden" id="ppom_product_price" value="<?php echo esc_attr( $product->get_price() ); ?>">

<!-- it is setting price to be used for dymanic prices in script.js -->
<input type="hidden" name="ppom[fields][id]" id="ppom_productmeta_id" value="<?php echo esc_attr($ppom_id); ?>">

<input type="hidden" name="ppom_product_id" id="ppom_product_id" value="<?php echo esc_attr( $product_id ); ?>">

<!-- Manage conditional hidden fields to skip validation -->
<input type="hidden" name="ppom[conditionally_hidden]" id="conditionally_hidden">

<!-- Option price hidden input: ppom-price.js -->
<input type="hidden" name="ppom[ppom_option_price]" id="ppom_option_price">

<input type="hidden" name="ppom_cart_key" value="<?php echo esc_attr($cart_key); ?>">

<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" />

<div id="ppom-price-cloner-wrapper">
	<span id="ppom-price-cloner">
		<?php 
		printf(__(get_woocommerce_price_format(), "ppom"), get_woocommerce_currency_symbol(), '<span class="ppom-price"></span>');
		?>
	</span>
</div>