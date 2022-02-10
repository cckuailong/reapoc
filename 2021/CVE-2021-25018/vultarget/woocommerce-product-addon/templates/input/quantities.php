<?php
/**
 * input quantities render
 *
 **/

ppom_direct_access_not_allowed();

// ppom_pa($args);

global $product;
$options = isset($args['options']) ? $args['options'] : null;
$dataname = isset($args['id']) ? $args['id'] : null;

$include_productprice = '';
if( ppom_is_field_has_price( $args ) ) {
    $include_productprice = 'on';
}

// ppom_pa($args);
$default_price  = !empty($args['default_price']) ? $args['default_price'] : 0;
// If price matrix attached then disable default_price
$pricematrix_field = ppom_has_field_by_type(ppom_get_product_id($product), 'pricematrix');
if ( $pricematrix_field ) {
    $default_price = 0;
}
// var_dump($default_price);

echo '<input type="hidden" name="ppom_quantities_option_price" id="ppom_quantities_option_price">';

if (isset($args['view_control']) && $args['view_control'] == 'horizontal') { ?>
<div class="nm-horizontal-layout">
    <table class="table table-bordered table-hover">
       <thead> 
        <tr>
           <th><?php _e('Options', "ppom");?></th>
        <?php foreach($options as $opt){ ?>
            <th>
                <label class="quantities-lable"> <?php echo stripslashes(trim($opt['option'])); ?>
                
                <?php 
                $the_price  = isset($opt['price']) && $opt['price'] != '' ? $opt['price'] : $default_price;
                if( $the_price )
                    echo ' <span class="ppom-quantity-price-wrap">'.wc_price($the_price).'</span>';
                ?>
                </label>
            </th>
        <?php } ?>
        </tr>
        </thead>
        
        <tr>
            <th><?php _e('Quantity', "ppom");?></th>
        <?php foreach($options as $opt){ ?>
            <td>
                <?php
                
                    $the_price      = isset($opt['price']) && $opt['price'] != '' ? $opt['price'] : $default_price;
                    // Price need to filter for currency switcher here not in wc_price
                    $the_price = apply_filters('ppom_option_price', $the_price);
                    $usebaseprice   = isset($opt['price']) ? 'no' : 'yes';
                    
                    $min = (isset($opt['min']) ? $opt['min'] : 0 );
                    $max = (isset($opt['max']) ? $opt['max'] : 10000 );
                    $required = ($args['required'] == 'on' ? 'required' : '');
                    $label  = $opt['option'];
                    $name   = $args['name'].'['.htmlentities($label).']';
                    
                    $option_id      = $opt['id'];
                    $dom_id         = apply_filters('ppom_dom_option_id', $option_id, $args);
                    
                    // Default value
                    $selected_val = '';
                    if($default_value){
                        foreach($default_value as $k => $v) {
                            if( $k == $label ) {
                                $selected_val = $v;
                            }
                        }
                    }
                    
                    $input_html  = '<input style="width:50px;text-align:center" '.esc_attr($required);
                    $input_html .=' min="'.esc_attr($min).'" max="'.esc_attr($max).'" ';
                    $input_html .= 'id="'.esc_attr($dom_id).'" data-data_name="'.esc_attr($dataname).'" ';
                    $input_html .= 'data-optionid="'.esc_attr($option_id).'" ';
                    $input_html .= 'data-label="'.esc_attr($label).'" ';
                    $input_html .= 'data-includeprice="'.esc_attr($include_productprice).'" ';
                    $input_html .= 'data-min="'.esc_attr($args['min_qty']).'" ';
                    $input_html .= 'data-max="'.esc_attr($args['max_qty']).'" ';
                    $input_html .= 'name="'.htmlentities($name).'" type="number" class="ppom-quantity" ';
                    $input_html .= 'data-usebase_price="'.esc_attr($usebaseprice).'" ';
                    $input_html .= 'value="'.esc_attr($selected_val).'" data-price="'.esc_attr($the_price).'">';          
                    
                    echo $input_html;
                ?>
            </td>
        <?php } ?>
        </tr>
    </table>
</div>
<?php } elseif (isset($args['view_control']) && $args['view_control'] == 'grid') { ?>

    <!-- Enable Grid View -->
    <div class="form-row  ppom-quantity-box-wrapper">
        
        <?php 
            foreach($options as $opt){ 

                $min    = (isset($opt['min']) ? $opt['min'] : 0 );
                $max    = (isset($opt['max']) ? $opt['max'] : 10000 );
                
                $the_price      = isset($opt['price']) ? $opt['price'] : $default_price;
                $usebaseprice   = isset($opt['price']) ? 'no' : 'yes';
            
                $label  = $opt['option'];
                $name   = $args['name'].'['.htmlentities($label).']';

                $required = ($args['required'] == 'on' ? 'required' : '');
                
                $option_id      = $opt['id'];
                $dom_id         = apply_filters('ppom_dom_option_id', $option_id, $args);
                
                // Default value
                $selected_val = '';
                if($default_value){
                    foreach($default_value as $k => $v) {
                        if( $k == $label ) {
                            $selected_val = $v;
                        }
                    }
                }
        ?>
        <div class="col-md-3 ppom-quantity-box-cols text-center">
            <div class="ppom-quantity-label">

                <label class="quantities-lable"> <?php echo stripslashes(trim($opt['option'])); ?>

                </label>
               
                <?php 
                $the_price  = isset($opt['price']) && $opt['price'] != '' ? $opt['price'] : $default_price;
                if( $the_price )
                    echo ' <span class="ppom-quantity-price-wrap">'.wc_price($the_price).'</span>';
                ?>
            </div>
            
            <?php
            // Price need to filter for currency switcher here not in wc_price
            $the_price = apply_filters('ppom_option_price', $the_price);
            ?>

            <span class="ppom-quantity-qty-section">
                <input min="<?php echo esc_attr($min); ?>" 
                max="<?php echo esc_attr($max); ?>"
                data-data_name="<?php echo esc_attr($dataname); ?>" 
                id="<?php echo esc_attr($dom_id); ?>" 
                data-optionid="<?php echo esc_attr($option_id); ?>" 
                data-min="<?php echo esc_attr($args['min_qty']); ?>" 
                data-max="<?php echo esc_attr($args['max_qty']); ?>" 
                data-label="<?php echo esc_attr($label); ?>"
                data-includeprice="<?php echo esc_attr($include_productprice); ?>" 
                name="<?php echo htmlentities($name); ?>" type="number" class="ppom-quantity" 
                data-usebase_price="<?php echo esc_attr($usebaseprice); ?>" 
                value="<?php echo esc_attr($selected_val); ?>" placeholder="0" 
                data-price="<?php echo esc_attr($the_price); ?>" <?php echo esc_attr($required); ?> style="width: 50%;">
            </span>
        </div>
        <?php } ?>
    </div>

<?php } else { ?>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th><?php _e('Options', "ppom");?></th>
                <th><?php _e('Quantity', "ppom");?></th>
            </tr>
        </thead>
        <?php foreach($options as $opt){ ?>
            <tr>
                    <th>
                        <label class="quantities-lable"> <?php echo stripslashes(trim($opt['option'])); ?>
                        
                        <?php 
                        $the_price  = isset($opt['price']) && $opt['price'] != '' ? $opt['price'] : $default_price;
                        if( $the_price )
                            echo ' <span class="ppom-quantity-price-wrap">'.wc_price($the_price).'</span>';
                        ?>

                        </label>
                    </th>
                    <td>
                        <?php
                            $min    = (isset($opt['min']) ? $opt['min'] : 0 );
                            $max    = (isset($opt['max']) ? $opt['max'] : 10000 );
                            
                            // Price need to filter for currency switcher here not in wc_price
                            $the_price = apply_filters('ppom_option_price', $the_price);
                            
                            $usebaseprice   = isset($opt['price']) ? 'no' : 'yes';
                        
                            $label  = $opt['option'];
                            $name   = $args['name'].'['.htmlentities($label).']';
                            $option_id      = $opt['id'];
                            $dom_id         = apply_filters('ppom_dom_option_id', $option_id, $args);
                            
                            // Default value
                            $selected_val = '';
                            if($default_value){
                                foreach($default_value as $k => $v) {
                                    if( $k == $label ) {
                                        $selected_val = $v;
                                    }
                                }
                            }
                            
                            
                            $required = ($args['required'] == 'on' ? 'required' : '');
                            $input_html  = '<input style="width:50px;text-align:center" '.esc_attr($required);
                            $input_html .=' min="'.esc_attr($min).'" max="'.esc_attr($max).'" ';
                            $input_html .= 'id="'.esc_attr($dom_id).'" data-data_name="'.esc_attr($dataname).'" ';
                            $input_html .= 'data-optionid="'.esc_attr($option_id).'" ';
                            $input_html .= 'data-min="'.esc_attr($args['min_qty']).'" ';
                            $input_html .= 'data-max="'.esc_attr($args['max_qty']).'" ';
                            $input_html .= 'data-label="'.esc_attr($label).'" ';
                            $input_html .= 'data-includeprice="'.esc_attr($include_productprice).'" ';
                            $input_html .= 'name="'.htmlentities($name).'" type="number" class="ppom-quantity" ';
                            $input_html .= 'data-usebase_price="'.esc_attr($usebaseprice).'" ';
                            $input_html .= 'value="'.esc_attr($selected_val).'" placeholder="0" data-price="'.esc_attr($the_price).'">';
                            
                            echo $input_html;
                        ?>
                    </td>
            </tr>
        <?php } ?>
    </table>

<?php } ?>

<div id="display-total-price">
    <span style="display:none;font-weight:700" class="ppom-total-option-price">
        <?php echo __("Options Total: ", "ppom"); printf(__(get_woocommerce_price_format(), "ppom"), get_woocommerce_currency_symbol(), '<span class="ppom-price"></span>');?>
    </span><br>
    <span style="display:none;font-weight:700" class="ppom-total-price">
        <?php echo __("Product Total: ", "ppom"); printf(__(get_woocommerce_price_format(), "ppom"), get_woocommerce_currency_symbol(), '<span class="ppom-price"></span>');?>
    </span>
    <span style="display:none;font-weight:700" class="ppom-grand-total-price">
    <hr style="margin: 0">
        <?php echo __("Grand Total: ", "ppom"); printf(__(get_woocommerce_price_format(), "ppom"), get_woocommerce_currency_symbol(), '<span class="ppom-price"></span>');?>
    </span>
</div>