<?php
/**
* Quantities Input Horizontal Layout Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/component/quantities/horizontal-layout.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$fm = new PPOM_InputManager($field_meta, 'quantities');

$min_qty   = $fm->get_meta_value('min_qty');
$max_qty   = $fm->get_meta_value('max_qty');
$default_price   = $fm->get_meta_value('default_price', 0);
$manage_stock = $fm->get_meta_value('manage_stock', 0);
$manage_stock = $manage_stock === 'on' ? true : false;
$unlink_qty	= $fm->get_meta_value('unlink_order_qty', '');

$include_productprice = '';
if( ppom_is_field_has_price( $field_meta ) ) {
    $include_productprice = 'on';
}

// If price matrix attached then disable default_price
$pricematrix_field = ppom_has_field_by_type(ppom_get_product_id($product), 'pricematrix');
if ( $pricematrix_field ) {
    $default_price = 0;
}

$options = ppom_convert_options_to_key_val($fm->options(), $field_meta, $product);
// ppom_pa($options);

?>
<div class="nm-horizontal-layout">
    <table class="table table-bordered table-hover ppom-style">
       <thead> 
            <tr>
                <th><?php _e('Options', "ppom");?></th>
                <?php foreach($options as $opt){ 
                    $the_price  = isset($opt['price']) && $opt['price'] != '' ? $opt['price'] : $default_price;
                ?>
                <th>
                    <label class="quantities-lable"> <?php echo stripslashes(trim($opt['label'])); ?></label>
                </th>
                <?php } ?>
            </tr>
        </thead>
        
        <tr>
            <th><?php _e('Quantity', "ppom");?></th>
            <?php foreach($options as $opt){ 

    			// Price need to filter for currency switcher here not in wc_price
                $the_price = isset($opt['price']) && $opt['price'] != '' ? $opt['price'] : $default_price;
    			$usebaseprice = isset($opt['price']) ? 'no' : 'yes';
    			
    			$min    = (!empty($opt['min']) ? $opt['min'] : 0 );
                $max    = (!empty($opt['max']) ? intval($opt['max']) : 10000 );
                $stock	= $opt['stock'] !== '' ? intval($opt['stock']) : '';
        		$max	= $max > $stock && $manage_stock ? $stock : $max;
    			$required = ($fm->required() == 'on' ? 'required' : '');
    			$label    = $opt['raw'];
    			$name	  = $fm->form_name().'['.htmlentities($label).']';
    			$in_stock = $manage_stock ? sprintf( __( '%s in stock', 'woocommerce' ), $stock) : '';
    			$out_of_stock = $manage_stock && $stock < 1 ? true : false;
    			$disabled = $out_of_stock ? 'disabled="disabled"' : '';
    			$min = $out_of_stock ? 0 : $min;
    			
    			$option_id = $opt['id'];
    			$dom_id    = apply_filters('ppom_dom_option_id', $option_id, $field_meta);
    			
    			$default    = (!empty($opt['default']) ? $opt['default'] : $min );
    			// Default value
    			$selected_val = $default;
    			if($default_value){
        			foreach($default_value as $k => $v) {
        				if( $k == $label ) {
        					$selected_val = $v;
        				}
        			}
    			}
            ?>
            <td>
				<input 
					type="number" 
					name="<?php echo esc_attr( htmlentities($name) ); ?>" 
					id="<?php echo esc_attr($dom_id); ?>" 
					data-data_name="<?php echo esc_attr( $fm->data_name() ); ?>"
					class="ppom-quantity"
					min="<?php echo esc_attr($min); ?>"
					max="<?php echo esc_attr($max); ?>"
					data-optionid="<?php echo esc_attr($option_id); ?>" 
					data-price="<?php echo esc_attr($the_price); ?>" 
					data-min="<?php echo esc_attr($min_qty); ?>" 
					data-max="<?php echo esc_attr($max_qty); ?>" 
					data-label="<?php echo esc_attr($label); ?>" 
					data-includeprice="<?php echo esc_attr($include_productprice); ?>"
					data-unlink_qty="<?php echo esc_attr($unlink_qty); ?>" 
					data-usebase_price="<?php echo esc_attr($usebaseprice); ?>" 
					value="<?php echo esc_attr($selected_val); ?>" 
					style="width:50px;text-align:center" 
					<?php echo esc_attr($required); ?>
				>
				
				<?php
                printf(__('<span class="ppom-instock">%s</span>','ppom'),$in_stock);
                ?>
			</td>
            <?php } ?>
        </tr>
    </table>
</div>